<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('home') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('courses.index') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    @foreach ($courses as $course)
        <url>
            <loc>{{ route('courses.detail', $course->slug) }}</loc>
            <lastmod>{{ $course->updated_at->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
</urlset>
