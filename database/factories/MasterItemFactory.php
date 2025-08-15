<?php

namespace Database\Factories;

use App\Models\MasterItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MasterItem>
 */
class MasterItemFactory extends Factory
{
    protected $model = MasterItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        static $increment = 1;
        return [
            'kode' => 'PRD' . str_pad($increment++, 4, '0', STR_PAD_LEFT),
            'nama' => $this->faker->word(),
            'jenis' => $this->faker->randomElement(['Obat', 'Alkes', 'Matkes', 'Umum', 'ATK']),
            'harga_beli' => $this->faker->numberBetween(100, 1000000),
            'laba' => $this->faker->numberBetween(0, 100),
            'supplier' => $this->faker->randomElement(['Tokopaedi', 'Bukulapuk', 'TokoBagas', 'E Commurz', 'Blublu']),
        ];
    }
}
