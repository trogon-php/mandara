<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AdditionalNotificationSeeder extends Seeder
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
                'title' => 'New Course: React Native Development',
                'description' => 'Learn to build cross-platform mobile apps with React Native. From basics to advanced concepts.',
                'course_id' => $courses->first()?->id,
                'category_id' => $categories->first()?->id,
                'premium' => true,
                'action_link' => '/courses/react-native',
                'image' => null,
                'created_at' => Carbon::now()->subDays(7)->subHours(3), // 7 days ago
            ],
            [
                'title' => 'Monthly Progress Report',
                'description' => 'Your learning progress for this month: 15 courses completed, 8 certificates earned!',
                'course_id' => null,
                'category_id' => null,
                'premium' => false,
                'action_link' => '/reports/monthly',
                'image' => null,
                'created_at' => Carbon::now()->subDays(5)->subHours(12), // 5 days ago
            ],
            [
                'title' => 'Live Coding Session: Python Algorithms',
                'description' => 'Join our live coding session on Python algorithms. Interactive session with Q&A.',
                'course_id' => $courses->skip(1)->first()?->id,
                'category_id' => $categories->first()?->id,
                'premium' => false,
                'action_link' => '/live-sessions/python-algorithms',
                'image' => null,
                'created_at' => Carbon::now()->subDays(3)->subHours(6), // 3 days ago
            ],
            [
                'title' => 'Course Update: JavaScript ES2024',
                'description' => 'Your JavaScript course has been updated with the latest ES2024 features and examples.',
                'course_id' => $courses->skip(1)->first()?->id,
                'category_id' => $categories->first()?->id,
                'premium' => false,
                'action_link' => '/courses/javascript-es2024',
                'image' => null,
                'created_at' => Carbon::now()->subDays(2)->subHours(9), // 2 days ago
            ],
            [
                'title' => 'Community Challenge: Build a Todo App',
                'description' => 'Participate in our community challenge! Build a todo app using any technology.',
                'course_id' => null,
                'category_id' => $categories->skip(1)->first()?->id,
                'premium' => false,
                'action_link' => '/challenges/todo-app',
                'image' => null,
                'created_at' => Carbon::now()->subDay()->subHours(4), // 1 day ago
            ],
            [
                'title' => 'Premium Feature: Code Review Sessions',
                'description' => 'Get your code reviewed by industry experts. New premium feature now available!',
                'course_id' => $courses->last()?->id,
                'category_id' => $categories->last()?->id,
                'premium' => true,
                'action_link' => '/premium/code-review',
                'image' => null,
                'created_at' => Carbon::now()->subHours(18), // 18 hours ago
            ],
            [
                'title' => 'Weekend Workshop: Data Structures',
                'description' => 'Join our weekend workshop on data structures and algorithms. Perfect for interview prep.',
                'course_id' => $courses->first()?->id,
                'category_id' => $categories->first()?->id,
                'premium' => false,
                'action_link' => '/workshops/data-structures',
                'image' => null,
                'created_at' => Carbon::now()->subHours(12), // 12 hours ago
            ],
            [
                'title' => 'New Instructor: Sarah Johnson',
                'description' => 'Meet our new instructor Sarah Johnson, expert in Machine Learning and AI. Check out her courses!',
                'course_id' => null,
                'category_id' => $categories->first()?->id,
                'premium' => false,
                'action_link' => '/instructors/sarah-johnson',
                'image' => null,
                'created_at' => Carbon::now()->subHours(8), // 8 hours ago
            ],
            [
                'title' => 'Course Completion: Web Development Bootcamp',
                'description' => 'Congratulations! You have successfully completed the Web Development Bootcamp. Download your certificate.',
                'course_id' => $courses->skip(1)->first()?->id,
                'category_id' => $categories->skip(1)->first()?->id,
                'premium' => false,
                'action_link' => '/certificates/web-development-bootcamp',
                'image' => null,
                'created_at' => Carbon::now()->subHours(4), // 4 hours ago
            ],
            [
                'title' => 'Flash Sale: 70% Off All Courses',
                'description' => 'Limited time offer! Get 70% off on all courses. Sale ends in 24 hours. Don\'t miss out!',
                'course_id' => null,
                'category_id' => null,
                'premium' => false,
                'action_link' => '/flash-sale',
                'image' => null,
                'created_at' => Carbon::now()->subHours(2), // 2 hours ago
            ],
        ];

        foreach ($notifications as $notificationData) {
            Notification::create($notificationData);
        }
    }
}
