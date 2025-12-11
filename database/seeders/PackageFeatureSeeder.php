<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\PackageFeature;

class PackageFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some packages to associate features with
        $packages = Package::take(3)->get();
        
        if ($packages->isEmpty()) {
            $this->command->warn('No packages found. Please create some packages first.');
            return;
        }

        $features = [
            // Features for first package
            [
                'package_id' => $packages[0]->id,
                'title' => 'Unlimited Access',
                'description' => 'Get unlimited access to all course materials and resources for the duration of your subscription.',
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'package_id' => $packages[0]->id,
                'title' => '24/7 Support',
                'description' => 'Round-the-clock customer support to help you with any questions or issues.',
                'status' => 'active',
                'sort_order' => 2,
            ],
            [
                'package_id' => $packages[0]->id,
                'title' => 'Mobile App Access',
                'description' => 'Access your courses on the go with our mobile application.',
                'status' => 'active',
                'sort_order' => 3,
            ],
            [
                'package_id' => $packages[0]->id,
                'title' => 'Certificate of Completion',
                'description' => 'Receive a verified certificate upon successful completion of the course.',
                'status' => 'active',
                'sort_order' => 4,
            ],
            [
                'package_id' => $packages[0]->id,
                'title' => 'Live Q&A Sessions',
                'description' => 'Participate in weekly live Q&A sessions with instructors.',
                'status' => 'inactive',
                'sort_order' => 5,
            ],

            // Features for second package (if exists)
            [
                'package_id' => $packages->count() > 1 ? $packages[1]->id : $packages[0]->id,
                'title' => 'Premium Content',
                'description' => 'Access to exclusive premium content not available in basic packages.',
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'package_id' => $packages->count() > 1 ? $packages[1]->id : $packages[0]->id,
                'title' => 'One-on-One Mentoring',
                'description' => 'Personal mentoring sessions with industry experts.',
                'status' => 'active',
                'sort_order' => 2,
            ],
            [
                'package_id' => $packages->count() > 1 ? $packages[1]->id : $packages[0]->id,
                'title' => 'Project Portfolio Review',
                'description' => 'Get your project portfolio reviewed by professionals.',
                'status' => 'active',
                'sort_order' => 3,
            ],
            [
                'package_id' => $packages->count() > 1 ? $packages[1]->id : $packages[0]->id,
                'title' => 'Job Placement Assistance',
                'description' => 'Assistance with job placement and career guidance.',
                'status' => 'inactive',
                'sort_order' => 4,
            ],

            // Features for third package (if exists)
            [
                'package_id' => $packages->count() > 2 ? $packages[2]->id : $packages[0]->id,
                'title' => 'Advanced Analytics',
                'description' => 'Detailed analytics and progress tracking for your learning journey.',
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'package_id' => $packages->count() > 2 ? $packages[2]->id : $packages[0]->id,
                'title' => 'Custom Learning Path',
                'description' => 'Personalized learning path based on your goals and current skill level.',
                'status' => 'active',
                'sort_order' => 2,
            ],
            [
                'package_id' => $packages->count() > 2 ? $packages[2]->id : $packages[0]->id,
                'title' => 'Group Study Sessions',
                'description' => 'Join group study sessions with other learners.',
                'status' => 'active',
                'sort_order' => 3,
            ],
            [
                'package_id' => $packages->count() > 2 ? $packages[2]->id : $packages[0]->id,
                'title' => 'Offline Downloads',
                'description' => 'Download course materials for offline learning.',
                'status' => 'active',
                'sort_order' => 4,
            ],
            [
                'package_id' => $packages->count() > 2 ? $packages[2]->id : $packages[0]->id,
                'title' => 'Priority Support',
                'description' => 'Get priority support with faster response times.',
                'status' => 'inactive',
                'sort_order' => 5,
            ],

            // Additional mixed features
            [
                'package_id' => $packages[0]->id,
                'title' => 'Community Access',
                'description' => 'Join our exclusive community of learners and professionals.',
                'status' => 'active',
                'sort_order' => 6,
            ],
            [
                'package_id' => $packages[0]->id,
                'title' => 'Lifetime Updates',
                'description' => 'Receive lifetime updates to course content and materials.',
                'status' => 'active',
                'sort_order' => 7,
            ],
            [
                'package_id' => $packages->count() > 1 ? $packages[1]->id : $packages[0]->id,
                'title' => 'Money-Back Guarantee',
                'description' => '30-day money-back guarantee if you are not satisfied.',
                'status' => 'active',
                'sort_order' => 5,
            ],
            [
                'package_id' => $packages->count() > 2 ? $packages[2]->id : $packages[0]->id,
                'title' => 'API Access',
                'description' => 'Access to our API for integration with other tools.',
                'status' => 'inactive',
                'sort_order' => 6,
            ],
        ];

        foreach ($features as $feature) {
            PackageFeature::create($feature);
        }

        $this->command->info('Package features seeded successfully!');
    }
}