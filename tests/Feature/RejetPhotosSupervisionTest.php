<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * 1. Rejet (coordinateur ou admin) → notification du facilitateur (API + web).
 * 2. Photos + GPS envoyés par l'appli → visibles dans le détail coordinateur
 *    (y compris les ménages d'une fiche localité fusionnée).
 * 3. Plusieurs photos par ménage.
 * 4. L'admin vérifie l'espace d'un coordinateur et valide à sa place.
 * Transaction annulée à la fin ; les fichiers photo créés sont supprimés.
 */
class RejetPhotosSupervisionTest extends TestCase
{
    use DatabaseTransactions;

    private int $maxPhotoId = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);
        $this->maxPhotoId = (int) DB::table('report_photos')->max('id');
    }

    protected function tearDown(): void
    {
        foreach (DB::table('report_photos')->where('id', '>', $this->maxPhotoId)->pluck('path') as $p) {
            @unlink(public_path('uploads/' . $p));
        }
        parent::tearDown();
    }

    private function makeUser(string $roleLike, ?int $ongId): User
    {
        $user = User::create([
            'username' => 'test_' . $roleLike . '_' . uniqid(),
            'email' => uniqid('test_') . '@example.test',
            'password' => bcrypt('secret123'),
            'photo' => 'default.png',
            'operateur_id' => $ongId,
        ]);
        $roleId = DB::table('roles')->where('name', 'like', "%$roleLike%")->value('id');
        DB::table('role_user')->insert(['user_id' => $user->id, 'role_id' => $roleId]);
        return $user->fresh();
    }

    private function image(int $w = 40): string
    {
        $im = imagecreatetruecolor($w, 30);
        ob_start();
        imagejpeg($im);
        imagedestroy($im);
        return base64_encode(ob_get_clean());
    }

    /** ONG, coordinateur, facilitateur, localité, projet */
    private function contexte(): array
    {
        $ong = (int) DB::table('operateurs')->orderBy('id')->value('id');
        $coord = $this->makeUser('coordinat', $ong);
        $facil = $this->makeUser('facil', $ong);
        $loc = DB::table('localites')->whereNotNull('commune_id')->first();
        $projet = (int) DB::table('projets')->value('id');
        return [$ong, $coord, $facil, $loc, $projet];
    }

    private function base($loc, $projet, $ong, array $extra = []): array
    {
        return array_merge([
            'localite_id' => $loc->id, 'commune_id' => $loc->commune_id, 'moughata_id' => $loc->moughata_id,
            'wilaya_id' => $loc->wilaya_id, 'projet_id' => $projet, 'operateur_id' => $ong,
        ], $extra);
    }

    public function test_rejet_du_coordinateur_notifie_le_facilitateur()
    {
        [$ong, $coord, $facil, $loc, $projet] = $this->contexte();

        Sanctum::actingAs($facil);
        $this->postJson('/api/localite_reports', $this->base($loc, $projet, $ong, [
            'mobile_ref' => 'etat_localite_test_1', 'operation_id' => 2, 'nbr_h_c' => 5, 'date_op' => '2026-11-06',
        ]))->assertOk();
        $reportId = DB::table('localite_operations')->where('mobile_ref', 'etat_localite_test_1')->value('id');

        $this->actingAs($coord, 'admin')->post(route('coordinateur.validation.store'), [
            'type' => 'localite', 'action' => 'rejete', 'ids' => [$reportId], 'remarque' => 'Population incohérente',
        ])->assertRedirect();

        $n = DB::table('user_notifications')->where('user_id', $facil->id)->first();
        $this->assertNotNull($n, 'notification créée');
        $this->assertSame('etat_localite_test_1', $n->mobile_ref, 'lien vers la fiche du téléphone');
        $this->assertStringContainsString('Population incohérente', $n->message);
        $this->assertStringContainsString('coordinateur', $n->message);

        // L'appli du facilitateur la reçoit, puis la marque lue.
        Sanctum::actingAs($facil);
        $this->getJson('/api/notifications')->assertOk()
            ->assertJsonPath('non_lues', 1)
            ->assertJsonPath('notifications.0.mobile_ref', 'etat_localite_test_1');
        $this->postJson('/api/notifications/lues')->assertOk();
        $this->getJson('/api/notifications')->assertJsonPath('non_lues', 0);

        // Web : cloche + page des notifications
        $this->actingAs($facil, 'admin')->get(route('admin.notifications'))
            ->assertOk()->assertSee('Population incohérente');
    }

    public function test_rejet_admin_notifie_le_facilitateur()
    {
        [$ong, , $facil, $loc, $projet] = $this->contexte();
        Sanctum::actingAs($facil);
        $this->postJson('/api/infrastructure_reports', $this->base($loc, $projet, $ong, [
            'mobile_ref' => 'infra_test_1', 'operation_id' => 3, 'nom_infrastructure' => 'Ecole test ' . uniqid(),
            'type_infrastructure' => 'Ecole', 'date_op' => '2026-11-06',
        ]))->assertOk();
        $id = DB::table('infrastructure_operations')->where('mobile_ref', 'infra_test_1')->value('id');

        $admin = $this->makeUser('administrat', null);
        $this->actingAs($admin, 'admin')->postJson(route('admin.reports.infrastructures.bulkUpdate'), [
            'ids' => [$id], 'status' => 0, 'remarque' => 'Photo floue',
        ])->assertOk();

        $n = DB::table('user_notifications')->where('user_id', $facil->id)->first();
        $this->assertNotNull($n);
        $this->assertStringContainsString("l'administrateur", $n->message);
        $this->assertStringContainsString('Photo floue', $n->message);
    }

    public function test_photos_et_gps_visibles_par_le_coordinateur()
    {
        [$ong, $coord, $facil, $loc, $projet] = $this->contexte();
        Sanctum::actingAs($facil);

        // Rapport localité avec GPS + 2 photos
        $this->postJson('/api/localite_reports', $this->base($loc, $projet, $ong, [
            'mobile_ref' => 'etat_localite_test_2', 'operation_id' => 2, 'date_op' => '2026-11-06',
            'latlng' => '18.0735,-15.9582', 'photos_base64' => [$this->image(), $this->image(50)],
        ]))->assertOk();
        $rid = DB::table('localite_operations')->where('mobile_ref', 'etat_localite_test_2')->value('id');
        $this->assertSame('18.0735,-15.9582', DB::table('localite_operations')->where('id', $rid)->value('latlng'));

        $res = $this->actingAs($coord, 'admin')->getJson(route('coordinateur.validation.detail', ['localite', $rid]))->assertOk();
        $this->assertCount(2, $res->json('photos'));
        $this->assertSame(18.0735, $res->json('gps.0.lat'));
        $this->assertStringContainsString('google.com/maps', $res->json('gps.0.url'));
        foreach ($res->json('photos') as $p) {
            $this->assertFileExists(public_path(substr(parse_url($p['url'], PHP_URL_PATH), 1)));
        }

        // Ménages (plusieurs photos chacun) → fiche localité fusionnée avec
        // les photos et positions de chaque ménage.
        Sanctum::actingAs($facil);
        $this->postJson('/api/menage_reports', $this->base($loc, $projet, $ong, [
            'mobile_ref' => 'etat_menage_test_1', 'operation_id' => 2, 'nom_chef_menage' => 'Sidi',
            'latlng' => '18.1,-15.9', 'photos_base64' => [$this->image(), $this->image(), $this->image()],
        ]))->assertOk();
        $this->postJson('/api/menage_reports', $this->base($loc, $projet, $ong, [
            'mobile_ref' => 'etat_menage_test_2', 'operation_id' => 2, 'nom_chef_menage' => 'Ahmed',
            'latlng' => '18.2,-15.8', 'photos_base64' => [$this->image()],
        ]))->assertOk();
        $menageId = DB::table('menages')->where('mobile_ref', 'etat_menage_test_1')->value('id');
        $this->assertSame(3, DB::table('report_photos')->where('report_table', 'menages')->where('report_id', $menageId)->count());

        // Renvoyée avec 1 seule photo : remplace les 3 (anciens fichiers supprimés)
        $old = DB::table('report_photos')->where('report_table', 'menages')->where('report_id', $menageId)->pluck('path');
        $this->postJson('/api/menage_reports', $this->base($loc, $projet, $ong, [
            'mobile_ref' => 'etat_menage_test_1', 'operation_id' => 2, 'nom_chef_menage' => 'Sidi',
            'latlng' => '18.1,-15.9', 'photos_base64' => [$this->image()],
        ]))->assertOk();
        $this->assertSame(1, DB::table('report_photos')->where('report_table', 'menages')->where('report_id', $menageId)->count());
        foreach ($old as $p) {
            $this->assertFileDoesNotExist(public_path('uploads/' . $p));
        }

        $fiche = DB::table('localite_operations')
            ->where('mobile_ref', \App\Services\MenageAggregator::ref(2, $loc->id, $projet))->value('id');
        $res = $this->actingAs($coord, 'admin')->getJson(route('coordinateur.validation.detail', ['localite', $fiche]))->assertOk();
        $this->assertCount(2, $res->json('photos'), '1 photo par ménage');
        $this->assertSame(['Ménage Sidi', 'Ménage Ahmed'], array_column($res->json('gps'), 'label'));
    }

    public function test_coordinateur_supprime_une_selection_multiple()
    {
        [$ong, $coord, $facil, $loc, $projet] = $this->contexte();
        Sanctum::actingAs($facil);
        foreach ([1, 2, 3] as $i) {
            $this->postJson('/api/localite_reports', $this->base($loc, $projet, $ong, [
                'mobile_ref' => "etat_localite_supp_$i", 'operation_id' => 3, 'date_op' => '2026-11-06',
                'photos_base64' => [$this->image()],
            ]))->assertOk();
        }
        // Ménages → fiche localité fusionnée
        foreach ([1, 2] as $i) {
            $this->postJson('/api/menage_reports', $this->base($loc, $projet, $ong, [
                'mobile_ref' => "etat_menage_supp_$i", 'operation_id' => 2, 'photos_base64' => [$this->image()],
            ]))->assertOk();
        }
        $ids = DB::table('localite_operations')->where('mobile_ref', 'like', 'etat_localite_supp_%')->orderBy('id')->pluck('id')->all();
        $fiche = DB::table('localite_operations')->where('mobile_ref', \App\Services\MenageAggregator::ref(2, $loc->id, $projet))->value('id');
        $menageIds = DB::table('menages')->where('mobile_ref', 'like', 'etat_menage_supp_%')->pluck('id')->all();
        $photos = DB::table('report_photos')->where('id', '>', $this->maxPhotoId)->pluck('path');
        $this->assertCount(5, $photos);

        // Une ligne déjà validée par l'admin et une ligne d'une autre ONG : intouchables
        DB::table('localite_operations')->where('id', $ids[2])->update(['confirmed' => 1]);
        $autreOng = (int) DB::table('operateurs')->where('id', '!=', $ong)->value('id');
        $autre = DB::table('localite_operations')->insertGetId(
            collect((array) DB::table('localite_operations')->find($ids[0]))->except(['id', 'mobile_ref'])
                ->merge(['operateur_id' => $autreOng, 'user_id' => $this->makeUser('facil', $autreOng)->id])->all());

        $this->actingAs($coord, 'admin')->post(route('coordinateur.validation.delete'), [
            'type' => 'localite', 'ids' => [$ids[0], $ids[1], $ids[2], $fiche, $autre], 'remarque' => 'Doublons',
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertSame(0, DB::table('localite_operations')->whereIn('id', [$ids[0], $ids[1], $fiche])->count(), 'sélection supprimée');
        $this->assertSame(0, DB::table('menages')->whereIn('id', $menageIds)->count(), 'ménages de la fiche fusionnée supprimés');
        $this->assertTrue(DB::table('localite_operations')->where('id', $ids[2])->exists(), 'validée admin : gardée');
        $this->assertTrue(DB::table('localite_operations')->where('id', $autre)->exists(), 'autre ONG : gardée');

        // Photos des lignes supprimées effacées (fichiers + table), celle de la ligne gardée conservée
        $restantes = DB::table('report_photos')->where('id', '>', $this->maxPhotoId)->pluck('path');
        $this->assertCount(1, $restantes);
        foreach ($photos->diff($restantes) as $p) {
            $this->assertFileDoesNotExist(public_path('uploads/' . $p));
        }

        // Facilitateur prévenu : 2 fiches + la fiche fusionnée
        $notifs = DB::table('user_notifications')->where('user_id', $facil->id)->where('type', 'suppression')->get();
        $this->assertCount(3, $notifs);
        $this->assertStringContainsString('supprimé par le coordinateur', $notifs[0]->message);
        $this->assertStringContainsString('Doublons', $notifs[0]->message);

        // Un facilitateur ne peut pas utiliser cette action
        $this->actingAs($facil, 'admin')->post(route('coordinateur.validation.delete'), [
            'type' => 'localite', 'ids' => [$ids[2]],
        ])->assertForbidden();
    }

    public function test_admin_verifie_et_valide_a_la_place_du_coordinateur()
    {
        [$ong, $coord, $facil, $loc, $projet] = $this->contexte();
        Sanctum::actingAs($facil);
        $this->postJson('/api/localite_reports', $this->base($loc, $projet, $ong, [
            'mobile_ref' => 'etat_localite_test_3', 'operation_id' => 2, 'date_op' => '2026-11-06',
        ]))->assertOk();
        $rid = DB::table('localite_operations')->where('mobile_ref', 'etat_localite_test_3')->value('id');

        $admin = $this->makeUser('administrat', null);
        // Bloqué : le coordinateur n'a pas encore validé
        $this->actingAs($admin, 'admin')->postJson(route('admin.reports.localites.bulkUpdate'), ['ids' => [$rid], 'status' => 1])
            ->assertJsonPath('blocked', 1);

        // Liste des coordinateurs + son espace
        $this->actingAs($admin, 'admin')->get(route('supervision.coordinateurs.index'))
            ->assertOk()->assertSee($coord->username);
        $this->actingAs($admin, 'admin')->get(route('supervision.coordinateurs.validation', [$coord->id, 'statut' => 'attente']))
            ->assertOk()->assertSee('Supervision')->assertSee($loc->intitule);
        $this->actingAs($admin, 'admin')->getJson(route('supervision.coordinateurs.validation.detail', [$coord->id, 'localite', $rid]))
            ->assertOk()->assertJsonStructure(['fields', 'photos', 'gps']);

        // Validation à la place du coordinateur
        $this->actingAs($admin, 'admin')->post(route('supervision.coordinateurs.validation.store', $coord->id), [
            'type' => 'localite', 'action' => 'valide', 'ids' => [$rid],
        ])->assertRedirect();
        $r = DB::table('localite_operations')->where('id', $rid)->first();
        $this->assertSame('valide', $r->statut_coordinateur);
        $this->assertSame($admin->id, (int) $r->coordinateur_id);
        $this->assertStringContainsString('[Admin ' . $admin->username . ']', $r->coordinateur_remarque);

        // L'admin peut maintenant valider définitivement
        $this->actingAs($admin, 'admin')->postJson(route('admin.reports.localites.bulkUpdate'), ['ids' => [$rid], 'status' => 1])
            ->assertJsonPath('blocked', 0);
        $this->assertSame(1, (int) DB::table('localite_operations')->where('id', $rid)->value('confirmed'));

        // Ni un facilitateur ni un coordinateur n'ont accès à la supervision
        $this->actingAs($facil, 'admin')->get(route('supervision.coordinateurs.index'))->assertForbidden();
        $this->actingAs($coord, 'admin')->get(route('supervision.coordinateurs.index'))
            ->assertRedirect(route('coordinateur.dashboard'));
    }
}
