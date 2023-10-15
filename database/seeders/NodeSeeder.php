<?php

namespace Database\Seeders;

use App\Models\Node;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        for ($i = 0; $i < 4; $i++) {
           $node = Node::create([
                'name' => 'corporation_'.$i,
                'parent_node_id' => null,
                'type' => 'corporation',
                'extra' => null,
                'height' => 0
            ]);
            for ($i = 0; $i < 4; $i++) {
                $node2 = Node::create([
                    'name' => 'building_'.$i,
                    'parent_node_id' => $node->id,
                    'type' => 'building',
                    'extra' => rand(1000,9999),
                    'height' => 1
                ]);
                for ($i = 0; $i < 4; $i++) {
                    $node3 = Node::create([
                        'name' => 'property_'.$i,
                        'parent_node_id' => $node2->id,
                        'type' => 'property',
                        'extra' => rand(5000,15000),
                        'height' => 2
                    ]);
                }
            }
    }
    }
}

