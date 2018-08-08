<?php

use Illuminate\Database\Seeder;

class EnvironmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        DB::unprepared(file_get_contents(storage_path('seeds/environment_dump.sql')));
        $this->command->info('Environment table seeded!');
    }
}
