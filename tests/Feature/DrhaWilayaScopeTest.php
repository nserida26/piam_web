<?php

namespace Tests\Feature;

use App\Models\Localite;
use App\Models\LocaliteReport;
use App\Models\User;
use App\Models\Wilaya;
use App\Support\WilayaScope;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Compte DRHA : tableau de bord, reporting, validations… limités à sa wilaya.
 */
class DrhaWilayaScopeTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        WilayaScope::forget();
        $this->withoutMiddleware([
            \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        ]);
    }

    private function makeUser(string $roleLike, array $wilayaIds = []): User
    {
        $user = User::create([
            'username' => 'test_' . uniqid(),
            'email' => uniqid('test_') . '@example.test',
            'password' => bcrypt('secret123'),
            'photo' => 'default.png',
        ]);
        $roleId = DB::table('roles')->where('name', 'like', "%$roleLike%")->value('id');
        DB::table('role_user')->insert(['user_id' => $user->id, 'role_id' => $roleId]);
        foreach ($wilayaIds as $w) {
            DB::table('wilaya_users')->insert(['user_id' => $user->id, 'wilaya_id' => $w]);
        }
        return $user->fresh();
    }

    /** La wilaya qui a le plus de rapports localité. */
    private function busiestWilaya(): int
    {
        return (int) DB::table('localite_operations')->select('wilaya_id')
            ->groupBy('wilaya_id')->orderByRaw('COUNT(*) DESC')->value('wilaya_id');
    }

    public function test_drha_ne_voit_que_sa_wilaya()
    {
        $w = $this->busiestWilaya();
        $drha = $this->makeUser('drha', [$w]);
        $this->actingAs($drha, 'admin');

        $this->assertSame([$w], Wilaya::pluck('id')->map(function ($id) { return (int) $id; })->all());
        $this->assertSame(DB::table('localites')->where('wilaya_id', $w)->count(), Localite::count());
        $this->assertSame(DB::table('localite_operations')->where('wilaya_id', $w)->count(), LocaliteReport::count());
        $this->assertSame(
            DB::table('localite_operations')->where('wilaya_id', $w)->count(),
            DB::table('localite_operations')->tap(WilayaScope::on())->count()
        );
    }

    public function test_pages_drha_filtrees_et_outils_nationaux_fermes()
    {
        $w = $this->busiestWilaya();
        $drha = $this->makeUser('drha', [$w]);

        $this->actingAs($drha, 'admin')->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($drha, 'admin')->get(route('admin.reports.localites'))->assertOk();

        $res = $this->actingAs($drha, 'admin')->get(route('admin.reporting.index'))->assertOk();
        $this->assertEquals(
            DB::table('localites')->where('wilaya_id', $w)->count(),
            $res->original->getData()['report']['donnees_generales']['nb_localites']
        );

        $this->actingAs($drha, 'admin')->get(route('admin.users.index'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_drha_sans_wilaya_ne_voit_rien_et_admin_voit_tout()
    {
        $this->actingAs($this->makeUser('drha'), 'admin');
        $this->assertSame(0, Wilaya::count());
        $this->assertSame(0, LocaliteReport::count());

        WilayaScope::forget();
        $this->actingAs($this->makeUser('administrat', [$this->busiestWilaya()]), 'admin');
        $this->assertSame(DB::table('wilayas')->count(), Wilaya::count());
    }
}
