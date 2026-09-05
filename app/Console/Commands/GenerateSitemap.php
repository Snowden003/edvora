<?php

namespace App\Console\Commands;

use App\Http\Controllers\SitemapController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate {--save : Also write a static public/sitemap.xml file}';

    protected $description = 'Generate and refresh the dynamic XML sitemap cache, with optional file output';

    public function handle(): int
    {
        $this->info('Generating sitemap...');

        $controller = new SitemapController();
        $xml = $controller->generateXml();

        // Warm cache for 2 hours
        Cache::put('sitemap_xml_content', $xml, now()->addHours(2));
        $this->info('✓ Sitemap cached successfully.');

        // Count URLs in generated XML
        $urlCount = substr_count($xml, '<url>');
        $this->info("✓ Generated sitemap with {$urlCount} URL(s).");

        if ($this->option('save')) {
            $filePath = public_path('sitemap.xml');
            file_put_contents($filePath, $xml);
            $this->info("✓ Written static sitemap to {$filePath}");
        }

        return self::SUCCESS;
    }
}
