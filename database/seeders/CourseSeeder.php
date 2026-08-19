<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::where('role', 'teacher')->first();

        if (!$teacher) {
            $teacher = User::create([
                'name'     => 'Dr. Sarah Johnson',
                'email'    => 'sarah@edvora.tech',
                'password' => bcrypt('password'),
                'role'     => 'teacher',
                'status'   => 'active',
            ]);
        }

        $courses = [
            [
                'category' => 'programming',
                'title'    => 'Complete Python Programming Masterclass',
                'description' => 'Learn Python from scratch with hands-on projects and real-world applications. Covers fundamentals, OOP, file handling, and popular libraries.',
                'thumbnail'   => 'https://images.unsplash.com/photo-1526379095098-d400fd0bf935?w=600&h=400&fit=crop',
                'level'       => 'beginner',
                'duration_hours' => 40,
                'enrolled_count' => 1250,
                'rating'         => 4.80,
                'total_reviews'  => 320,
            ],
            [
                'category' => 'web-development',
                'title'    => 'Modern Web Development with React & Node.js',
                'description' => 'Build full-stack web applications using React, Node.js, and MongoDB. From REST APIs to deployment.',
                'thumbnail'   => 'https://images.unsplash.com/photo-1593720213428-28a5b9e94613?w=600&h=400&fit=crop',
                'level'       => 'intermediate',
                'duration_hours' => 60,
                'enrolled_count' => 890,
                'rating'         => 4.90,
                'total_reviews'  => 245,
            ],
            [
                'category' => 'cyber-security',
                'title'    => 'Ethical Hacking & Penetration Testing',
                'description' => 'Master cybersecurity fundamentals and ethical hacking techniques. Learn to identify and fix vulnerabilities.',
                'thumbnail'   => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=600&h=400&fit=crop',
                'level'       => 'advanced',
                'duration_hours' => 80,
                'enrolled_count' => 650,
                'rating'         => 4.70,
                'total_reviews'  => 180,
            ],
            [
                'category' => 'data-science',
                'title'    => 'Data Science & Machine Learning with Python',
                'description' => 'Learn data analysis, visualization, and machine learning algorithms using Python, Pandas, and Scikit-learn.',
                'thumbnail'   => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600&h=400&fit=crop',
                'level'       => 'intermediate',
                'duration_hours' => 70,
                'enrolled_count' => 720,
                'rating'         => 4.80,
                'total_reviews'  => 210,
            ],
            [
                'category' => 'web-development',
                'title'    => 'Laravel PHP Framework Complete Guide',
                'description' => 'Master Laravel framework and build modern web applications. Covers routing, Eloquent ORM, Blade, queues, and APIs.',
                'thumbnail'   => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=600&h=400&fit=crop',
                'level'       => 'intermediate',
                'duration_hours' => 50,
                'enrolled_count' => 580,
                'rating'         => 4.60,
                'total_reviews'  => 150,
            ],
            [
                'category' => 'mobile-development',
                'title'    => 'Flutter Mobile App Development',
                'description' => 'Build beautiful cross-platform mobile apps with Flutter and Dart. From UI design to publishing on App Store and Google Play.',
                'thumbnail'   => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=600&h=400&fit=crop',
                'level'       => 'beginner',
                'duration_hours' => 45,
                'enrolled_count' => 430,
                'rating'         => 4.50,
                'total_reviews'  => 120,
            ],
            [
                'category' => 'devops',
                'title'    => 'Docker & Kubernetes DevOps Bootcamp',
                'description' => 'Learn containerization and orchestration. Build CI/CD pipelines, deploy microservices, and manage cloud infrastructure.',
                'thumbnail'   => 'https://images.unsplash.com/photo-1667372393119-3d4c48d07fc9?w=600&h=400&fit=crop',
                'level'       => 'advanced',
                'duration_hours' => 55,
                'enrolled_count' => 380,
                'rating'         => 4.75,
                'total_reviews'  => 95,
            ],
            [
                'category' => 'database',
                'title'    => 'SQL & Database Design Mastery',
                'description' => 'Master relational database design, SQL queries, optimization, and modern database management with MySQL and PostgreSQL.',
                'thumbnail'   => 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?w=600&h=400&fit=crop',
                'level'       => 'beginner',
                'duration_hours' => 35,
                'enrolled_count' => 510,
                'rating'         => 4.65,
                'total_reviews'  => 140,
            ],
            [
                'category' => 'design',
                'title'    => 'UI/UX Design Fundamentals',
                'description' => 'Learn user-centered design principles, wireframing, prototyping, and create stunning interfaces using Figma.',
                'thumbnail'   => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=600&h=400&fit=crop',
                'level'       => 'beginner',
                'duration_hours' => 30,
                'enrolled_count' => 460,
                'rating'         => 4.55,
                'total_reviews'  => 110,
            ],
            [
                'category' => 'programming',
                'title'    => 'Advanced C++ & System Programming',
                'description' => 'Deep dive into C++ for systems programming, memory management, concurrency, and high-performance applications.',
                'thumbnail'   => 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?w=600&h=400&fit=crop',
                'level'       => 'advanced',
                'duration_hours' => 65,
                'enrolled_count' => 290,
                'rating'         => 4.70,
                'total_reviews'  => 75,
            ],
            [
                'category' => 'cyber-security',
                'title'    => 'Network Security & Cryptography',
                'description' => 'Understand network protocols, encryption algorithms, VPNs, firewalls, and practical network defense strategies.',
                'thumbnail'   => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=600&h=400&fit=crop',
                'level'       => 'intermediate',
                'duration_hours' => 50,
                'enrolled_count' => 340,
                'rating'         => 4.60,
                'total_reviews'  => 88,
            ],
            [
                'category' => 'data-science',
                'title'    => 'Deep Learning & Neural Networks',
                'description' => 'Master deep learning concepts using TensorFlow and PyTorch. Build CNNs, RNNs, and transformer models from scratch.',
                'thumbnail'   => 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=600&h=400&fit=crop',
                'level'       => 'advanced',
                'duration_hours' => 90,
                'enrolled_count' => 410,
                'rating'         => 4.85,
                'total_reviews'  => 130,
            ],
        ];

        foreach ($courses as $data) {
            $category = Category::where('slug', $data['category'])->first();
            if (!$category) {
                continue;
            }

            Course::firstOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    'category_id'    => $category->id,
                    'teacher_id'     => $teacher->id,
                    'title'          => $data['title'],
                    'description'    => $data['description'],
                    'thumbnail'      => $data['thumbnail'],
                    'level'          => $data['level'],
                    'duration_hours' => $data['duration_hours'],
                    'status'         => 'published',
                    'enrolled_count' => $data['enrolled_count'],
                    'rating'         => $data['rating'],
                    'total_reviews'  => $data['total_reviews'],
                ]
            );
        }
    }
}
