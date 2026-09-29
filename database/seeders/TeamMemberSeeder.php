<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TeamMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (TeamMember::count() > 0) {
            return;
        }

        $members = [
            [
                'name'        => 'مهندس آرش سپهری',
                'role_title'  => 'مدیر ارشد فنی و معمار سیستم',
                'department'  => 'دپارتمان فنی و هوش مصنوعی',
                'email'       => 'arash@edvora.org',
                'phone'       => '+98 912 111 2233',
                'telegram'    => 'arash_sepehri',
                'linkedin'    => 'https://linkedin.com/in/arash-sepehri',
                'github'      => 'https://github.com/arash-sepehri',
                'bio'         => 'رهبر تیم مهندسی ادورا تک، متخصص سیستم‌های توزیع‌شده، پلتفرم‌های آموزشی و هوش مصنوعی.',
                'employee_id' => 'EDV-1001',
                'status'      => 'active',
                'avatar'      => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&auto=format&fit=crop&q=80',
            ],
            [
                'name'        => 'دکتر زهرا کریمی',
                'role_title'  => 'سرپرست آموزش و برنامه‌ریزی درسی',
                'department'  => 'دپارتمان آکادمیک و آموزش',
                'email'       => 'zahra.karimi@edvora.org',
                'phone'       => '+98 912 222 3344',
                'telegram'    => 'zahra_karimi_edu',
                'linkedin'    => 'https://linkedin.com/in/zahra-karimi',
                'github'      => null,
                'bio'         => 'دکترای تکنولوژی آموزشی، مسئول ارزیابی سرفصل‌ها و نظارت بر دوره‌های تعاملی آنلاین.',
                'employee_id' => 'EDV-1002',
                'status'      => 'active',
                'avatar'      => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=400&auto=format&fit=crop&q=80',
            ],
            [
                'name'        => 'نیما رادمنش',
                'role_title'  => 'طراح ارشد تجربه کاربری (Lead UI/UX)',
                'department'  => 'دپارتمان طراحی محصول',
                'email'       => 'nima.rad@edvora.org',
                'phone'       => '+98 912 333 4455',
                'telegram'    => 'nima_radmanesh',
                'linkedin'    => 'https://linkedin.com/in/nima-radmanesh',
                'github'      => 'https://github.com/nima-rad',
                'bio'         => 'طراح دیزاین سیستم ادورا تک با تمرکز بر دسترسی‌پذیری و رابط کاربری مدرن نسل جدید.',
                'employee_id' => 'EDV-1003',
                'status'      => 'active',
                'avatar'      => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&auto=format&fit=crop&q=80',
            ],
        ];

        foreach ($members as $index => $data) {
            TeamMember::create(array_merge($data, [
                'uuid'        => (string) Str::uuid(),
                'order'       => $index,
                'joined_date' => now()->subMonths(6 - $index)->toDateString(),
            ]));
        }
    }
}
