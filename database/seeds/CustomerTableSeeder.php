<?php

use Illuminate\Database\Seeder;

class CustomerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        DB::unprepared(file_get_contents(storage_path('seeds/customers.sql')));
        $this->command->info('Customers table seeded!');
    }
}
