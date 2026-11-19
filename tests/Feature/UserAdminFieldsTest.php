<?php

namespace Tests\Feature;

use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserAdminFieldsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_fields_are_not_mass_assignable(): void
    {
        $user = User::create([
            'first_name' => 'Mass',
            'last_name' => 'Assign',
            'email' => 'mass@example.com',
            'password' => 'password',
            'is_admin' => true,
            'games_available' => 99,
        ]);

        $user->refresh();

        $this->assertFalse((bool) $user->is_admin);
        $this->assertSame(0, (int) $user->games_available);
    }

    public function test_registration_cannot_grant_admin_or_credits(): void
    {
        $this->post('/register', [
            'first_name' => 'Sneaky',
            'last_name' => 'User',
            'email' => 'sneaky@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'is_admin' => 1,
            'games_available' => 99,
        ]);

        $user = User::where('email', 'sneaky@example.com')->firstOrFail();

        $this->assertFalse((bool) $user->is_admin);
        $this->assertSame(0, (int) $user->games_available);
    }

    public function test_only_admins_can_access_the_admin_panel(): void
    {
        $panel = Filament::getPanel('admin');

        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        $this->assertTrue($admin->canAccessPanel($panel));
        $this->assertFalse(User::factory()->create()->canAccessPanel($panel));
    }

    public function test_admin_can_set_admin_fields_from_the_edit_page(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $target = User::factory()->create();

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(EditUser::class, ['record' => $target->getRouteKey()])
            ->fillForm([
                'is_admin' => true,
                'games_available' => 5,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $target->refresh();

        $this->assertTrue((bool) $target->is_admin);
        $this->assertSame(5, (int) $target->games_available);
    }
}
