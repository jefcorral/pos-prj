<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        $company = Company::firstOrCreate(
            ['slug' => 'demo-store'],
            ['name' => 'Demo Store', 'currency' => 'PHP', 'timezone' => 'Asia/Manila']
        );

        $branch = Branch::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'MAIN'],
            ['name' => 'Main Branch', 'address' => '123 Main St.']
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@pos.test'],
            ['name' => 'Admin', 'password' => 'password', 'company_id' => $company->id, 'branch_id' => $branch->id]
        );
        $admin->assignRole('admin');

        $manager = User::firstOrCreate(
            ['email' => 'manager@pos.test'],
            ['name' => 'Manager', 'password' => 'password', 'company_id' => $company->id, 'branch_id' => $branch->id]
        );
        $manager->assignRole('manager');

        $cashier = User::firstOrCreate(
            ['email' => 'cashier@pos.test'],
            ['name' => 'Cashier', 'password' => 'password', 'company_id' => $company->id, 'branch_id' => $branch->id]
        );
        $cashier->assignRole('cashier');

        $unit = Unit::firstOrCreate(['company_id' => $company->id, 'name' => 'Piece'], ['abbreviation' => 'pc']);
        $vat = Tax::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'VAT 12%'],
            ['rate' => 12, 'type' => 'inclusive']
        );

        $categories = collect(['Beverages', 'Snacks', 'Groceries', 'Personal Care'])->mapWithKeys(
            fn ($name) => [$name => Category::firstOrCreate(
                ['company_id' => $company->id, 'slug' => str()->slug($name)],
                ['name' => $name]
            )]
        );

        $brand = Brand::firstOrCreate(['company_id' => $company->id, 'name' => 'Generic']);

        $products = [
            ['name' => 'Coca-Cola 330ml', 'sku' => 'BEV-001', 'barcode' => '4800016000011', 'selling_price' => 35, 'cost_price' => 25, 'category' => 'Beverages', 'stock' => 100],
            ['name' => 'Bottled Water 500ml', 'sku' => 'BEV-002', 'barcode' => '4800016000028', 'selling_price' => 20, 'cost_price' => 12, 'category' => 'Beverages', 'stock' => 150],
            ['name' => 'Instant Coffee 3-in-1', 'sku' => 'BEV-003', 'barcode' => '4800016000035', 'selling_price' => 15, 'cost_price' => 9, 'category' => 'Beverages', 'stock' => 200],
            ['name' => 'Potato Chips 80g', 'sku' => 'SNK-001', 'barcode' => '4800016000042', 'selling_price' => 45, 'cost_price' => 30, 'category' => 'Snacks', 'stock' => 80],
            ['name' => 'Chocolate Bar', 'sku' => 'SNK-002', 'barcode' => '4800016000059', 'selling_price' => 50, 'cost_price' => 35, 'category' => 'Snacks', 'stock' => 60],
            ['name' => 'Rice 5kg', 'sku' => 'GRC-001', 'barcode' => '4800016000066', 'selling_price' => 280, 'cost_price' => 230, 'category' => 'Groceries', 'stock' => 40],
            ['name' => 'Canned Sardines', 'sku' => 'GRC-002', 'barcode' => '4800016000073', 'selling_price' => 25, 'cost_price' => 18, 'category' => 'Groceries', 'stock' => 8, 'low' => 20],
            ['name' => 'Shampoo Sachet', 'sku' => 'PC-001', 'barcode' => '4800016000080', 'selling_price' => 8, 'cost_price' => 5, 'category' => 'Personal Care', 'stock' => 300],
            ['name' => 'Toothpaste 100ml', 'sku' => 'PC-002', 'barcode' => '4800016000097', 'selling_price' => 65, 'cost_price' => 45, 'category' => 'Personal Care', 'stock' => 50],
        ];

        foreach ($products as $data) {
            $product = Product::firstOrCreate(
                ['company_id' => $company->id, 'sku' => $data['sku']],
                [
                    'name' => $data['name'],
                    'barcode' => $data['barcode'],
                    'selling_price' => $data['selling_price'],
                    'cost_price' => $data['cost_price'],
                    'category_id' => $categories[$data['category']]->id,
                    'brand_id' => $brand->id,
                    'unit_id' => $unit->id,
                    'tax_id' => $vat->id,
                    'low_stock_threshold' => $data['low'] ?? 10,
                    'branch_id' => $branch->id,
                ]
            );

            Inventory::firstOrCreate(
                ['branch_id' => $branch->id, 'product_id' => $product->id, 'product_variant_id' => null],
                ['company_id' => $company->id, 'quantity' => $data['stock']]
            );
        }

        Customer::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Walk-in Customer'],
            ['type' => 'regular']
        );

        Supplier::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Demo Supplier Inc.'],
            ['contact_name' => 'Juan Dela Cruz', 'phone' => '09171234567', 'email' => 'sales@demosupplier.test']
        );
    }
}
