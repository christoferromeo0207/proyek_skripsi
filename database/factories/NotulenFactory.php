<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class NotulenFactory extends Factory
{
    protected $model = \App\Models\Notulen::class;

    public function definition()
{
    return [
        'tanggal' => Carbon::now()->subDays(rand(1, 30))->format('Y-m-d H:i:s'),
        'nama' => $this->faker->name,
        'unit' => $this->faker->word,
        'jabatan' => $this->faker->jobTitle,
        'no_hp' => '08' . $this->faker->randomNumber(8, true),
        'pertemuan' => 'Pertemuan ' . rand(1, 20),
        'jenis' => $this->faker->word,
        'status' => $this->faker->randomElement(['Selesai', 'Dalam proses']),
        'created_at' => now(),
        'updated_at' => now(),
    ];
}

}

