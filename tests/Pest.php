<?php

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in('Feature');

/**
 * Create a company + branch + user with the given role.
 *
 * @return array{0: Company, 1: Branch, 2: User}
 */
function tenant(string $role = 'admin'): array
{
    (new RolesAndPermissionsSeeder)->run();

    $company = Company::factory()->create();
    $branch = Branch::factory()->create(['company_id' => $company->id]);
    $user = User::factory()->create([
        'company_id' => $company->id,
        'branch_id' => $branch->id,
    ]);
    $user->assignRole($role);

    return [$company, $branch, $user];
}
