<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use Faker\Factory as Faker;

class SiswaSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');
        $startNis = 20000;

        for ($i = 0; $i < 10; $i++) {
            Siswa::create([
                'nama' => $faker->name,
                'nis' => $startNis + $i,
                'gender' => $i % 2 === 0 ? 'L' : 'P',
                'alamat' => $faker->address,
                'kontak' => '08' . rand(1000000000, 9999999999),
                'email' => $faker->unique()->safeEmail,
                'foto' => 'default.jpg',
            ]);
        }
    }
}
