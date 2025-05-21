<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CommissionLevel;

class CommissionLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        CommissionLevel::updateOrCreate(
            ['label'      => 'Level A'],    
            ['percentage' => 7.00]          
        );

        CommissionLevel::updateOrCreate(
            ['label'      => 'Level B'],
            ['percentage' => 5.00]
        );

        CommissionLevel::updateOrCreate(
            ['label'      => 'Level C'],
            ['percentage' => 3.50]
        );
        
    }

}
