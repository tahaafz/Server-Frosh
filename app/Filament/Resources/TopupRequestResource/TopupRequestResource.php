<?php

namespace App\Filament\Resources\TopupRequestResource;

use App\Models\TopupRequest;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use function App\Filament\Resources\notification;
use function App\Filament\Resources\schema;

class TopupRequestResource extends Resource
{
    protected static ?string $model = TopupRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'مالی';
    protected static ?string $navigationLabel = 'درخواست‌های افزایش موجودی (دارای تصویر)';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNotNull('receipt_media_id')
            ->orWhereNotNull('receipt_file_id')
            ->when(schema()->hasColumn((new TopupRequest)->getTable(), 'status'), function ($q) {
                $q->where('status', 'pending');
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('user.name')->label('کاربر')->searchable()->toggleable(),
                TextColumn::make('amount')->label('مبلغ')->numeric()->sortable(),
                TextColumn::make('currency')->label('ارز')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('method')->label('روش')->toggleable(),
                ImageColumn::make('receiptMedia.path')
                    ->label('رسید')
                    ->height(96)->width(96)
                    ->circular(false)
                    ->getStateUsing(function (TopupRequest $record) {
                        $media = $record->receiptMedia ?? null;
                        $path  = method_exists($media, 'fullPath') ? $media->fullPath() : ($media->path ?? null);
                        if ($path) return Storage::url($path);
                        if (!empty($record->receipt_file_id)) {
                            return route('admin.topup.receipt.proxy', $record); // تعریف این route اختیاری است
                        }
                        return null;
                    })
                    ->extraAttributes(['class' => 'cursor-pointer'])
                    ->action(static::reviewAction()),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ]),
                TextColumn::make('created_at')->label('تاریخ')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'pending'  => 'در انتظار',
                        'approved' => 'تایید شده',
                        'rejected' => 'رد شده',
                    ])
                    ->default('pending')
                    ->visible(fn () => schema()->hasColumn((new TopupRequest)->getTable(), 'status')),
            ])
            ->actions([
                static::reviewAction(),
                Action::make('approve')
                    ->label('تایید')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->visible(fn (TopupRequest $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (TopupRequest $record) {
                        $record->forceFill([
                            'status'       => 'approved',
                            'admin_id'     => auth()->id(),
                            'approved_at'  => now(),
                        ])->save();
                        notification()->success('درخواست تایید شد.');
                    }),
                Action::make('reject')
                    ->label('رد')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->visible(fn (TopupRequest $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (TopupRequest $record) {
                        $record->forceFill([
                            'status'       => 'rejected',
                            'admin_id'     => auth()->id(),
                            'approved_at'  => null,
                        ])->save();
                        notification()->success('درخواست رد شد.');
                    }),
            ])
            ->bulkActions([]);
    }

    protected static function reviewAction(): Action
    {
        return Action::make('review')
            ->label('بررسی رسید')
            ->icon('heroicon-o-eye')
            ->modalHeading('بررسی رسید')
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->modalWidth('4xl')
            ->modalContent(function (TopupRequest $record) {
                $media = $record->receiptMedia ?? null;
                $path  = method_exists($media, 'fullPath') ? $media->fullPath() : ($media->path ?? null);
                $url   = $path ? Storage::url($path) : null;
                $img   = $url ? "<img src=\"{$url}\" alt=\"receipt\" class=\"w-full h-auto rounded-xl\">" : "<div class=\"text-center py-8\">تصویر در دسترس نیست</div>";
                $meta  = "<div class=\"text-sm text-gray-600 dark:text-gray-300 text-center mt-4\">
                    <div>کاربر: <strong>".e(optional($record->user)->name ?? '-')."</strong></div>
                    <div>مبلغ: <strong>".number_format((int)$record->amount)." ".e($record->currency ?? 'IRR')."</strong></div>
                    <div>روش: <strong>".e($record->method ?? '-')."</strong></div>
                    <div>تاریخ: <strong>".optional($record->created_at)->format('Y-m-d H:i')."</strong></div>
                </div>";
                return new HtmlString("<div class=\"space-y-4\">{$img}{$meta}</div>");
            })
            ->modalFooterActions([
                Action::make('approveModal')
                    ->label('تایید')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->action(function (TopupRequest $record) {
                        $record->forceFill([
                            'status'       => 'approved',
                            'admin_id'     => auth()->id(),
                            'approved_at'  => now(),
                        ])->save();
                        notification()->success('درخواست تایید شد.');
                    })
                    ->close(),
                Action::make('rejectModal')
                    ->label('رد')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (TopupRequest $record) {
                        $record->forceFill([
                            'status'       => 'rejected',
                            'admin_id'     => auth()->id(),
                            'approved_at'  => null,
                        ])->save();
                        notification()->success('درخواست رد شد.');
                    })
                    ->close(),
                Action::make('cancel')
                    ->label('بستن')
                    ->color('gray')
                    ->close(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTopupRequests::route('/'),
        ];
    }
}
