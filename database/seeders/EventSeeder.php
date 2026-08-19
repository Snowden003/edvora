<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'title'            => 'AI & Machine Learning Summit',
                'description'      => 'A deep dive into the latest advancements in AI and machine learning, featuring talks from industry leaders.',
                'type'             => 'conference',
                'location'         => 'Online / Virtual',
                'start_date'       => now()->addDays(10),
                'end_date'         => now()->addDays(10)->addHours(4),
                'max_attendees'    => 500,
                'registered_count' => 312,
            ],
            [
                'title'            => 'Web Development Workshop',
                'description'      => 'Hands-on workshop covering modern web development techniques with React and Node.js.',
                'type'             => 'workshop',
                'location'         => 'Kabul Tech Hub, Room 3',
                'start_date'       => now()->addDays(15),
                'end_date'         => now()->addDays(15)->addHours(3),
                'max_attendees'    => 50,
                'registered_count' => 38,
            ],
            [
                'title'            => 'Career Guidance Webinar',
                'description'      => 'Join industry professionals for a Q&A session about career paths in tech.',
                'type'             => 'webinar',
                'location'         => 'Online / Virtual',
                'start_date'       => now()->addDays(20),
                'end_date'         => now()->addDays(20)->addHours(2),
                'max_attendees'    => 200,
                'registered_count' => 145,
            ],
            [
                'title'            => 'Cybersecurity Bootcamp',
                'description'      => 'Intensive 1-day bootcamp on ethical hacking and network security fundamentals.',
                'type'             => 'workshop',
                'location'         => 'Edvora Training Center',
                'start_date'       => now()->addDays(25),
                'end_date'         => now()->addDays(25)->addHours(8),
                'max_attendees'    => 30,
                'registered_count' => 28,
            ],
        ];

        foreach ($events as $data) {
            Event::firstOrCreate(
                ['slug' => Str::slug($data['title'])],
                $data
            );
        }
    }
}
