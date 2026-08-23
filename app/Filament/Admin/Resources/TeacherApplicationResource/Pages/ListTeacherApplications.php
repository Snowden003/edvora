<?php

namespace App\Filament\Admin\Resources\TeacherApplicationResource\Pages;

use App\Filament\Admin\Resources\TeacherApplicationResource;
use App\Models\User;
use App\Mail\TeacherApplicationApproved;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

class ListTeacherApplications extends ListRecords
{
    protected static string $resource = TeacherApplicationResource::class;

    protected function getHeaderActions(): array
    {
        $pendingCount = User::where('role', 'teacher')->whereHas('teacher')->where('status', 'pending')->count();

        return [
            Action::make('approve_all_pending')
                ->label('Approve All Pending (' . $pendingCount . ')')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->visible($pendingCount > 0)
                ->requiresConfirmation()
                ->modalHeading('Approve All Pending Applications')
                ->modalDescription("Are you sure you want to approve all {$pendingCount} pending teacher applications at once?")
                ->modalSubmitActionLabel('Yes, approve all pending')
                ->action(function () {
                    $pendingTeachers = User::where('role', 'teacher')
                        ->whereHas('teacher')
                        ->where('status', 'pending')
                        ->get();

                    $count = 0;
                    foreach ($pendingTeachers as $record) {
                        $record->update(['status' => 'active']);
                        $record->teacher?->update(['is_verified' => true]);

                        try {
                            if ($record->email) {
                                Mail::to($record->email)->send(new TeacherApplicationApproved($record));
                            }
                        } catch (\Throwable $e) {
                            // Continue on mail failure
                        }
                        $count++;
                    }

                    Notification::make()
                        ->title("All {$count} pending applications have been approved.")
                        ->success()
                        ->send();
                }),

            Action::make('reject_all_pending')
                ->label('Reject All Pending (' . $pendingCount . ')')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible($pendingCount > 0)
                ->requiresConfirmation()
                ->modalHeading('Reject All Pending Applications')
                ->modalDescription("Are you sure you want to reject all {$pendingCount} pending teacher applications?")
                ->modalSubmitActionLabel('Yes, reject all pending')
                ->action(function () {
                    $count = User::where('role', 'teacher')
                        ->whereHas('teacher')
                        ->where('status', 'pending')
                        ->update(['status' => 'rejected']);

                    Notification::make()
                        ->title("All {$count} pending applications have been rejected.")
                        ->warning()
                        ->send();
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Applications'),
            'pending' => Tab::make('Pending Review')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(User::where('role', 'teacher')->whereHas('teacher')->where('status', 'pending')->count())
                ->badgeColor('warning'),
            'active' => Tab::make('Approved')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'active'))
                ->badgeColor('success'),
            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'rejected'))
                ->badgeColor('danger'),
            'suspended' => Tab::make('Suspended')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'suspended'))
                ->badgeColor('gray'),
        ];
    }
}