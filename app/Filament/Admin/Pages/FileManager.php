<?php

namespace App\Filament\Admin\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseDocument;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileManager extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = 'File Manager';
    protected static ?string $navigationGroup = 'System';
    protected static ?string $title = 'File Manager';
    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.admin.pages.file-manager';

    public string $currentFolder = '__all__';
    public string $viewMode = 'grid';
    public string $search = '';
    public bool $orphansOnly = false;

    public static array $folders = [
        'avatars'          => ['label' => 'Avatars',          'icon' => 'person'],
        'covers'           => ['label' => 'Cover Images',     'icon' => 'image'],
        'courses'          => ['label' => 'Course Images',    'icon' => 'book'],
        'instructors'      => ['label' => 'Instructors',      'icon' => 'academic-cap'],
        'cvs'              => ['label' => 'CVs',              'icon' => 'document'],
        'course-documents' => ['label' => 'Course Documents', 'icon' => 'document-text'],
        'events'           => ['label' => 'Events',           'icon' => 'calendar'],
        'certificates'     => ['label' => 'Certificates',     'icon' => 'trophy'],
    ];

    public function mount(): void
    {
        $this->currentFolder = request()->get('folder', '__all__');
        $this->viewMode      = session('fm_view', 'grid');
    }

    /**
     * Returns all file paths currently referenced in the database,
     * normalised to relative storage paths (e.g. "avatars/abc.jpg").
     */
    public function getRegisteredPaths(): array
    {
        $paths = collect();

        // users: avatar, cover_image, cv_path
        $paths = $paths->merge(
            User::whereNotNull('avatar')->pluck('avatar')
        )->merge(
            User::whereNotNull('cover_image')->pluck('cover_image')
        )->merge(
            User::whereNotNull('cv_path')->pluck('cv_path')
        );

        // courses: thumbnail
        $paths = $paths->merge(
            Course::whereNotNull('thumbnail')->pluck('thumbnail')
        );

        // events: thumbnail, invitation_card_file
        $paths = $paths->merge(
            Event::whereNotNull('thumbnail')->pluck('thumbnail')
        )->merge(
            Event::whereNotNull('invitation_card_file')->pluck('invitation_card_file')
        );

        // certificates: file_path, image_path
        $paths = $paths->merge(
            Certificate::whereNotNull('file_path')->pluck('file_path')
        )->merge(
            Certificate::whereNotNull('image_path')->pluck('image_path')
        );

        // course_documents: file_path
        $paths = $paths->merge(
            CourseDocument::whereNotNull('file_path')->pluck('file_path')
        );

        // Normalise: strip leading "storage/" or "/storage/" if present
        return $paths
            ->filter()
            ->map(fn($p) => ltrim(preg_replace('#^/?storage/#', '', $p), '/'))
            ->unique()
            ->values()
            ->all();
    }

    public function getFolderStats(): array
    {
        $registered = $this->getRegisteredPaths();
        $stats = [];
        $totalCount   = 0;
        $totalSize    = 0;
        $totalOrphans = 0;

        foreach (self::$folders as $key => $meta) {
            $files   = Storage::disk('public')->files($key);
            $size    = collect($files)->sum(fn($f) => Storage::disk('public')->size($f));
            $orphans = count(array_filter($files, fn($f) => !in_array($f, $registered)));
            $stats[$key] = [
                'label'   => $meta['label'],
                'icon'    => $meta['icon'],
                'count'   => count($files),
                'size'    => $this->formatBytes($size),
                'orphans' => $orphans,
            ];
            $totalCount   += count($files);
            $totalSize    += $size;
            $totalOrphans += $orphans;
        }

        // Calculate total orphan files for the orphans section
        $allFiles = [];
        foreach (array_keys(self::$folders) as $folder) {
            $allFiles = array_merge($allFiles, Storage::disk('public')->files($folder));
        }
        $orphanFiles = array_filter($allFiles, fn($f) => !in_array($f, $registered));
        $orphanSize  = collect($orphanFiles)->sum(fn($f) => Storage::disk('public')->size($f));

        $stats = array_merge([
            '__all__' => [
                'label'   => 'All Files',
                'icon'    => 'squares-2x2',
                'count'   => $totalCount,
                'size'    => $this->formatBytes($totalSize),
                'orphans' => $totalOrphans,
            ],
            '__orphans__' => [
                'label'   => 'Not in DB',
                'icon'    => 'exclamation-triangle',
                'count'   => count($orphanFiles),
                'size'    => $this->formatBytes($orphanSize),
                'orphans' => count($orphanFiles),
                'is_orphans' => true,
            ],
        ], $stats);

        return $stats;
    }

    public function getFiles(): array
    {
        $registered = $this->getRegisteredPaths();

        if ($this->currentFolder === '__orphans__') {
            // Show all orphaned files from all folders
            $files = [];
            foreach (array_keys(self::$folders) as $folder) {
                $files = array_merge($files, Storage::disk('public')->files($folder));
            }
            $files = array_filter($files, fn($f) => !in_array($f, $registered));
        } elseif ($this->currentFolder === '__all__') {
            $files = [];
            foreach (array_keys(self::$folders) as $folder) {
                $files = array_merge($files, Storage::disk('public')->files($folder));
            }
        } else {
            $files = Storage::disk('public')->files($this->currentFolder);
        }

        if ($this->orphansOnly && $this->currentFolder !== '__orphans__') {
            $files = array_filter($files, fn($f) => !in_array($f, $registered));
        }

        if ($this->search) {
            $search = strtolower($this->search);
            $files = array_filter($files, fn($f) => str_contains(strtolower(basename($f)), $search));
        }

        usort($files, fn($a, $b) =>
            Storage::disk('public')->lastModified($b) <=> Storage::disk('public')->lastModified($a)
        );

        return array_map(function ($path) use ($registered) {
            $name = basename($path);
            $size = Storage::disk('public')->size($path);
            $mime = Storage::disk('public')->mimeType($path);
            $modified = Storage::disk('public')->lastModified($path);
            $isImage  = str_starts_with($mime, 'image/');
            $isOrphan = !in_array($path, $registered);

            return [
                'path'      => $path,
                'name'      => $name,
                'url'       => asset('storage/' . $path),
                'size'      => $this->formatBytes($size),
                'bytes'     => $size,
                'mime'      => $mime,
                'modified'  => date('M d, Y H:i', $modified),
                'is_image'  => $isImage,
                'ext'       => strtolower(pathinfo($name, PATHINFO_EXTENSION)),
                'is_orphan' => $isOrphan,
            ];
        }, array_values($files));
    }

    public function toggleOrphans(): void
    {
        $this->orphansOnly = !$this->orphansOnly;
    }

    public function setFolder(string $folder): void
    {
        $this->currentFolder = $folder;
        $this->search = '';
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = $mode;
        session(['fm_view' => $mode]);
    }

    public function deleteFile(string $path): void
    {
        // Guard against path traversal
        $allowedFolders = array_keys(self::$folders);
        $topFolder = explode('/', $path)[0];
        if (!in_array($topFolder, $allowedFolders)) {
            Notification::make()->title('Access denied.')->danger()->send();
            return;
        }

        if (!Storage::disk('public')->exists($path)) {
            Notification::make()->title('File not found.')->warning()->send();
            return;
        }
        Storage::disk('public')->delete($path);
        Notification::make()->title('File deleted successfully.')->success()->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('upload')
                ->label('Upload Files')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->form([
                    Select::make('folder')
                        ->label('Upload to Folder')
                        ->options(collect(self::$folders)->mapWithKeys(fn($v, $k) => [$k => $v['label']]))
                        ->default($this->currentFolder)
                        ->required(),
                    FileUpload::make('files')
                        ->label('Select Files')
                        ->multiple()
                        ->maxSize(10240)
                        ->disk('public')
                        ->directory('fm-tmp')
                        ->visibility('public')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $folder   = $data['folder'];
                    $uploaded = 0;

                    foreach ((array) $data['files'] as $tmpRelPath) {
                        $originalName = basename($tmpRelPath);
                        $destination  = $folder . '/' . $originalName;

                        if (Storage::disk('public')->exists($tmpRelPath)) {
                            Storage::disk('public')->move($tmpRelPath, $destination);
                            $uploaded++;
                        }
                    }

                    // Clean up any leftover tmp files
                    Storage::disk('public')->deleteDirectory('fm-tmp');

                    $this->currentFolder = $folder;

                    Notification::make()
                        ->title("{$uploaded} file(s) uploaded to '{$folder}' successfully.")
                        ->success()
                        ->send();
                }),
        ];
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}
