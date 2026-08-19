<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Donation;
use Carbon\Carbon;

class DonationSeeder extends Seeder
{
    public function run(): void
    {
        $donations = [
            [
                'name' => 'Hossein Rezaei',
                'email' => 'hossein.rezaei@example.com',
                'phone' => '+98 912 345 6789',
                'amount' => 240000,
                'message' => 'Investing in the future of youth is investing in the future of the country',
                'show_name' => true,
                'status' => 'completed',
                'donated_at' => Carbon::now()->subMonths(3),
            ],
            [
                'name' => 'Maryam Ahmadi',
                'email' => 'maryam.ahmadi@example.com',
                'phone' => '+98 913 456 7890',
                'amount' => 50000,
                'message' => 'Supporting education for a better tomorrow',
                'show_name' => true,
                'status' => 'completed',
                'donated_at' => Carbon::now()->subMonths(1),
            ],
            [
                'name' => 'Ali Mohammad',
                'email' => 'ali.m@example.com',
                'phone' => '+98 914 567 8901',
                'amount' => 25000,
                'message' => 'Happy to support tech education',
                'show_name' => true,
                'status' => 'completed',
                'donated_at' => Carbon::now()->subDays(15),
            ],
            [
                'name' => 'Sara Karimi',
                'email' => 'sara.k@example.com',
                'phone' => '+98 915 678 9012',
                'amount' => 15000,
                'message' => null,
                'show_name' => true,
                'status' => 'completed',
                'donated_at' => Carbon::now()->subDays(7),
            ],
            [
                'name' => 'Reza Nouri',
                'email' => 'reza.n@example.com',
                'phone' => '+98 916 789 0123',
                'amount' => 10000,
                'message' => 'Keep up the great work!',
                'show_name' => false,
                'status' => 'completed',
                'donated_at' => Carbon::now()->subDays(2),
            ],
        ];

        foreach ($donations as $donation) {
            Donation::create($donation);
        }

        $this->command->info('✅ Donations seeded successfully!');
    }
}
