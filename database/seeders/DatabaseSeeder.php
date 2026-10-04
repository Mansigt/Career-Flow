<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create primary portfolio demo user
        $demoUser = User::create([
            'name'     => 'Demo Developer',
            'email'    => 'demo@careerflow.test',
            'password' => Hash::make('password123'),
        ]);

        $today = Carbon::today();

        // 2. Seed realistic applications for Demo User
        $demoApplications = [
            [
                'company'        => 'Acme Technologies',
                'position'       => 'Junior PHP / Laravel Developer',
                'location'       => 'Remote',
                'job_type'       => 'Full-time',
                'salary'         => 75000.00,
                'applied_date'   => $today->copy()->subDays(10)->toDateString(),
                'follow_up_date' => $today->toDateString(), // Due today!
                'status'         => Application::STATUS_INTERVIEW,
                'job_url'        => 'https://example.com/careers/php-dev',
                'notes'          => 'Technical interview completed on Tuesday. Recruiter mentioned positive feedback on the coding test; follow up regarding final panel interview.',
            ],
            [
                'company'        => 'CloudScale Solutions',
                'position'       => 'Backend Web Developer',
                'location'       => 'New York, NY',
                'job_type'       => 'Full-time',
                'salary'         => 85000.00,
                'applied_date'   => $today->copy()->subDays(14)->toDateString(),
                'follow_up_date' => $today->copy()->subDays(2)->toDateString(), // Overdue reminder!
                'status'         => Application::STATUS_SHORTLISTED,
                'job_url'        => 'https://example.com/jobs/backend-dev',
                'notes'          => 'Initial resume screening passed. Need to follow up with HR coordinator Sarah about technical assessment schedule.',
            ],
            [
                'company'        => 'Apex Digital Labs',
                'position'       => 'Full Stack Engineer',
                'location'       => 'Austin, TX (Hybrid)',
                'job_type'       => 'Full-time',
                'salary'         => 92000.00,
                'applied_date'   => $today->copy()->subDays(3)->toDateString(),
                'follow_up_date' => $today->copy()->addDays(5)->toDateString(),
                'status'         => Application::STATUS_APPLIED,
                'job_url'        => 'https://example.com/jobs/fullstack',
                'notes'          => 'Applied directly on company careers portal. Tailored resume highlighting REST API design and relational database optimization.',
            ],
            [
                'company'        => 'FinTech Dynamics',
                'position'       => 'Software Engineering Intern',
                'location'       => 'Remote',
                'job_type'       => 'Internship',
                'salary'         => 45000.00,
                'applied_date'   => $today->copy()->subDays(20)->toDateString(),
                'follow_up_date' => null,
                'status'         => Application::STATUS_SELECTED,
                'job_url'        => 'https://example.com/internships/software',
                'notes'          => 'Received official offer letter! Compensation confirmed with sign-on stipend. Start date scheduled for next month.',
            ],
            [
                'company'        => 'Nexus Cyber Systems',
                'position'       => 'PHP / MVC Contract Developer',
                'location'       => 'San Francisco, CA',
                'job_type'       => 'Contract',
                'salary'         => 65000.00,
                'applied_date'   => $today->copy()->subDays(25)->toDateString(),
                'follow_up_date' => null,
                'status'         => Application::STATUS_REJECTED,
                'job_url'        => 'https://example.com/careers/contractor',
                'notes'          => 'Role filled internally. Received polite rejection email. Encouraged to apply for future mid-level openings.',
            ],
            [
                'company'        => 'Global Logic Group',
                'position'       => 'Associate Software Engineer',
                'location'       => 'Chicago, IL',
                'job_type'       => 'Part-time',
                'salary'         => 40000.00,
                'applied_date'   => $today->copy()->subDays(5)->toDateString(),
                'follow_up_date' => $today->copy()->addDays(3)->toDateString(),
                'status'         => Application::STATUS_APPLIED,
                'job_url'        => 'https://example.com/jobs/associate-eng',
                'notes'          => 'Application acknowledged by automated system. Waiting for hiring manager review.',
            ],
        ];

        foreach ($demoApplications as $appData) {
            $demoUser->applications()->create($appData);
        }

        // 3. Create second user to verify cross-user isolation and authorization
        $secondUser = User::create([
            'name'     => 'Alex Rivera',
            'email'    => 'other@careerflow.test',
            'password' => Hash::make('password123'),
        ]);

        $secondUser->applications()->create([
            'company'        => 'Secret Corp Labs',
            'position'       => 'Security Analyst',
            'location'       => 'Seattle, WA',
            'job_type'       => 'Full-time',
            'salary'         => 110000.00,
            'applied_date'   => $today->copy()->subDays(4)->toDateString(),
            'follow_up_date' => $today->copy()->addDays(7)->toDateString(),
            'status'         => Application::STATUS_APPLIED,
            'job_url'        => 'https://example.com/jobs/security',
            'notes'          => 'Application submitted for Alex Rivera. Demo user must NEVER see or edit this application.',
        ]);
    }
}
