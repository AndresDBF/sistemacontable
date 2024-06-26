<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CatProveSeeder::class);
        $this->call(MonedaSeeder::class);
        $this->call(ReglasStsSeeder::class);
        $this->call(TipPagoSeeder::class);
        $this->call(ValorNominaSeeder::class);
    }
}
