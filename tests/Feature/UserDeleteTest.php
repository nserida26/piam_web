<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Suppression d'un compte depuis Utilisateurs : les liens sont nettoyés,
 * et un compte qui a des données collectées n'est pas supprimé (la cascade
 * en base effacerait ses rapports).
 */
class UserDeleteTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);
    }

    private function makeUser(string $roleLike): User
    {
        $user = User::create([
            'username' => 'test_' . uniqid(),
            'email' => uniqid('test_') . '@example.test',
            'password' => bcrypt('secret123'),
            'photo' => 'default.png',
        ]);
        $roleId = DB::table('roles')->where('name', 'like', "%$roleLike%")->value('id');
        DB::table('role_user')->insert(['user_id' => $user->id, 'role_id' => $roleId]);
        return $user->fresh();
    }

    public function test_suppression_d_un_compte_sans_donnees_nettoie_ses_liens()
    {
        $admin = $this->makeUser('administrat');
        $fac = $this->makeUser('facil');
        $fac->localites()->sync([DB::table('localites')->value('id')]);
        $fac->operateursAutorises()->sync([DB::table('operateurs')->value('id')]);
        DB::table('users')->where('id', $admin->id)->update(['created_by' => $fac->id]);

        $this->actingAs($admin, 'admin')
            ->delete(route('admin.users.destroy', $fac->id))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success');

        $this->assertNull(User::find($fac->id));
        $this->assertSame(0, DB::table('role_user')->where('user_id', $fac->id)->count());
        $this->assertSame(0, DB::table('user_localites')->where('user_id', $fac->id)->count());
        $this->assertSame(0, DB::table('user_operateurs_autorises')->where('user_id', $fac->id)->count());
        $this->assertNull(DB::table('users')->where('id', $admin->id)->value('created_by'));
    }

    public function test_un_compte_avec_des_donnees_collectees_n_est_pas_supprime()
    {
        $admin = $this->makeUser('administrat');
        $fac = $this->makeUser('facil');
        $tpl = (array) DB::table('localite_operations')->first();
        $reportId = DB::table('localite_operations')->insertGetId(
            ['user_id' => $fac->id] + collect($tpl)->except(['id', 'mobile_ref'])->all()
        );

        $this->actingAs($admin, 'admin')
            ->delete(route('admin.users.destroy', $fac->id))
            ->assertSessionHas('warning');

        $this->assertNotNull(User::find($fac->id));
        $this->assertTrue(DB::table('localite_operations')->where('id', $reportId)->exists());
    }

    public function test_l_admin_ne_peut_pas_supprimer_son_propre_compte()
    {
        $admin = $this->makeUser('administrat');

        $this->actingAs($admin, 'admin')
            ->delete(route('admin.users.destroy', $admin->id))
            ->assertSessionHas('warning');

        $this->assertNotNull(User::find($admin->id));
    }
}
