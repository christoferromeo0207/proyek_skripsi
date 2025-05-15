<?php
namespace Database\Factories;

use App\Models\Marketing;
use Illuminate\Database\Eloquent\Factories\Factory;

class PegawaiMarketingFactory extends Factory
{
    protected $model = Marketing::class;

    public function definition(): array
    {
        return [
            'nama_pegawai'   => $this->faker->name(),
            'username_pegawai' => $this->faker->unique()->userName(),
            'email'          => $this->faker->unique()->safeEmail(),
            'posisi_pegawai' => 'Pegawai Marketing',
            'no_telp'        => $this->faker->phoneNumber(),
            'tempat_lahir'   => $this->faker->city(),
            'tanggal_lahir'  => $this->faker->date(),
            'tanggal_masuk'  => $this->faker->dateTimeBetween('-5 years','now')->format('Y-m-d'),
            'PIC'            => $this->faker->boolean(30),
        ];
    }
}
