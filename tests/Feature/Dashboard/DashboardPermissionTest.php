<?php

namespace Tests\Feature\Dashboard;

use App\Filament\TeamPanel\Widgets\MonthlyFinanceChartWidget;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_developer_does_not_see_finance_block_on_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Developer');

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
        $this->assertFalse(MonthlyFinanceChartWidget::canView());
    }

    public function test_financial_user_sees_finance_block_on_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Financial');

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
        $this->assertTrue(MonthlyFinanceChartWidget::canView());
    }
}
