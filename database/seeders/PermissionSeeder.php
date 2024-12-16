<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Models\Admin;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create or assign the "view_users" permission
        $this->createOrAssignPermission('view_users');
        $this->createOrAssignPermission('view_complaints');
        $this->createOrAssignPermission('manage_setting');

        // Create or assign the "item-management" permission
        $this->createOrAssignPermission('view_products');
        $this->createOrAssignPermission('edit_product');
        $this->createOrAssignPermission('view_articles');
        $this->createOrAssignPermission('view_categories');
        $this->createOrAssignPermission('manage_markeet_pickup');

        // view orders
        $this->createOrAssignPermission('view_orders');

        // manage admins
        $this->createOrAssignPermission('view_admins');
        $this->createOrAssignPermission('view_balance_history');

        // general inventory
        $this->createOrAssignPermission('view_inventory');
        $this->createOrAssignPermission('view_general_inventory_orders');

        // reviews
        $this->createOrAssignPermission('view_customers_reviews');
    }

    /**
     * Create or assign the specified permission.
     *
     * @param  string  $permissionName
     * @return void
     */
    private function createOrAssignPermission($permissionName)
    {
        $permission = Permission::where('name', $permissionName)->first();

        // If permission doesn't exist, create it
        if (!$permission) {
            $permission = Permission::create(['name' => $permissionName, 'guard_name' => 'admin']);
        }

        // Assign the permission to the admin user if not already assigned
        $admin = Admin::find(1);
        if ($admin && !$admin->hasPermissionTo($permission)) {
            $admin->givePermissionTo($permission);
        }
    }
}
