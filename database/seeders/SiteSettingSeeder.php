<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Company Info
            ['key' => 'company_name',         'label' => 'Company Name',         'group' => 'company', 'type' => 'text',     'value' => 'Edvora Tech'],
            ['key' => 'company_tagline',      'label' => 'Tagline',              'group' => 'company', 'type' => 'text',     'value' => 'Empowering learners worldwide with quality education and innovative teaching methods.'],
            ['key' => 'company_description',  'label' => 'Description',          'group' => 'company', 'type' => 'textarea', 'value' => 'Edvora Tech is a leading online education platform dedicated to providing world-class learning experiences.'],
            ['key' => 'company_email',        'label' => 'Email',                'group' => 'company', 'type' => 'text',     'value' => 'info@edvora.com'],
            ['key' => 'company_phone',        'label' => 'Phone',                'group' => 'company', 'type' => 'text',     'value' => ''],
            ['key' => 'company_address',      'label' => 'Address',              'group' => 'company', 'type' => 'textarea', 'value' => ''],
            ['key' => 'company_logo',         'label' => 'Logo',                 'group' => 'company', 'type' => 'image',    'value' => ''],
            ['key' => 'company_founded_year', 'label' => 'Founded Year',         'group' => 'company', 'type' => 'text',     'value' => '2020'],

            // Social Links
            ['key' => 'social_facebook',  'label' => 'Facebook URL',   'group' => 'social', 'type' => 'url', 'value' => ''],
            ['key' => 'social_twitter',   'label' => 'Twitter/X URL',  'group' => 'social', 'type' => 'url', 'value' => ''],
            ['key' => 'social_instagram', 'label' => 'Instagram URL',  'group' => 'social', 'type' => 'url', 'value' => ''],
            ['key' => 'social_linkedin',  'label' => 'LinkedIn URL',   'group' => 'social', 'type' => 'url', 'value' => ''],
            ['key' => 'social_youtube',   'label' => 'YouTube URL',    'group' => 'social', 'type' => 'url', 'value' => ''],

            // SEO
            ['key' => 'seo_meta_title',       'label' => 'Default Meta Title',       'group' => 'seo', 'type' => 'text',     'value' => 'Edvora Tech - Online Education Platform'],
            ['key' => 'seo_meta_description', 'label' => 'Default Meta Description', 'group' => 'seo', 'type' => 'textarea', 'value' => 'Empowering learners worldwide with quality education and innovative teaching methods.'],

            // Footer
            ['key' => 'footer_copyright',      'label' => 'Copyright Text',         'group' => 'footer', 'type' => 'text', 'value' => 'Edvora Tech. All rights reserved.'],
            ['key' => 'footer_newsletter_text', 'label' => 'Newsletter Description', 'group' => 'footer', 'type' => 'text', 'value' => 'Get the latest updates on courses, events, and educational content.'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
