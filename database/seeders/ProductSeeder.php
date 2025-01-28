<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use Faker\Factory as Faker;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1,39) as $index) {
            Product::create([
                'name' => $faker->name,
                'price' => $faker->numberBetween($min = 1000, $max = 2000),
                'purchase' => $faker->numberBetween($min = 1000, $max = 2000),
                'profit' => $faker->numberBetween($min = 300, $max = 700),
                'category_id' => $faker->numberBetween($min = 63, $max = 66),
                'thumbnail' => $faker->imageUrl($width = 200, $height = 200),
                'video' => $faker->url('http://localhost/oyehoe/public/video/1684444472.mp4'),
                'pinned_at' => Carbon::now(),
                'article' => $faker->company,
            ]);
        }
    }
}
