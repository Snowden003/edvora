<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ContactMessageResource\Pages;
use App\Mail\ContactReplyMail;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon    = 'heroicon-o-envelope';
    protected static ?string $navigationLabel   = 'Contact Messages';
    protected static ?string $navigationGroup   = 'Communication & Events';
    protected static ?int    $navigationSort     = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) ContactMessage::where('status', 'new')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Message Details')->columns(2)->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Sender Name')->disabled(),

                Forms\Components\TextInput::make('email')
                    ->label('Email')->disabled(),

                Forms\Components\TextInput::make('company_name')
                    ->label('Company')->disabled()
                    ->visible(fn ($record) => $record && $record->company_name),

                Forms\Components\TextInput::make('phone')
                    ->label('Phone')->disabled()
                    ->visible(fn ($record) => $record && $record->phone),

                Forms\Components\Select::make('category')
                    ->label('Category')->disabled()
                    ->options([
                        'bug_report'     => 'Bug Report',
                        'system_issue'   => 'System Issue',
                        'partnership'    => 'Partnership',
                        'course_inquiry' => 'Course Inquiry',
                        'account_help' => 'Account Help',
                        'billing'        => 'Billing & Payment',
                        'general'        => 'General',
                        'other'          => 'Other',
                    ]),

                Forms\Components\Select::make('priority')
                    ->label('Priority')->disabled()
                    ->options([
                        'normal' => 'Normal',
                        'high'   => 'High',
                    ]),

                Forms\Components\TextInput::make('subject')
                    ->label('Subject')->disabled()->columnSpanFull(),

                Forms\Components\Textarea::make('message')
                    ->label('Message')->disabled()->rows(5)->columnSpanFull(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'new'     => 'New',
                        'read'    => 'Read',
                        'replied' => 'Replied',
                    ]),

                Forms\Components\TextInput::make('created_at')
                    ->label('Received At')->disabled()
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('M d, Y H:i') : '-'),
            ]),

            Forms\Components\Section::make('Admin Reply')
                ->description('Select a draft template or write a custom reply. Click Save to send the reply via email.')
                ->schema([
                    Forms\Components\Select::make('reply_template')
                        ->label('Quick Reply Templates')
                        ->placeholder('Select a template to auto-fill...')
                        ->options([
                            'thank_you' => '🙏 Thank You — General appreciation',
                            'received'  => '📩 Message Received — Acknowledgment',
                            'resolved'  => '✅ Issue Resolved — Problem fixed',
                            'more_info' => '📋 Need More Info — Request details',
                            'course'    => '📚 Course Inquiry — Course information',
                            'technical' => '🔧 Technical Support — Technical help',
                            'follow_up' => '🔄 Follow Up — Checking in',
                        ])
                        ->reactive()
                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                            $templates = [
                                'thank_you' => '<p>Dear <strong>{name}</strong>,</p><p>Thank you for reaching out to us at Edvora Tech! We truly appreciate your message and the time you took to contact us.</p><p>Your feedback is valuable to us and helps us improve our services. If you have any further questions or need additional assistance, please don\'t hesitate to reach out.</p><p>Best regards,<br><strong>Edvora Tech Team</strong></p>',
                                'received'  => '<p>Dear <strong>{name}</strong>,</p><p>We have received your message regarding "<em>{subject}</em>" and wanted to let you know that our team is reviewing it.</p><p>We will get back to you with a detailed response as soon as possible. In the meantime, feel free to explore our <a href="https://edvora.tech/faq">FAQ section</a> for quick answers.</p><p>Best regards,<br><strong>Edvora Tech Support</strong></p>',
                                'resolved'  => '<p>Dear <strong>{name}</strong>,</p><p>Great news! We have looked into your inquiry and are happy to inform you that the matter has been resolved.</p><p>Here\'s a summary of what was done:</p><ul><li>Your issue has been reviewed by our team</li><li>The necessary changes have been applied</li><li>Everything should be working as expected now</li></ul><p>If you experience any further issues, please don\'t hesitate to contact us again.</p><p>Best regards,<br><strong>Edvora Tech Support</strong></p>',
                                'more_info' => '<p>Dear <strong>{name}</strong>,</p><p>Thank you for contacting Edvora Tech. We have reviewed your message regarding "<em>{subject}</em>".</p><p>To better assist you, we would need some additional information:</p><ol><li>Could you provide more details about your request?</li><li>Any relevant screenshots or examples would be helpful</li><li>What is your preferred timeline for resolution?</li></ol><p>Once we have this information, we\'ll be able to assist you more effectively.</p><p>Best regards,<br><strong>Edvora Tech Support</strong></p>',
                                'course'    => '<p>Dear <strong>{name}</strong>,</p><p>Thank you for your interest in our courses at Edvora Tech!</p><p>We offer a wide range of courses designed to help you advance your skills. Here are some helpful resources:</p><ul><li>Browse our full course catalog on our website</li><li>Each course includes detailed descriptions and curriculum</li><li>You can enroll directly through your student dashboard</li></ul><p>If you need help choosing the right course or have specific questions about any program, feel free to ask!</p><p>Best regards,<br><strong>Edvora Tech Academic Team</strong></p>',
                                'technical' => '<p>Dear <strong>{name}</strong>,</p><p>Thank you for reporting this technical issue. Our development team has been notified and is looking into it.</p><p>In the meantime, please try the following:</p><ol><li>Clear your browser cache and cookies</li><li>Try using a different browser</li><li>Ensure your internet connection is stable</li><li>Try logging out and logging back in</li></ol><p>If the issue persists after trying these steps, please let us know and we\'ll escalate it to our technical team.</p><p>Best regards,<br><strong>Edvora Tech Technical Support</strong></p>',
                                'follow_up' => '<p>Dear <strong>{name}</strong>,</p><p>We hope this message finds you well! We wanted to follow up on your previous inquiry regarding "<em>{subject}</em>".</p><p>We want to make sure that everything has been resolved to your satisfaction. If you still need assistance or have any additional questions, please don\'t hesitate to let us know.</p><p>Your satisfaction is our top priority!</p><p>Best regards,<br><strong>Edvora Tech Team</strong></p>',
                            ];

                            if ($state && isset($templates[$state])) {
                                $set('admin_reply', $templates[$state]);
                            }
                        })
                        ->dehydrated(false)
                        ->columnSpanFull(),

                    Forms\Components\RichEditor::make('admin_reply')
                        ->label('Your Reply')
                        ->toolbarButtons([
                            'bold', 'italic', 'underline', 'strike',
                            'h2', 'h3',
                            'bulletList', 'orderedList',
                            'link', 'blockquote',
                            'undo', 'redo',
                        ])
                        ->placeholder('Write your reply here or select a template above...')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Sender')->searchable()->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')->searchable()->sortable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('category')
                    ->label('Category')
                    ->colors([
                        'danger'  => 'bug_report',
                        'warning' => 'system_issue',
                        'primary' => 'partnership',
                        'info'    => 'course_inquiry',
                        'gray'    => 'billing',
                        'success' => 'general',
                    ])
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'bug_report' => 'Bug Report',
                        'system_issue' => 'System Issue',
                        'partnership' => 'Partnership',
                        'course_inquiry' => 'Course Inquiry',
                        'account_help' => 'Account Help',
                        'billing' => 'Billing',
                        'general' => 'General',
                        default => ucfirst($state),
                    })
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('priority')
                    ->label('Priority')
                    ->colors([
                        'success' => 'normal',
                        'danger'  => 'high',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Subject')->searchable()->sortable()->limit(30),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'danger'  => 'new',
                        'warning' => 'read',
                        'success' => 'replied',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')->dateTime('M d, Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new'     => 'New',
                        'read'    => 'Read',
                        'replied' => 'Replied',
                    ]),
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'bug_report'     => 'Bug Report',
                        'system_issue'   => 'System Issue',
                        'partnership'    => 'Partnership',
                        'course_inquiry' => 'Course Inquiry',
                        'account_help' => 'Account Help',
                        'billing'        => 'Billing & Payment',
                        'general'        => 'General',
                    ]),
                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'normal' => 'Normal',
                        'high'   => 'High',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('reply')
                    ->label('Reply')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('primary')
                    ->visible(fn (ContactMessage $record) => $record->status !== 'replied')
                    ->form([
                        Forms\Components\Textarea::make('admin_reply')
                            ->label('Your Reply')
                            ->required()
                            ->rows(5)
                            ->placeholder('Write your reply to this message...'),
                    ])
                    ->action(function (ContactMessage $record, array $data) {
                        $record->update([
                            'admin_reply' => $data['admin_reply'],
                            'status'      => 'replied',
                            'replied_at'  => now(),
                        ]);

                        try {
                            Mail::to($record->email)->send(new ContactReplyMail($record->fresh()));
                            Notification::make()->title('Reply sent to ' . $record->email)->success()->send();
                        } catch (\Exception $e) {
                            Notification::make()->title('Reply saved but email failed')->body($e->getMessage())->warning()->send();
                        }
                    }),

                Tables\Actions\Action::make('markRead')
                    ->label('Mark as Read')
                    ->icon('heroicon-o-eye')
                    ->color('warning')
                    ->visible(fn (ContactMessage $record) => $record->status === 'new')
                    ->requiresConfirmation()
                    ->action(function (ContactMessage $record) {
                        $record->update(['status' => 'read']);
                        Notification::make()->title('Marked as read.')->success()->send();
                    }),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'edit'  => Pages\EditContactMessage::route('/{record}/edit'),
        ];
    }
}
