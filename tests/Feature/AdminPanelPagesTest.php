<?php

namespace Tests\Feature;

use App\Filament\Resources\GameProductResource;
use App\Filament\Resources\GameResource;
use App\Filament\Resources\QuestionResource;
use App\Filament\Resources\SettingResource;
use App\Filament\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminPanelPagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{0: class-string}>
     */
    public static function resourceProvider(): array
    {
        return [
            'users' => [UserResource::class],
            'games' => [GameResource::class],
            'questions' => [QuestionResource::class],
            'game products' => [GameProductResource::class],
            'settings' => [SettingResource::class],
        ];
    }

    #[DataProvider('resourceProvider')]
    public function test_admin_can_load_each_resource_list_page(string $resource): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        $this->actingAs($admin)
            ->get($resource::getUrl('index'))
            ->assertOk();
    }

    public function test_admin_can_load_the_user_edit_page(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        $this->actingAs($admin)
            ->get(UserResource::getUrl('edit', ['record' => User::factory()->create()]))
            ->assertOk();
    }

    public function test_non_admins_cannot_open_the_admin_panel(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(UserResource::getUrl('index'))
            ->assertForbidden();
    }
}
