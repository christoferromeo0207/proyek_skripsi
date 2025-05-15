<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Notulen;

class NotulenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

     public function run()
    {
    // You can seed other models here as well
        Notulen::factory()->count(10)->create(); // Adjust the count as necessary

        $this->call([
            NotulenSeeder::class,
        ]);
    }
  
}
