<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdminFileManagerController extends Controller
{
    protected string $disk = 'public';

    /**
     * Display a listing of files and directories.
     */
    public function index(Request $request): Response
    {
        $rawPath = (string) $request->input('path', '');
        $currentPath = $this->sanitizePath($rawPath);
        $search = trim((string) $request->input('search', ''));
        $typeFilter = (string) $request->input('type', 'all'); // all, image, document, video, audio, archive
        $sortBy = (string) $request->input('sort', 'date'); // name, size, date, type
        $sortDir = (string) $request->input('order', 'desc'); // asc, desc
        $viewMode = (string) $request->input('mode', 'folder'); // folder or all

        $storage = Storage::disk($this->disk);

        // Calculate global statistics across the entire public disk
        $allDiskFiles = $storage->allFiles();
        $totalSizeBytes = 0;
        $categoryCounts = [
            'image' => 0,
            'document' => 0,
            'video' => 0,
            'audio' => 0,
            'archive' => 0,
            'other' => 0,
        ];

        foreach ($allDiskFiles as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $cat = $this->categorizeExtension($ext);
            $categoryCounts[$cat]++;
            try {
                $totalSizeBytes += $storage->size($file);
            } catch (\Exception $e) {
                // Ignore unreadable file size
            }
        }

        $stats = [
            'total_files' => count($allDiskFiles),
            'total_size_bytes' => $totalSizeBytes,
            'total_size_formatted' => $this->formatBytes($totalSizeBytes),
            'categories' => $categoryCounts,
        ];

        // Folders list (only in folder mode)
        $folders = [];
        if ($viewMode !== 'all' && empty($search)) {
            $rawDirectories = $storage->directories($currentPath);
            foreach ($rawDirectories as $dir) {
                $dirName = basename($dir);
                // Count items inside this folder
                $itemsCount = count($storage->files($dir)) + count($storage->directories($dir));
                $folders[] = [
                    'name' => $dirName,
                    'path' => $dir,
                    'items_count' => $itemsCount,
                ];
            }

            usort($folders, fn($a, $b) => strnatcasecmp($a['name'], $b['name']));
        }

        // Files list
        if ($viewMode === 'all' || !empty($search)) {
            // If searching or in 'all' view mode, scan all files recursively
            $filesList = $allDiskFiles;
        } else {
            // Normal folder navigation
            $filesList = $storage->files($currentPath);
        }

        $mappedFiles = [];
        foreach ($filesList as $filePath) {
            $fileName = basename($filePath);

            // Search filter
            if (!empty($search) && !str_contains(mb_strtolower($fileName), mb_strtolower($search)) && !str_contains(mb_strtolower($filePath), mb_strtolower($search))) {
                continue;
            }

            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $cat = $this->categorizeExtension($ext);

            // Category/Type filter
            if ($typeFilter !== 'all' && $cat !== $typeFilter) {
                continue;
            }

            try {
                $sizeBytes = $storage->size($filePath);
                $lastModifiedTimestamp = $storage->lastModified($filePath);
            } catch (\Exception $e) {
                $sizeBytes = 0;
                $lastModifiedTimestamp = time();
            }

            $mime = $storage->mimeType($filePath) ?: 'application/octet-stream';
            $url = $storage->url($filePath);

            $mappedFiles[] = [
                'name' => $fileName,
                'path' => $filePath,
                'url' => $url,
                'size_bytes' => $sizeBytes,
                'size_formatted' => $this->formatBytes($sizeBytes),
                'last_modified' => Carbon::createFromTimestamp($lastModifiedTimestamp)->diffForHumans(),
                'last_modified_date' => Carbon::createFromTimestamp($lastModifiedTimestamp)->format('Y-m-d H:i'),
                'timestamp' => $lastModifiedTimestamp,
                'extension' => $ext,
                'mime_type' => $mime,
                'type' => $cat,
                'is_image' => ($cat === 'image'),
                'folder' => dirname($filePath) === '.' ? '' : dirname($filePath),
            ];
        }

        // Sorting
        usort($mappedFiles, function ($a, $b) use ($sortBy, $sortDir) {
            $factor = ($sortDir === 'asc') ? 1 : -1;
            return match ($sortBy) {
                'name' => $factor * strnatcasecmp($a['name'], $b['name']),
                'size' => $factor * ($a['size_bytes'] <=> $b['size_bytes']),
                'type' => $factor * strcmp($a['type'], $b['type']),
                default => $factor * ($a['timestamp'] <=> $b['timestamp']),
            };
        });

        // Breadcrumbs
        $breadcrumbs = [
            ['name' => 'پوشه اصلی (Root)', 'path' => ''],
        ];

        if (!empty($currentPath)) {
            $parts = explode('/', $currentPath);
            $accumulated = '';
            foreach ($parts as $part) {
                if ($part === '') continue;
                $accumulated = $accumulated ? $accumulated . '/' . $part : $part;
                $breadcrumbs[] = [
                    'name' => $part,
                    'path' => $accumulated,
                ];
            }
        }

        return Inertia::render('Admin/FileManager/Index', [
            'files' => $mappedFiles,
            'folders' => $folders,
            'breadcrumbs' => $breadcrumbs,
            'currentPath' => $currentPath,
            'stats' => $stats,
            'filters' => [
                'path' => $currentPath,
                'search' => $search,
                'type' => $typeFilter,
                'sort' => $sortBy,
                'order' => $sortDir,
                'mode' => $viewMode,
            ],
        ]);
    }

    /**
     * Upload one or multiple files into a directory.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'files' => 'required',
            'files.*' => 'file|max:102400', // Max 100MB per file
            'target_path' => 'nullable|string',
        ]);

        $rawPath = (string) $request->input('target_path', '');
        $targetPath = $this->sanitizePath($rawPath);
        $storage = Storage::disk($this->disk);

        $uploadedCount = 0;
        $files = $request->file('files');
        if (!is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            if (!$file->isValid()) {
                continue;
            }

            $originalName = $file->getClientOriginalName();
            $safeName = $this->sanitizeFileName($originalName);

            $destination = $targetPath ? $targetPath . '/' . $safeName : $safeName;

            // If file already exists, generate unique name
            if ($storage->exists($destination)) {
                $nameOnly = pathinfo($safeName, PATHINFO_FILENAME);
                $ext = pathinfo($safeName, PATHINFO_EXTENSION);
                $safeName = $nameOnly . '_' . time() . '_' . Str::random(4) . ($ext ? '.' . $ext : '');
                $destination = $targetPath ? $targetPath . '/' . $safeName : $safeName;
            }

            $storage->putFileAs($targetPath ?: '', $file, $safeName);
            $uploadedCount++;
        }

        return back()->with('success', "تعداد {$uploadedCount} فایل با موفقیت در پوشه ذخیره شد.");
    }

    /**
     * Create a new folder.
     */
    public function createFolder(Request $request)
    {
        $request->validate([
            'folder_name' => 'required|string|max:100|regex:/^[a-zA-Z0-9_\-\x{0600}-\x{06FF} ]+$/u',
            'target_path' => 'nullable|string',
        ]);

        $folderName = trim($request->input('folder_name'));
        $folderName = Str::slug($folderName, '-');
        if (empty($folderName)) {
            $folderName = 'folder_' . time();
        }

        $rawPath = (string) $request->input('target_path', '');
        $targetPath = $this->sanitizePath($rawPath);

        $fullPath = $targetPath ? $targetPath . '/' . $folderName : $folderName;
        $storage = Storage::disk($this->disk);

        if ($storage->exists($fullPath)) {
            return back()->with('error', 'پوشه‌ای با این نام قبلاً وجود دارد.');
        }

        $storage->makeDirectory($fullPath);

        return back()->with('success', "پوشه «{$folderName}» با موفقیت ایجاد شد.");
    }

    /**
     * Delete a single file or directory.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $path = $this->sanitizePath($request->input('path'));
        $storage = Storage::disk($this->disk);

        if (!$storage->exists($path) && !in_array($path, $storage->directories(dirname($path) === '.' ? '' : dirname($path)))) {
            return back()->with('error', 'فایل یا پوشه مورد نظر یافت نشد.');
        }

        // Check if directory
        if (is_dir($storage->path($path))) {
            $storage->deleteDirectory($path);
            return back()->with('success', 'پوشه و تمام محتویات آن با موفقیت حذف شدند.');
        }

        $storage->delete($path);
        return back()->with('success', 'فایل با موفقیت حذف شد.');
    }

    /**
     * Delete multiple files or folders in bulk.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'paths' => 'required|array',
            'paths.*' => 'string',
        ]);

        $paths = $request->input('paths', []);
        $storage = Storage::disk($this->disk);
        $deletedCount = 0;

        foreach ($paths as $raw) {
            $path = $this->sanitizePath($raw);
            if ($storage->exists($path)) {
                if (is_dir($storage->path($path))) {
                    $storage->deleteDirectory($path);
                } else {
                    $storage->delete($path);
                }
                $deletedCount++;
            }
        }

        return back()->with('success', "تعداد {$deletedCount} آیتم با موفقیت حذف شدند.");
    }

    /**
     * Prevent directory traversal attacks by sanitizing path.
     */
    protected function sanitizePath(string $path): string
    {
        $path = str_replace(['..', '\\'], ['', '/'], $path);
        $path = preg_replace('#/+#', '/', $path);
        return trim($path, '/');
    }

    /**
     * Clean and sanitize file names.
     */
    protected function sanitizeFileName(string $name): string
    {
        $name = str_replace(['..', '/', '\\'], '', $name);
        return preg_replace('/[^\w\.\-\x{0600}-\x{06FF}]/u', '_', $name);
    }

    /**
     * Categorize extension into standard groups.
     */
    protected function categorizeExtension(string $ext): string
    {
        return match ($ext) {
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'avif', 'bmp', 'ico' => 'image',
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'rtf', 'md' => 'document',
            'mp4', 'webm', 'mkv', 'avi', 'mov', 'flv' => 'video',
            'mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac' => 'audio',
            'zip', 'rar', '7z', 'tar', 'gz' => 'archive',
            default => 'other',
        };
    }

    /**
     * Format raw bytes into human readable format.
     */
    protected function formatBytes(int $bytes, int $precision = 1): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);

        return round($bytes / (1024 ** $power), $precision) . ' ' . $units[$power];
    }
}
