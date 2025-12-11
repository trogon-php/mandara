<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some courses and categories for relationships
        $courses = Course::take(3)->get();
        $categories = Category::take(2)->get();

        $notifications = [
            [
                'title' => 'Welcome to Our Learning Platform!',
                'description' => 'Get started with your learning journey. Explore our comprehensive courses and unlock your potential.',
                'course_id' => $courses->first()?->id,
                'category_id' => $categories->first()?->id,
                'premium' => false,
                'action_link' => '/courses',
                'image' => null,
            ],
            [
                'title' => 'New Course Available: Advanced JavaScript',
                'description' => 'Master advanced JavaScript concepts including ES6+, async programming, and modern frameworks.',
                'course_id' => $courses->skip(1)->first()?->id,
                'category_id' => $categories->first()?->id,
                'premium' => true,
                'action_link' => '/courses/advanced-javascript',
                'image' => null,
            ],
            [
                'title' => 'Weekly Learning Challenge',
                'description' => 'Complete this week\'s coding challenge and earn bonus points. Challenge yourself with real-world problems.',
                'course_id' => null,
                'category_id' => $categories->skip(1)->first()?->id,
                'premium' => false,
                'action_link' => '/challenges/weekly',
                'image' => null,
            ],
            [
                'title' => 'Premium Content Unlocked',
                'description' => 'Congratulations! You now have access to premium content including exclusive tutorials and expert sessions.',
                'course_id' => $courses->last()?->id,
                'category_id' => null,
                'premium' => true,
                'action_link' => '/premium/dashboard',
                'image' => null,
            ],
            [
                'title' => 'System Maintenance Notice',
                'description' => 'We will be performing scheduled maintenance on Sunday, 2:00 AM - 4:00 AM. Some features may be temporarily unavailable.',
                'course_id' => null,
                'category_id' => null,
                'premium' => false,
                'action_link' => null,
                'image' => null,
            ],
            [
                'title' => 'New Feature: Interactive Quizzes',
                'description' => 'Test your knowledge with our new interactive quiz system. Get instant feedback and track your progress.',
                'course_id' => $courses->first()?->id,
                'category_id' => $categories->first()?->id,
                'premium' => false,
                'action_link' => '/features/quizzes',
                'image' => null,
            ],
            [
                'title' => 'Course Completion Certificate',
                'description' => 'You have successfully completed the "Web Development Fundamentals" course. Download your certificate now!',
                'course_id' => $courses->skip(1)->first()?->id,
                'category_id' => $categories->skip(1)->first()?->id,
                'premium' => false,
                'action_link' => '/certificates/download',
                'image' => null,
            ],
            [
                'title' => 'Live Webinar: AI in Education',
                'description' => 'Join our exclusive webinar on "Artificial Intelligence in Education" featuring industry experts. Limited seats available.',
                'course_id' => null,
                'category_id' => $categories->first()?->id,
                'premium' => true,
                'action_link' => '/webinars/ai-education',
                'image' => null,
            ],
            [
                'title' => 'Profile Update Required',
                'description' => 'Please update your profile information to continue using all platform features. It only takes 2 minutes.',
                'course_id' => null,
                'category_id' => null,
                'premium' => false,
                'action_link' => '/profile/edit',
                'image' => null,
            ],
            [
                'title' => 'Special Offer: 50% Off Premium',
                'description' => 'Limited time offer! Get 50% off on premium subscription. Unlock unlimited access to all courses and features.',
                'course_id' => $courses->last()?->id,
                'category_id' => $categories->last()?->id,
                'premium' => true,
                'action_link' => '/offers/premium-discount',
                'image' => null,
            ],
        ];

        foreach ($notifications as $notificationData) {
            Notification::create($notificationData);
        }
    }
}
