<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Rôle coordinateur d'ONG : accès limité à son espace, données de son ONG
 * uniquement, double validation coordinateur puis admin.
 * Tout se fait dans une transaction annulée (aucune donnée ne reste).
 */
class CoordinateurWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Les routes sont enregistrées sans préfixe de langue en test : on
        // neutralise la redirection automatique vers /fr.
        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);
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

    /** Deux rapports localité en attente, de deux ONG différentes. */
    private function twoPendingReports(): array
    {
        $tpl = DB::table('localite_operations')->first();
        $ongs = DB::table('operateurs')->orderBy('id')->limit(2)->pluck('id');
        $base = collect((array) $tpl)->except(['id', 'mobile_ref'])->all();

        $a = DB::table('localite_operations')->insertGetId(['operateur_id' => $ongs[0], 'confirmed' => 0, 'statut_coordinateur' => null] + $base);
        $b = DB::table('localite_operations')->insertGetId(['operateur_id' => $ongs[1], 'confirmed' => 0, 'statut_coordinateur' => null] + $base);
        return [$ongs[0], $ongs[1], $a, $b];
    }

    public function test_coordinateur_est_confine_a_son_espace()
    {
        [$ongA] = $this->twoPendingReports();
        $coord = $this->makeUser('coordinat', $ongA);

        $this->actingAs($coord, 'admin')
            ->get(route('admin.users.index'))
            ->assertRedirect(route('coordinateur.dashboard'));

        $this->actingAs($coord, 'admin')
            ->get(route('coordinateur.dashboard'))
            ->assertOk();

        // Un admin, lui, n'a pas accès à l'espace coordinateur.
        $admin = $this->makeUser('administrat', null);
        $this->actingAs($admin, 'admin')
            ->get(route('coordinateur.dashboard'))
            ->assertForbidden();
    }

    public function test_coordinateur_ne_voit_et_ne_valide_que_son_ong()
    {
        [$ongA, $ongB, $reportA, $reportB] = $this->twoPendingReports();
        $coord = $this->makeUser('coordinat', $ongA);

        $ids = $coord->restrictReportsToOng(DB::table('localite_operations'), 'localite_operations')
            ->whereIn('localite_operations.id', [$reportA, $reportB])
            ->pluck('localite_operations.id')->all();
        $this->assertEquals([$reportA], $ids);

        // Détail d'un rapport d'une autre ONG : introuvable.
        $this->actingAs($coord, 'admin')
            ->get(route('coordinateur.validation.detail', ['localite', $reportB]))
            ->assertNotFound();

        $this->actingAs($coord, 'admin')
            ->post(route('coordinateur.validation.store'), [
                'type' => 'localite', 'action' => 'valide', 'ids' => [$reportA, $reportB],
            ])->assertRedirect();

        $this->assertEquals('valide', DB::table('localite_operations')->where('id', $reportA)->value('statut_coordinateur'));
        $this->assertNull(DB::table('localite_operations')->where('id', $reportB)->value('statut_coordinateur'));
    }

    public function test_admin_valide_apres_le_coordinateur()
    {
        [$ongA, $ongB, $reportA, $reportB] = $this->twoPendingReports();
        $this->makeUser('coordinat', $ongA); // l'ONG A a un coordinateur, pas l'ONG B
        $admin = $this->makeUser('administrat', null);

        $res = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.reports.localites.bulkUpdate'), ['ids' => [$reportA, $reportB], 'status' => 1])
            ->assertOk()->json();

        $this->assertEquals(1, $res['blocked']);
        $this->assertEquals(0, DB::table('localite_operations')->where('id', $reportA)->value('confirmed'));
        $this->assertEquals(1, DB::table('localite_operations')->where('id', $reportB)->value('confirmed'));

        // Après validation coordinateur, l'admin peut valider.
        DB::table('localite_operations')->where('id', $reportA)->update(['statut_coordinateur' => 'valide']);
        $res = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.reports.localites.bulkUpdate'), ['ids' => [$reportA], 'status' => 1])
            ->json();
        $this->assertEquals(0, $res['blocked']);
        $this->assertEquals(1, DB::table('localite_operations')->where('id', $reportA)->value('confirmed'));
    }

    public function test_pages_admin_modifiees_toujours_accessibles()
    {
        $admin = $this->makeUser('administrat', null);
        $this->actingAs($admin, 'admin')->get(route('admin.users.create'))->assertOk()->assertSee('operateur_id', false);
        $this->actingAs($admin, 'admin')->get(route('admin.users.edit', $admin->id))->assertOk();
        $this->actingAs($admin, 'admin')->get(route('admin.reports.localites'))->assertOk();
        $this->actingAs($admin, 'admin')->get(route('admin.reports.infrastructures'))->assertOk();
        $this->actingAs($admin, 'admin')
            ->postJson(route('admin.reports.localites.data'), ['draw' => 1, 'start' => 0, 'length' => 5])
            ->assertOk()->assertJsonStructure(['data' => [['statut_coordinateur']]]);

        // Un coordinateur créé par l'admin sans ONG est refusé.
        $roleId = DB::table('roles')->where('name', 'Coordinateur')->value('id');
        $this->actingAs($admin, 'admin')
            ->post(route('admin.users.store'), [
                'name' => 'Coord', 'email' => uniqid('c_') . '@example.test',
                'password' => 'secret123', 'password_confirmation' => 'secret123',
                'role_id' => [$roleId],
            ])->assertSessionHasErrors('operateur_id');
    }

    public function test_app_mobile_ne_recoit_que_la_wilaya_du_compte_et_accepte_l_email()
    {
        [$ongA] = $this->twoPendingReports();
        $coord = $this->makeUser('coordinat', $ongA);
        $wilaya = DB::table('localites')->value('wilaya_id');
        $coord->wilayas()->sync([$wilaya]);

        // Connexion mobile avec l'email (et des espaces) au lieu du nom d'utilisateur.
        $login = $this->postJson('/api/login', ['username' => '  ' . $coord->email . ' ', 'password' => 'secret123'])
            ->assertOk();
        $token = $login->json('result.headers.authorization');
        $this->assertNotEmpty($token);

        $data = $this->withHeader('Authorization', 'Bearer ' . $token)->getJson('/api')->assertOk()->json('result');
        $this->assertEquals([$wilaya], array_column($data['wilayas'], 'id'));
        $this->assertEquals([$wilaya], array_values(array_unique(array_column($data['localites'], 'wilaya_id'))));
        $this->assertEquals(DB::table('localites')->where('wilaya_id', $wilaya)->count(), count($data['localites']));
        // Projets et opérateurs : liste complète, même pour un compte rattaché à une ONG.
        $this->assertCount(DB::table('operateurs')->count(), $data['operateurs']);
        $this->assertCount(DB::table('projets')->count(), $data['projets']);

        // Rapport sur une localité d'une autre wilaya : refusé.
        $autre = DB::table('localites')->where('wilaya_id', '!=', $wilaya)->value('id');
        $this->assertFalse($coord->fresh()->canWorkOnLocalite($autre));
    }

    public function test_coordinateur_sans_ong_voit_un_message_clair()
    {
        $coord = $this->makeUser('coordinat', null);
        $this->actingAs($coord, 'admin')
            ->get(route('coordinateur.dashboard'))
            ->assertOk()
            ->assertSee(__('coordinateur.no_ong_title'));
    }

    public function test_facilitateur_herite_de_la_wilaya_du_coordinateur()
    {
        [$ongA] = $this->twoPendingReports();
        $coord = $this->makeUser('coordinat', $ongA);
        $wilaya = DB::table('localites')->value('wilaya_id');
        $coord->wilayas()->sync([$wilaya]);
        $dans = DB::table('localites')->where('wilaya_id', $wilaya)->value('id');
        $hors = DB::table('localites')->where('wilaya_id', '!=', $wilaya)->value('id');

        $payload = fn ($loc) => [
            'name' => 'Facil ' . uniqid(), 'email' => uniqid('f_') . '@example.test',
            'password' => 'secret123', 'password_confirmation' => 'secret123',
            'localite_ids' => [$loc],
        ];

        $this->actingAs($coord, 'admin')
            ->post(route('coordinateur.facilitateurs.store'), $payload($hors))
            ->assertSessionHasErrors('localite_ids.0');

        $this->actingAs($coord, 'admin')
            ->post(route('coordinateur.facilitateurs.store'), $payload($dans))
            ->assertRedirect(route('coordinateur.facilitateurs.index'));

        $f = User::where('created_by', $coord->id)->firstOrFail();
        $this->assertEquals([$wilaya], $f->allowedWilayaIds());
    }

    public function test_creation_facilitateur_propose_tous_les_projets_et_operateurs()
    {
        [$ongA] = $this->twoPendingReports();
        $coord = $this->makeUser('coordinat', $ongA);

        // Le formulaire liste tous les projets et tous les opérateurs.
        $page = $this->actingAs($coord, 'admin')->get(route('coordinateur.facilitateurs.create'))->assertOk()->getContent();
        $this->assertEquals(DB::table('projets')->count(), substr_count($page, 'name="projet_ids[]"'));
        $this->assertEquals(DB::table('operateurs')->count(), substr_count($page, 'name="operateur_ids[]"'));

        // Projet et opérateur hors marchés de l'ONG : acceptés.
        $projet = DB::table('projets')->whereNotIn('id', DB::table('marches')->where('operateur_id', $ongA)->pluck('projet_id'))->value('id');
        $autreOng = DB::table('operateurs')->where('id', '!=', $ongA)->value('id');
        $email = uniqid('f_') . '@example.test';
        $this->actingAs($coord, 'admin')->post(route('coordinateur.facilitateurs.store'), [
            'name' => 'Facil ops', 'email' => $email,
            'password' => 'secret123', 'password_confirmation' => 'secret123',
            'localite_ids' => [DB::table('localites')->value('id')],
            'projet_ids' => [$projet], 'operateur_ids' => [$ongA, $autreOng],
        ])->assertRedirect(route('coordinateur.facilitateurs.index'));

        $f = User::where('email', $email)->firstOrFail();
        $this->assertEquals($ongA, $f->operateur_id); // reste dans l'ONG du coordinateur
        $this->assertEqualsCanonicalizing([$ongA, $autreOng], $f->operateursAutorises()->pluck('operateurs.id')->all());

        // L'app mobile ne propose que les projets / opérateurs cochés.
        $token = $this->postJson('/api/login', ['username' => $email, 'password' => 'secret123'])->json('result.headers.authorization');
        $data = $this->withHeader('Authorization', 'Bearer ' . $token)->getJson('/api')->json('result');
        $this->assertEquals([$projet], array_column($data['projets'], 'id'));
        $this->assertEqualsCanonicalizing([$ongA, $autreOng], array_column($data['operateurs'], 'id'));

        // Formulaire admin : listes complètes aussi.
        $admin = $this->makeUser('administrat', null);
        $page = $this->actingAs($admin, 'admin')->get(route('admin.users.create'))->assertOk()->getContent();
        $this->assertEquals(DB::table('projets')->count(), substr_count($page, 'name="projet_ids[]"'));
        $this->assertEquals(DB::table('operateurs')->count(), substr_count($page, 'name="operateur_ids[]"'));
    }

    public function test_coordinateur_cree_un_facilitateur_de_son_ong()
    {
        [$ongA] = $this->twoPendingReports();
        $coord = $this->makeUser('coordinat', $ongA);
        $localite = DB::table('localites')->value('id');

        $this->actingAs($coord, 'admin')
            ->post(route('coordinateur.facilitateurs.store'), [
                'name' => 'Facilitateur test',
                'email' => 'facil_' . uniqid() . '@example.test',
                'password' => 'secret123',
                'password_confirmation' => 'secret123',
                'localite_ids' => [$localite],
            ])->assertRedirect(route('coordinateur.facilitateurs.index'));

        $f = User::where('created_by', $coord->id)->firstOrFail();
        $this->assertEquals($ongA, $f->operateur_id);
        $this->assertTrue($f->isFacilitateur());
        $this->assertEquals([$localite], $f->allowedLocaliteIds());

        // Les pages du coordinateur s'affichent.
        $this->actingAs($coord, 'admin')->get(route('coordinateur.facilitateurs.index'))->assertOk()->assertSee('Facilitateur test');
        $this->actingAs($coord, 'admin')->get(route('coordinateur.facilitateurs.edit', $f->id))->assertOk();
        $this->actingAs($coord, 'admin')->get(route('coordinateur.validation', ['type' => 'infrastructure', 'statut' => 'tous']))->assertOk();
    }
}
