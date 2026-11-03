<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Désactivation d'un compte : plus de connexion (web et mobile), données
 * conservées. L'admin désactive tout compte, le coordinateur ses facilitateurs.
 */
class UserStatusTest extends TestCase
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

    private function makeUser(string $roleLike, ?int $ongId = null): User
    {
        $user = User::create([
            'username' => 'test_' . uniqid(),
            'email' => uniqid('test_') . '@example.test',
            'password' => bcrypt('secret123'),
            'photo' => 'default.png',
            'operateur_id' => $ongId,
        ]);
        $roleId = DB::table('roles')->where('name', 'like', "%$roleLike%")->value('id');
        DB::table('role_user')->insert(['user_id' => $user->id, 'role_id' => $roleId]);
        return $user->fresh();
    }

    public function test_admin_desactive_puis_reactive_un_compte()
    {
        $admin = $this->makeUser('administrat');
        $fac = $this->makeUser('facil');
        $fac->createToken('MyApp');

        $this->actingAs($admin, 'admin')
            ->post(route('admin.users.toggle_status', $fac->id))
            ->assertRedirect(route('admin.users.index'));

        $fac->refresh();
        $this->assertFalse($fac->isActive());
        $this->assertSame(0, $fac->tokens()->count());
        $this->actingAs($admin, 'admin')->get(route('admin.users.index'))
            ->assertOk()->assertSee(__('user.status_disabled'));

        // Connexion mobile refusée, avec un message explicite.
        $this->postJson('/api/login', ['username' => $fac->email, 'password' => 'secret123'])
            ->assertStatus(404)
            ->assertJson(['success' => false, 'message' => __('auth.disabled')]);

        $this->actingAs($admin, 'admin')->post(route('admin.users.toggle_status', $fac->id));
        $this->assertTrue($fac->fresh()->isActive());
        $this->postJson('/api/login', ['username' => $fac->email, 'password' => 'secret123'])
            ->assertOk();
    }

    public function test_session_web_d_un_compte_desactive_est_fermee()
    {
        $drha = $this->makeUser('drha');
        DB::table('users')->where('id', $drha->id)->update(['status' => 'Disable']);

        $this->actingAs($drha->fresh(), 'admin')
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.showlogin'));
        $this->assertNull(auth()->guard('admin')->user());
    }

    public function test_admin_ne_peut_pas_se_desactiver()
    {
        $admin = $this->makeUser('administrat');
        $this->actingAs($admin, 'admin')->post(route('admin.users.toggle_status', $admin->id));
        $this->assertTrue($admin->fresh()->isActive());
    }

    public function test_coordinateur_ne_desactive_que_ses_facilitateurs()
    {
        $ongs = DB::table('operateurs')->orderBy('id')->limit(2)->pluck('id');
        $coord = $this->makeUser('coordinat', $ongs[0]);
        $mine = $this->makeUser('facil', $ongs[0]);
        $other = $this->makeUser('facil', $ongs[1]);

        $this->actingAs($coord, 'admin')
            ->post(route('coordinateur.facilitateurs.toggle_status', $mine->id))
            ->assertRedirect(route('coordinateur.facilitateurs.index'));
        $this->assertFalse($mine->fresh()->isActive());
        $this->actingAs($coord, 'admin')->get(route('coordinateur.facilitateurs.index'))
            ->assertOk()->assertSee(__('user.enable'));

        $this->actingAs($coord, 'admin')
            ->post(route('coordinateur.facilitateurs.toggle_status', $other->id))
            ->assertNotFound();
        $this->assertTrue($other->fresh()->isActive());
    }
}
