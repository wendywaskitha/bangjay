<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeroBannerResource\Pages;
use App\Filament\Resources\HeroBannerResource\RelationManagers;
use App\Models\HeroBanner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Str;

class HeroBannerResource extends Resource
{
    protected static ?string $model = HeroBanner::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Hero Banner';

    protected static ?string $modelLabel = 'Hero Banner';

    protected static ?string $pluralModelLabel = 'Data Hero Banner';

    protected static ?string $navigationGroup = 'Pengaturan Website';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'judul';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Hero Banner')
                    ->description('Konten utama hero banner')
                    ->icon('heroicon-o-photo')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('judul')
                            ->required()
                            ->maxLength(255)
                            ->label('Judul Banner')
                            ->placeholder('Masukkan judul menarik...')
                            ->helperText('Judul utama yang akan ditampilkan di banner')
                            ->prefixIcon('heroicon-o-sparkles')
                            ->autocomplete(false)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('subjudul')
                            ->maxLength(255)
                            ->label('Subjudul')
                            ->placeholder('Subjudul pendukung (opsional)...')
                            ->helperText('Teks pendukung di bawah judul')
                            ->prefixIcon('heroicon-o-document-text')
                            ->autocomplete(false)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('deskripsi_singkat')
                            ->label('Deskripsi Singkat')
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('Deskripsi singkat banner (maksimal 500 karakter)...')
                            ->helperText('Penjelasan singkat atau tagline')
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('urutan')
                                    ->required()
                                    ->numeric()
                                    ->default(fn () => HeroBanner::max('urutan') + 1)
                                    ->minValue(1)
                                    ->maxValue(99)
                                    ->label('Urutan Tampil')
                                    ->helperText('Urutan tampil banner (1 = pertama)')
                                    ->prefixIcon('heroicon-o-numbered-list')
                                    ->suffix('/99')
                                    ->columnSpan(1),

                                Forms\Components\Toggle::make('is_active')
                                    ->required()
                                    ->default(false)
                                    ->label('Status Aktif')
                                    ->helperText('Aktifkan untuk menampilkan banner')
                                    ->inline(false)
                                    ->onColor('success')
                                    ->offColor('gray')
                                    ->columnSpan(1),
                            ]),

                        Forms\Components\Placeholder::make('preview_info')
                            ->label('ℹ️ Info Banner')
                            ->content(function (Forms\Get $get): string {
                                $urutan = $get('urutan') ?? 1;
                                $aktif = $get('is_active') ? 'Aktif' : 'Nonaktif';

                                return "Banner ini akan tampil di urutan ke-{$urutan} dengan status {$aktif}.";
                            })
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Call-to-Action (CTA)')
                    ->description('Tombol aksi pada banner')
                    ->icon('heroicon-o-cursor-arrow-ripple')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('cta_text')
                                    ->maxLength(255)
                                    ->label('Teks Tombol CTA')
                                    ->placeholder('Contoh: Lihat Selengkapnya, Hubungi Kami')
                                    ->helperText('Teks yang tampil di tombol')
                                    ->prefixIcon('heroicon-o-cursor-arrow-rays')
                                    ->autocomplete(false)
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('cta_link')
                                    ->maxLength(255)
                                    ->label('Link Tombol CTA')
                                    ->placeholder('/tentang-kami atau https://example.com')
                                    ->helperText('URL tujuan saat tombol diklik')
                                    ->prefixIcon('heroicon-o-link')
                                    ->url()
                                    ->columnSpan(1),
                            ]),

                        Forms\Components\Placeholder::make('cta_preview')
                            ->label('Preview CTA')
                            ->content(function (Forms\Get $get): string {
                                $text = $get('cta_text');
                                $link = $get('cta_link');

                                if (!$text && !$link) {
                                    return '❌ Banner tanpa tombol CTA';
                                }
                                if ($text && !$link) {
                                    return '⚠️ Tombol "{' . $text . '}" tanpa link';
                                }
                                if (!$text && $link) {
                                    return '⚠️ Link tersedia tapi tanpa teks tombol';
                                }

                                return "✅ Tombol \"{$text}\" mengarah ke: {$link}";
                            })
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Media Banner')
                    ->description('Upload gambar hero banner')
                    ->icon('heroicon-o-photo')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\FileUpload::make('gambar')
                            ->image()
                            ->label('Gambar Hero Banner')
                            ->directory('hero-banners')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '21:9',
                                '32:9',
                            ])
                            ->imagePreviewHeight('250')
                            ->helperText('🖼️ Upload gambar banner (Rekomendasi: 1920x800px, max 3MB)')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(3072)
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('image_tips')
                            ->label('💡 Tips Gambar')
                            ->content('Gunakan gambar berkualitas tinggi dengan resolusi minimal 1920x800px. Format landscape 16:9 atau 21:9 untuk hasil terbaik.')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Informasi Tambahan')
                    ->description('Statistik dan metadata')
                    ->icon('heroicon-o-information-circle')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('total_banners')
                            ->label('Total Banner')
                            ->content(fn (): string =>
                                number_format(HeroBanner::count()) . ' banner terdaftar'
                            ),

                        Forms\Components\Placeholder::make('active_banners')
                            ->label('Banner Aktif')
                            ->content(fn (): string =>
                                number_format(HeroBanner::where('is_active', true)->count()) . ' banner aktif'
                            ),

                        Forms\Components\Placeholder::make('has_cta')
                            ->label('Status CTA')
                            ->content(function (Forms\Get $get): string {
                                $text = $get('cta_text');
                                $link = $get('cta_link');

                                return ($text && $link) ? '✅ CTA Lengkap' : '❌ CTA Tidak Lengkap';
                            }),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Dibuat pada')
                            ->content(fn (?HeroBanner $record): string =>
                                $record?->created_at?->format('d F Y, H:i') . ' (' . $record?->created_at?->diffForHumans() . ')' ?? '-'
                            )
                            ->visible(fn (?HeroBanner $record) => $record !== null),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->content(fn (?HeroBanner $record): string =>
                                $record?->updated_at?->diffForHumans() ?? '-'
                            )
                            ->visible(fn (?HeroBanner $record) => $record !== null),
                    ])
                    ->columns(3)
                    ->visible(fn ($operation) => $operation === 'edit' || $operation === 'view'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('urutan')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->alignCenter()
                    ->tooltip('Urutan tampil banner'),

                Tables\Columns\ImageColumn::make('gambar')
                    ->label('Gambar')
                    ->defaultImageUrl(url('/images/default-banner.png'))
                    ->height(60)
                    ->width(120),

                Tables\Columns\TextColumn::make('judul')
                    ->searchable()
                    ->label('Judul Banner')
                    ->icon('heroicon-o-sparkles')
                    ->iconColor('primary')
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Judul tersalin!')
                    ->description(fn (HeroBanner $record): string =>
                        $record->subjudul ?: 'Tanpa subjudul'
                    )
                    ->wrap()
                    ->limit(40)
                    ->tooltip(fn (HeroBanner $record): string => $record->judul),

                Tables\Columns\TextColumn::make('deskripsi_singkat')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->placeholder('Tanpa deskripsi')
                    ->tooltip(fn (?string $state): ?string => $state)
                    ->toggleable(),

                Tables\Columns\IconColumn::make('has_cta')
                    ->label('CTA')
                    ->boolean()
                    ->state(fn (HeroBanner $record): bool =>
                        !empty($record->cta_text) && !empty($record->cta_link)
                    )
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->alignCenter()
                    ->tooltip(fn (HeroBanner $record): string =>
                        $record->cta_text && $record->cta_link
                            ? "CTA: {$record->cta_text}"
                            : 'Tanpa tombol CTA'
                    ),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Status')
                    ->onColor('success')
                    ->offColor('gray')
                    ->alignCenter()
                    ->tooltip(fn (bool $state): string =>
                        $state ? 'Aktif - Klik untuk nonaktifkan' : 'Nonaktif - Klik untuk aktifkan'
                    )
                    ->afterStateUpdated(function ($record, $state) {
                        \Filament\Notifications\Notification::make()
                            ->title('Status berhasil diubah')
                            ->body($state ? 'Banner diaktifkan' : 'Banner dinonaktifkan')
                            ->success()
                            ->send();
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->label('Dibuat')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-calendar')
                    ->tooltip(fn ($state): string => $state->diffForHumans()),
            ])
            ->defaultSort('urutan', 'asc')
            ->reorderable('urutan')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua Status')
                    ->trueLabel('Banner Aktif')
                    ->falseLabel('Banner Nonaktif')
                    ->native(false),

                Tables\Filters\TernaryFilter::make('has_gambar')
                    ->label('Gambar')
                    ->placeholder('Semua')
                    ->trueLabel('Dengan Gambar')
                    ->falseLabel('Tanpa Gambar')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('gambar'),
                        false: fn (Builder $query) => $query->whereNull('gambar'),
                    )
                    ->native(false),

                Tables\Filters\TernaryFilter::make('has_cta')
                    ->label('Call-to-Action')
                    ->placeholder('Semua')
                    ->trueLabel('Dengan CTA')
                    ->falseLabel('Tanpa CTA')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('cta_text')->whereNotNull('cta_link'),
                        false: fn (Builder $query) => $query->where(function($q) {
                            $q->whereNull('cta_text')->orWhereNull('cta_link');
                        }),
                    )
                    ->native(false),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->persistFiltersInSession()
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info')
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\EditAction::make()
                        ->color('warning')
                        ->icon('heroicon-o-pencil'),
                    Tables\Actions\Action::make('toggle_active')
                        ->label(fn (HeroBanner $record): string =>
                            $record->is_active ? 'Nonaktifkan' : 'Aktifkan'
                        )
                        ->icon(fn (HeroBanner $record): string =>
                            $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle'
                        )
                        ->color(fn (HeroBanner $record): string =>
                            $record->is_active ? 'gray' : 'success'
                        )
                        ->requiresConfirmation()
                        ->action(function (HeroBanner $record) {
                            $record->update(['is_active' => !$record->is_active]);
                            \Filament\Notifications\Notification::make()
                                ->title('Status berhasil diubah')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('move_up')
                        ->label('Naikkan Urutan')
                        ->icon('heroicon-o-arrow-up')
                        ->color('info')
                        ->action(function (HeroBanner $record) {
                            $previous = HeroBanner::where('urutan', '<', $record->urutan)
                                ->orderBy('urutan', 'desc')
                                ->first();

                            if ($previous) {
                                $tempUrutan = $record->urutan;
                                $record->update(['urutan' => $previous->urutan]);
                                $previous->update(['urutan' => $tempUrutan]);

                                \Filament\Notifications\Notification::make()
                                    ->title('Urutan berhasil dinaikkan')
                                    ->success()
                                    ->send();
                            }
                        })
                        ->visible(fn (HeroBanner $record): bool =>
                            HeroBanner::where('urutan', '<', $record->urutan)->exists()
                        ),
                    Tables\Actions\Action::make('move_down')
                        ->label('Turunkan Urutan')
                        ->icon('heroicon-o-arrow-down')
                        ->color('info')
                        ->action(function (HeroBanner $record) {
                            $next = HeroBanner::where('urutan', '>', $record->urutan)
                                ->orderBy('urutan', 'asc')
                                ->first();

                            if ($next) {
                                $tempUrutan = $record->urutan;
                                $record->update(['urutan' => $next->urutan]);
                                $next->update(['urutan' => $tempUrutan]);

                                \Filament\Notifications\Notification::make()
                                    ->title('Urutan berhasil diturunkan')
                                    ->success()
                                    ->send();
                            }
                        })
                        ->visible(fn (HeroBanner $record): bool =>
                            HeroBanner::where('urutan', '>', $record->urutan)->exists()
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Hero Banner')
                        ->modalDescription('Apakah Anda yakin ingin menghapus banner ini?')
                        ->modalSubmitActionLabel('Ya, Hapus'),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Aksi')
                ->button()
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan Terpilih')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each->update(['is_active' => true]);
                            \Filament\Notifications\Notification::make()
                                ->title('Banner berhasil diaktifkan')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan Terpilih')
                        ->icon('heroicon-o-x-circle')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each->update(['is_active' => false]);
                            \Filament\Notifications\Notification::make()
                                ->title('Banner berhasil dinonaktifkan')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Banner Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus semua banner yang dipilih?')
                        ->modalSubmitActionLabel('Ya, Hapus Semua'),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Hero Banner')
            ->emptyStateDescription('Mulai dengan membuat hero banner pertama untuk homepage.')
            ->emptyStateIcon('heroicon-o-photo')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Buat Hero Banner')
                    ->icon('heroicon-o-plus')
                    ->color('primary'),
            ])
            ->striped()
            ->paginated([10, 25, 50])
            ->poll('30s');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Hero Banner')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Infolists\Components\ImageEntry::make('gambar')
                            ->label('Gambar Banner')
                            ->defaultImageUrl(url('/images/default-banner.png'))
                            ->columnSpanFull(),

                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('urutan')
                                    ->label('Urutan')
                                    ->badge()
                                    ->color('primary')
                                    ->icon('heroicon-o-numbered-list'),

                                Infolists\Components\TextEntry::make('is_active')
                                    ->label('Status')
                                    ->badge()
                                    ->formatStateUsing(fn (bool $state): string =>
                                        $state ? 'Aktif' : 'Nonaktif'
                                    )
                                    ->color(fn (bool $state): string =>
                                        $state ? 'success' : 'gray'
                                    )
                                    ->icon(fn (bool $state): string =>
                                        $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'
                                    ),

                                Infolists\Components\TextEntry::make('has_cta')
                                    ->label('Call-to-Action')
                                    ->state(fn (HeroBanner $record): string =>
                                        $record->cta_text && $record->cta_link ? 'Ada CTA' : 'Tanpa CTA'
                                    )
                                    ->badge()
                                    ->color(fn (HeroBanner $record): string =>
                                        $record->cta_text && $record->cta_link ? 'success' : 'gray'
                                    )
                                    ->icon('heroicon-o-cursor-arrow-ripple'),
                            ]),

                        Infolists\Components\TextEntry::make('judul')
                            ->label('Judul')
                            ->size('lg')
                            ->weight('bold')
                            ->copyable()
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('subjudul')
                            ->label('Subjudul')
                            ->default('-')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('deskripsi_singkat')
                            ->label('Deskripsi')
                            ->default('-')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Call-to-Action')
                    ->icon('heroicon-o-cursor-arrow-ripple')
                    ->schema([
                        Infolists\Components\TextEntry::make('cta_text')
                            ->label('Teks Tombol')
                            ->icon('heroicon-o-cursor-arrow-rays')
                            ->badge()
                            ->color('info')
                            ->default('-'),

                        Infolists\Components\TextEntry::make('cta_link')
                            ->label('Link Tombol')
                            ->icon('heroicon-o-link')
                            ->copyable()
                            ->url(fn (?string $state): ?string => $state)
                            ->openUrlInNewTab()
                            ->color('success')
                            ->default('-'),
                    ])
                    ->columns(2)
                    ->visible(fn (HeroBanner $record): bool =>
                        $record->cta_text || $record->cta_link
                    ),

                Infolists\Components\Section::make('Riwayat Data')
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Dibuat pada')
                            ->dateTime('d F Y, H:i:s')
                            ->icon('heroicon-o-calendar-days')
                            ->since(),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Terakhir diperbarui')
                            ->dateTime('d F Y, H:i:s')
                            ->icon('heroicon-o-arrow-path')
                            ->since(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHeroBanners::route('/'),
            'create' => Pages\CreateHeroBanner::route('/create'),
            'view' => Pages\ViewHeroBanner::route('/{record}'),
            'edit' => Pages\EditHeroBanner::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $aktif = static::getModel()::where('is_active', true)->count();
        $total = static::getModel()::count();
        return "{$aktif}/{$total}";
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $aktif = static::getModel()::where('is_active', true)->count();
        return $aktif > 0 ? 'success' : 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        $aktif = static::getModel()::where('is_active', true)->count();
        $total = static::getModel()::count();
        return "{$aktif} dari {$total} banner aktif";
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Urutan' => "#{$record->urutan}",
            'Status' => $record->is_active ? 'Aktif' : 'Nonaktif',
        ];
    }
}
