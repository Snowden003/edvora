<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Programming',         'slug' => 'programming',         'icon' => 'bi-code-slash',   'description' => 'Learn programming languages and software development'],
            ['name' => 'Web Development',     'slug' => 'web-development',     'icon' => 'bi-globe',        'description' => 'Master web technologies and frameworks'],
            ['name' => 'Cyber Security',      'slug' => 'cyber-security',      'icon' => 'bi-shield-lock',  'description' => 'Learn security practices and ethical hacking'],
            ['name' => 'Data Science',        'slug' => 'data-science',        'icon' => 'bi-graph-up',     'description' => 'Explore data analysis and machine learning'],
            ['name' => 'Mobile Development',  'slug' => 'mobile-development',  'icon' => 'bi-phone',        'description' => 'Build mobile applications for iOS and Android'],
            ['name' => 'Design',              'slug' => 'design',              'icon' => 'bi-palette',      'description' => 'UI/UX design and graphic design courses'],
            ['name' => 'DevOps',              'slug' => 'devops',              'icon' => 'bi-server',       'description' => 'Learn deployment and infrastructure management'],
            ['name' => 'Database',            'slug' => 'database',            'icon' => 'bi-database',     'description' => 'Master database design and management'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
