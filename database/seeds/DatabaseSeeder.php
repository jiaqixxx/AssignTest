<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

//        $order = new \App\Order([
//            'order_items' => '',
//            'environment' => '',
//            'product_look_up' => '',
//            'customer_details' => '',
//        ]);
//
//        $order->save();

        $user1 = new \App\User([
            'email' => 'test@gmail.com',
            'password' => bcrypt('123123'),
            'name' => 'test'
        ]);
        $user1->save();

        $user2 = new \App\User([
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123123'),
            'name' => 'admin'
        ]);
        $user2->save();

//        $assignment = new \App\Assignment([
//            'order_id' => $order->id,
//            'assignee_id' => $user1->id,
//            'has_comments' => false,
//            'assigned_by' => $user2->id,
//            'is_all_good' => false,
//            'is_approved' => false
//        ]);
//        $assignment->save();
    }
}
