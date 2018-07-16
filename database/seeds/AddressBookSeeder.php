<?php

use Illuminate\Database\Seeder;

class AddressBookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::unprepared(file_get_contents(storage_path('seeds/address_books.sql')));
        $this->command->info('Address Book table seeded!');
    }
}
