<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'College of Graduate Studies',
                'description' => 'Graduate programs and research',
            ],
            [
                'name' => 'College of Nursing and Health Sciences',
                'description' => 'Nursing and health sciences programs',
            ],
            [
                'name' => 'College of Engineering',
                'description' => 'Engineering and technology programs',
            ],
            [
                'name' => 'College of Education',
                'description' => 'Teacher education and educational programs',
            ],
            [
                'name' => 'College of Arts and Sciences',
                'description' => 'Liberal arts and science programs',
            ],
            [
                'name' => 'College of Industrial Technology',
                'description' => 'Industrial technology and vocational programs',
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
