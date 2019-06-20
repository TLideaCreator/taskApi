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
         $this->call('InitRoles');
         $this->call('InitAdmin');
         $this->call('InitSystemTemplate');
         $this->call('UpdateProjectTemp');
         $this->call('UpdateProjectTask');
    }
}
