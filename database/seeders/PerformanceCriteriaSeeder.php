<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PerformanceCriteria;

class PerformanceCriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $criteria = [
            // Technical Skills
            [
                'name' => 'Job Knowledge',
                'description' => 'Understanding and application of job-specific knowledge and skills',
                'category' => 'technical',
                'weight' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'Quality of Work',
                'description' => 'Accuracy, thoroughness, and quality of work output',
                'category' => 'technical',
                'weight' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'Problem Solving',
                'description' => 'Ability to identify and resolve work-related problems',
                'category' => 'technical',
                'weight' => 15,
                'is_active' => true,
            ],

            // Productivity
            [
                'name' => 'Productivity',
                'description' => 'Amount and efficiency of work completed within expected timeframes',
                'category' => 'productivity',
                'weight' => 15,
                'is_active' => true,
            ],
            [
                'name' => 'Initiative',
                'description' => 'Self-motivation and proactive approach to work responsibilities',
                'category' => 'productivity',
                'weight' => 10,
                'is_active' => true,
            ],

            // Behavioral
            [
                'name' => 'Reliability',
                'description' => 'Dependability and consistency in meeting commitments',
                'category' => 'behavioral',
                'weight' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Adaptability',
                'description' => 'Flexibility and ability to adjust to changing circumstances',
                'category' => 'behavioral',
                'weight' => 10,
                'is_active' => true,
            ],

            // Communication
            [
                'name' => 'Communication Skills',
                'description' => 'Effectiveness in oral and written communication',
                'category' => 'communication',
                'weight' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Teamwork',
                'description' => 'Ability to work collaboratively with others',
                'category' => 'communication',
                'weight' => 10,
                'is_active' => true,
            ],

            // Leadership (Optional - for supervisory roles)
            [
                'name' => 'Leadership',
                'description' => 'Ability to guide, motivate, and develop team members',
                'category' => 'leadership',
                'weight' => 15,
                'is_active' => false, // Inactive by default, activate for supervisory reviews
            ],
            [
                'name' => 'Decision Making',
                'description' => 'Quality and timeliness of decisions made',
                'category' => 'leadership',
                'weight' => 15,
                'is_active' => false, // Inactive by default
            ],
        ];

        foreach ($criteria as $criterion) {
            PerformanceCriteria::create($criterion);
        }
    }
}
