<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Marketing;

class MarketingSeeder extends Seeder
{
    public function run(): void
    {
        // 5 pegawai dummy via factory
        Marketing::factory()->count(5)->create();

        // contoh manual
        Marketing::create([
            'nama_pegawai'   => 'Christofer Romeo',
            'username_pegawai' => 'christoferromeo',
            'email'          => 'christofer@gmail.com',
            'posisi_pegawai' => 'Junior Marketing',
            'no_telp'        => '081234567890',
            'tempat_lahir'   => 'Denpasar',
            'tanggal_lahir'  => '2000-03-05',
            'tanggal_masuk'  => '2021-05-15',
            'PIC'            => true,
        ]);
    }
}
