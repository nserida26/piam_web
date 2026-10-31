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
