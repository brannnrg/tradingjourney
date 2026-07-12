<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            // Strategy tags
            ['name' => 'Breakout',           'type' => 'strategy'],
            ['name' => 'Pullback',           'type' => 'strategy'],
            ['name' => 'Scalping',           'type' => 'strategy'],
            ['name' => 'Swing Trading',      'type' => 'strategy'],
            ['name' => 'DCA',                'type' => 'strategy'],
            ['name' => 'Momentum',           'type' => 'strategy'],
            ['name' => 'Support/Resistance', 'type' => 'strategy'],

            // Emotion tags
            ['name' => 'Disiplin',           'type' => 'emotion'],
            ['name' => 'FOMO',               'type' => 'emotion'],
            ['name' => 'Revenge Trading',    'type' => 'emotion'],
            ['name' => 'Ragu-ragu',          'type' => 'emotion'],
            ['name' => 'Percaya Diri',       'type' => 'emotion'],
            ['name' => 'Greedy',             'type' => 'emotion'],
            ['name' => 'Panik',              'type' => 'emotion'],

            // Mistake tags
            ['name' => 'Overtrading',        'type' => 'mistake'],
            ['name' => 'SL Terlalu Sempit',  'type' => 'mistake'],
            ['name' => 'Entry Terlalu Cepat','type' => 'mistake'],
            ['name' => 'Tidak Sesuai Plan',  'type' => 'mistake'],
            ['name' => 'Over Leverage',      'type' => 'mistake'],
            ['name' => 'Chasing Pump',       'type' => 'mistake'],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(
                ['name' => $tag['name'], 'type' => $tag['type']]
            );
        }

        $this->command->info('Tags seeded: ' . count($tags) . ' tags created.');
    }
}
