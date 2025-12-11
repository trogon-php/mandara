<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\Role;
use Illuminate\Support\Facades\Hash;

class PaginationTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = time();
        $students = $this->generateStudentData($timestamp);

        $this->command->info("Creating 200 students for pagination testing...");

        foreach ($students as $studentData) {
            // Add password for all students
            $studentData['password'] = Hash::make('password123');
            
            try {
                User::create($studentData);
                $this->command->info("Created student: {$studentData['name']}");
                
            } catch (\Exception $e) {
                $this->command->error("Failed to create student {$studentData['name']}: " . $e->getMessage());
            }
        }

        $this->command->info('Pagination test seeding completed!');
    }

    /**
     * Generate 200 diverse student test data for pagination testing
     */
    private function generateStudentData(int $timestamp): array
    {
        $firstNames = [
            'Aaron', 'Abigail', 'Adam', 'Adrian', 'Aiden', 'Alex', 'Alexander', 'Alexis', 'Alice', 'Allison',
            'Amanda', 'Amber', 'Amy', 'Andrea', 'Andrew', 'Angela', 'Anna', 'Anthony', 'Ashley', 'Austin',
            'Ava', 'Benjamin', 'Beth', 'Blake', 'Brandon', 'Brenda', 'Brian', 'Brittany', 'Bruce', 'Bryan',
            'Caleb', 'Cameron', 'Carl', 'Carol', 'Carolyn', 'Catherine', 'Charles', 'Charlotte', 'Cheryl', 'Christian',
            'Christina', 'Christopher', 'Cindy', 'Claire', 'Clarence', 'Cody', 'Colin', 'Colleen', 'Connor', 'Courtney',
            'Craig', 'Crystal', 'Cynthia', 'Daniel', 'Danielle', 'David', 'Deborah', 'Debra', 'Denise', 'Dennis',
            'Diana', 'Diane', 'Donald', 'Donna', 'Doris', 'Dorothy', 'Douglas', 'Dylan', 'Edward', 'Elizabeth',
            'Emily', 'Emma', 'Eric', 'Erica', 'Eugene', 'Evelyn', 'Frank', 'Gabriel', 'Gary', 'George',
            'Gerald', 'Gloria', 'Grace', 'Gregory', 'Hannah', 'Harold', 'Harry', 'Heather', 'Helen', 'Henry',
            'Howard', 'Ian', 'Isaac', 'Isabella', 'Jack', 'Jacob', 'James', 'Jane', 'Janet', 'Janice',
            'Jason', 'Jean', 'Jeffrey', 'Jennifer', 'Jeremy', 'Jerry', 'Jessica', 'Joan', 'John', 'Johnny',
            'Jonathan', 'Jordan', 'Jose', 'Joseph', 'Joshua', 'Joyce', 'Juan', 'Judith', 'Judy', 'Julia',
            'Julie', 'Justin', 'Karen', 'Katherine', 'Kathleen', 'Kathryn', 'Kathy', 'Keith', 'Kelly', 'Kenneth',
            'Kevin', 'Kimberly', 'Kyle', 'Larry', 'Laura', 'Lauren', 'Lawrence', 'Linda', 'Lisa', 'Lori',
            'Louis', 'Louise', 'Lucas', 'Luis', 'Luke', 'Lynn', 'Madison', 'Margaret', 'Maria', 'Marie',
            'Marilyn', 'Mark', 'Martha', 'Mary', 'Matthew', 'Megan', 'Melissa', 'Michael', 'Michelle', 'Nancy',
            'Natalie', 'Nathan', 'Nicholas', 'Nicole', 'Noah', 'Norma', 'Olivia', 'Pamela', 'Patricia', 'Patrick',
            'Paul', 'Paula', 'Peter', 'Philip', 'Rachel', 'Ralph', 'Randy', 'Raymond', 'Rebecca', 'Richard',
            'Robert', 'Roger', 'Ronald', 'Rose', 'Roy', 'Russell', 'Ruth', 'Ryan', 'Samantha', 'Samuel',
            'Sandra', 'Sara', 'Sarah', 'Scott', 'Sean', 'Sharon', 'Shirley', 'Sophia', 'Stephanie', 'Stephen',
            'Steven', 'Susan', 'Tammy', 'Teresa', 'Terry', 'Theresa', 'Thomas', 'Timothy', 'Tyler', 'Victoria',
            'Vincent', 'Virginia', 'Walter', 'Wayne', 'William', 'Willie', 'Zachary', 'Zoe', 'Aaron', 'Abigail',
            'Adam', 'Adrian', 'Aiden', 'Alex', 'Alexander', 'Alexis', 'Alice', 'Allison', 'Amanda', 'Amber',
            'Amy', 'Andrea', 'Andrew', 'Angela', 'Anna', 'Anthony', 'Ashley', 'Austin', 'Ava', 'Benjamin',
            'Beth', 'Blake', 'Brandon', 'Brenda', 'Brian', 'Brittany', 'Bruce', 'Bryan', 'Caleb', 'Cameron',
            'Carl', 'Carol', 'Carolyn', 'Catherine', 'Charles', 'Charlotte', 'Cheryl', 'Christian', 'Christina', 'Christopher',
            'Cindy', 'Claire', 'Clarence', 'Cody', 'Colin', 'Colleen', 'Connor', 'Courtney', 'Craig', 'Crystal',
            'Cynthia', 'Daniel', 'Danielle', 'David', 'Deborah', 'Debra', 'Denise', 'Dennis', 'Diana', 'Diane'
        ];

        $lastNames = [
            'Adams', 'Allen', 'Anderson', 'Bailey', 'Baker', 'Barnes', 'Bell', 'Bennett', 'Brooks', 'Brown',
            'Butler', 'Campbell', 'Carter', 'Clark', 'Coleman', 'Collins', 'Cook', 'Cooper', 'Cox', 'Davis',
            'Edwards', 'Evans', 'Foster', 'Garcia', 'Gonzalez', 'Gray', 'Green', 'Griffin', 'Hall', 'Harris',
            'Henderson', 'Hill', 'Howard', 'Hughes', 'Jackson', 'Johnson', 'Jones', 'Kelly', 'King', 'Lee',
            'Lewis', 'Long', 'Lopez', 'Martin', 'Martinez', 'Miller', 'Mitchell', 'Moore', 'Morgan', 'Murphy',
            'Nelson', 'Parker', 'Patterson', 'Perez', 'Peterson', 'Phillips', 'Powell', 'Price', 'Ramirez', 'Reed',
            'Richardson', 'Rivera', 'Roberts', 'Robinson', 'Rodriguez', 'Rogers', 'Ross', 'Russell', 'Sanchez', 'Scott',
            'Simmons', 'Smith', 'Stewart', 'Taylor', 'Thomas', 'Thompson', 'Torres', 'Turner', 'Walker', 'Ward',
            'Washington', 'Watson', 'White', 'Williams', 'Wilson', 'Wood', 'Wright', 'Young', 'Adams', 'Allen',
            'Anderson', 'Bailey', 'Baker', 'Barnes', 'Bell', 'Bennett', 'Brooks', 'Brown', 'Butler', 'Campbell',
            'Carter', 'Clark', 'Coleman', 'Collins', 'Cook', 'Cooper', 'Cox', 'Davis', 'Edwards', 'Evans',
            'Foster', 'Garcia', 'Gonzalez', 'Gray', 'Green', 'Griffin', 'Hall', 'Harris', 'Henderson', 'Hill',
            'Howard', 'Hughes', 'Jackson', 'Johnson', 'Jones', 'Kelly', 'King', 'Lee', 'Lewis', 'Long',
            'Lopez', 'Martin', 'Martinez', 'Miller', 'Mitchell', 'Moore', 'Morgan', 'Murphy', 'Nelson', 'Parker',
            'Patterson', 'Perez', 'Peterson', 'Phillips', 'Powell', 'Price', 'Ramirez', 'Reed', 'Richardson', 'Rivera',
            'Roberts', 'Robinson', 'Rodriguez', 'Rogers', 'Ross', 'Russell', 'Sanchez', 'Scott', 'Simmons', 'Smith',
            'Stewart', 'Taylor', 'Thomas', 'Thompson', 'Torres', 'Turner', 'Walker', 'Ward', 'Washington', 'Watson',
            'White', 'Williams', 'Wilson', 'Wood', 'Wright', 'Young', 'Adams', 'Allen', 'Anderson', 'Bailey'
        ];

        $countries = [
            ['code' => '+1', 'name' => 'USA'],
            ['code' => '+44', 'name' => 'UK'],
            ['code' => '+91', 'name' => 'India'],
            ['code' => '+49', 'name' => 'Germany'],
            ['code' => '+86', 'name' => 'China'],
            ['code' => '+61', 'name' => 'Australia'],
            ['code' => '+34', 'name' => 'Spain'],
            ['code' => '+353', 'name' => 'Ireland'],
            ['code' => '+82', 'name' => 'South Korea'],
            ['code' => '+52', 'name' => 'Mexico'],
            ['code' => '+33', 'name' => 'France'],
            ['code' => '+39', 'name' => 'Italy'],
            ['code' => '+81', 'name' => 'Japan'],
            ['code' => '+55', 'name' => 'Brazil'],
            ['code' => '+7', 'name' => 'Russia'],
            ['code' => '+31', 'name' => 'Netherlands'],
            ['code' => '+46', 'name' => 'Sweden'],
            ['code' => '+47', 'name' => 'Norway'],
            ['code' => '+45', 'name' => 'Denmark'],
            ['code' => '+41', 'name' => 'Switzerland']
        ];

        $statuses = ['active', 'pending', 'blocked'];
        $students = [];

        for ($i = 1; $i <= 200; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $country = $countries[array_rand($countries)];
            $status = $statuses[array_rand($statuses)];
            
            // Generate unique email and phone
            $email = strtolower($firstName . '.' . $lastName . '.' . $i . '.' . $timestamp . '@pagination.test.com');
            $phone = $i . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            
            // Random verification status
            $emailVerified = rand(0, 1) ? now() : null;
            $phoneVerified = rand(0, 1) ? now() : null;
            
            // Random creation date (last 90 days)
            $daysAgo = rand(0, 90);
            $hoursAgo = rand(0, 23);
            $minutesAgo = rand(0, 59);
            $createdAt = now()->subDays($daysAgo)->subHours($hoursAgo)->subMinutes($minutesAgo);

            $students[] = [
                'name' => $firstName . ' ' . $lastName,
                'email' => $email,
                'phone' => $phone,
                'country_code' => $country['code'],
                'status' => $status,
                'role_id' => Role::STUDENT->value,
                'email_verified_at' => $emailVerified,
                'phone_verified_at' => $phoneVerified,
                'created_at' => $createdAt,
            ];
        }

        return $students;
    }
}

