<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfilBangJaiResource\Pages;
use App\Filament\Resources\ProfilBangJaiResource\RelationManagers;
use App\Models\ProfilBangJai;
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

class ProfilBangJaiResource extends Resource
{
    protected static ?string $model = ProfilBangJai::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Profil Bang Jai';

    protected static ?string $modelLabel = 'Profil Bang Jai';

    protected static ?string $pluralModelLabel = 'Data Profil Bang Jai';

    protected static ?string $navigationGroup = 'Pengaturan Website';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'judul';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Profil')
                    ->description('Data utama profil Bang Jai')
                    ->icon('heroicon-o-user-circle')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('judul')
                            ->required()
                            ->maxLength(255)
                            ->label('Judul Profil')
                            ->placeholder('Contoh: Profil H. Jainal Abidin')
                            ->helperText('Judul untuk halaman profil')
                            ->prefixIcon('heroicon-o-identification')
                            ->autocomplete(false)
                            ->columnSpan(2),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Profil Aktif')
                            ->helperText('⚠️ Hanya satu profil yang bisa aktif sekaligus')
                            ->inline(false)
                            ->onColor('success')
                            ->offColor('gray')
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set, ?ProfilBangJai $record) {
                                // Auto-nonaktifkan profil lain saat ini diaktifkan
                                if ($state) {
                                    ProfilBangJai::where('id', '!=', $record?->id)
                                        ->update(['is_active' => false]);
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\Placeholder::make('status_info')
                            ->label('Info Status')
                            ->content(function (Forms\Get $get, ?ProfilBangJai $record): string {
                                if ($get('is_active')) {
                                    return '✅ Profil ini akan ditampilkan di website';
                                }

                                $aktif = ProfilBangJai::where('is_active', true)
                                    ->where('id', '!=', $record?->id)
                                    ->first();

                                if ($aktif) {
                                    return "ℹ️ Profil aktif saat ini: {$aktif->judul}";
                                }

                                return '⚠️ Belum ada profil aktif';
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Konten Profil')
                    ->description('Isi lengkap profil')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->schema([
                        Forms\Components\RichEditor::make('konten_profil')
                            ->required()
                            ->label('Konten Profil')
                            ->columnSpanFull()
                            ->placeholder('Tulis konten profil lengkap Bang Jai di sini...')
                            ->helperText('Konten lengkap profil dengan formatting')
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'undo',
                            ])
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('profil-attachments')
                            ->maxLength(10000),
                    ]),

                Forms\Components\Section::make('Media Profil')
                    ->description('Upload foto profil dan banner')
                    ->icon('heroicon-o-photo')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\FileUpload::make('foto_profil')
                                    ->image()
                                    ->label('Foto Profil')
                                    ->directory('profil-images')
                                    ->visibility('public')
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '1:1',
                                        '4:5',
                                    ])
                                    ->imagePreviewHeight('250')
                                    ->helperText('📸 Upload foto profil (Rekomendasi: 800x800px, max 2MB)')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(2048)
                                    ->columnSpan(1),

                                Forms\Components\FileUpload::make('foto_banner')
                                    ->image()
                                    ->label('Foto Banner')
                                    ->directory('profil-banners')
                                    ->visibility('public')
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '16:9',
                                        '21:9',
                                    ])
                                    ->imagePreviewHeight('250')
                                    ->helperText('🖼️ Upload banner profil (Rekomendasi: 1920x600px, max 3MB)')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(3072)
                                    ->columnSpan(1),
                            ]),

                        Forms\Components\Placeholder::make('media_info')
                            ->label('ℹ️ Tips Media')
                            ->content('Foto profil digunakan untuk avatar/foto utama, sedangkan foto banner digunakan sebagai latar belakang header halaman profil.')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Informasi Tambahan')
                    ->description('Statistik dan metadata')
                    ->icon('heroicon-o-information-circle')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('word_count')
                            ->label('Jumlah Kata')
                            ->content(function (Forms\Get $get): string {
                                $konten = $get('konten_profil') ?? '';
                                $wordCount = str_word_count(strip_tags($konten));
                                return number_format($wordCount) . ' kata (~' . ceil($wordCount / 200) . ' menit baca)';
                            }),

                        Forms\Components\Placeholder::make('char_count')
                            ->label('Jumlah Karakter')
                            ->content(function (Forms\Get $get): string {
                                $konten = $get('konten_profil') ?? '';
                                $charCount = strlen(strip_tags($konten));
                                return number_format($charCount) . ' karakter';
                            }),

                        Forms\Components\Placeholder::make('has_media')
                            ->label('Status Media')
                            ->content(function (Forms\Get $get): string {
                                $profil = $get('foto_profil');
                                $banner = $get('foto_banner');

                                $status = [];
                                $status[] = $profil ? '✅ Foto Profil' : '❌ Foto Profil';
                                $status[] = $banner ? '✅ Banner' : '❌ Banner';

                                return implode(' | ', $status);
                            }),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Dibuat pada')
                            ->content(fn (?ProfilBangJai $record): string =>
                                $record?->created_at?->format('d F Y, H:i') . ' (' . $record?->created_at?->diffForHumans() . ')' ?? '-'
                            )
                            ->visible(fn (?ProfilBangJai $record) => $record !== null),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->content(fn (?ProfilBangJai $record): string =>
                                $record?->updated_at?->diffForHumans() ?? '-'
                            )
                            ->visible(fn (?ProfilBangJai $record) => $record !== null),
                    ])
                    ->columns(3)
                    ->visible(fn ($operation) => $operation === 'edit' || $operation === 'view'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('No')
                    ->rowIndex()
                    ->alignCenter(),

                Tables\Columns\ImageColumn::make('foto_profil')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png'))
                    ->size(60),

                Tables\Columns\TextColumn::make('judul')
                    ->searchable()
                    ->label('Judul Profil')
                    ->icon('heroicon-o-user-circle')
                    ->iconColor('primary')
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Judul tersalin!')
                    ->description(fn (ProfilBangJai $record): string =>
                        Str::limit(strip_tags($record->konten_profil), 60)
                    )
                    ->wrap()
                    ->tooltip('Klik untuk menyalin'),

                Tables\Columns\IconColumn::make('has_foto_profil')
                    ->label('Foto Profil')
                    ->boolean()
                    ->state(fn (ProfilBangJai $record): bool => !empty($record->foto_profil))
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->alignCenter()
                    ->tooltip(fn (ProfilBangJai $record): string =>
                        $record->foto_profil ? 'Foto profil tersedia' : 'Belum ada foto profil'
                    ),

                Tables\Columns\IconColumn::make('has_foto_banner')
                    ->label('Banner')
                    ->boolean()
                    ->state(fn (ProfilBangJai $record): bool => !empty($record->foto_banner))
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->alignCenter()
                    ->tooltip(fn (ProfilBangJai $record): string =>
                        $record->foto_banner ? 'Banner tersedia' : 'Belum ada banner'
                    ),

                Tables\Columns\TextColumn::make('word_count')
                    ->label('Kata')
                    ->state(function (ProfilBangJai $record): int {
                        return str_word_count(strip_tags($record->konten_profil));
                    })
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-document-text')
                    ->suffix(' kata')
                    ->alignCenter()
                    ->tooltip(fn ($state): string => number_format($state) . ' kata (~' . ceil($state / 200) . ' menit baca)')
                    ->toggleable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Status Aktif')
                    ->onColor('success')
                    ->offColor('gray')
                    ->alignCenter()
                    ->tooltip(fn (bool $state): string =>
                        $state ? 'Profil Aktif - Klik untuk nonaktifkan' : 'Nonaktif - Klik untuk aktifkan'
                    )
                    ->beforeStateUpdated(function ($record, $state) {
                        // Jika akan diaktifkan, nonaktifkan yang lain dulu
                        if ($state) {
                            ProfilBangJai::where('id', '!=', $record->id)
                                ->update(['is_active' => false]);
                        }
                    })
                    ->afterStateUpdated(function ($record, $state) {
                        \Filament\Notifications\Notification::make()
                            ->title('Status berhasil diubah')
                            ->body($state
                                ? 'Profil diaktifkan. Profil lain telah dinonaktifkan.'
                                : 'Profil dinonaktifkan'
                            )
                            ->success()
                            ->send();
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->label('Dibuat')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-calendar')
                    ->tooltip(fn ($state): string => $state->diffForHumans()),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->label('Diperbarui')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-clock')
                    ->since(),
            ])
            ->defaultSort('is_active', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua Status')
                    ->trueLabel('Profil Aktif')
                    ->falseLabel('Profil Nonaktif')
                    ->native(false),

                Tables\Filters\TernaryFilter::make('has_foto_profil')
                    ->label('Foto Profil')
                    ->placeholder('Semua')
                    ->trueLabel('Dengan Foto')
                    ->falseLabel('Tanpa Foto')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('foto_profil'),
                        false: fn (Builder $query) => $query->whereNull('foto_profil'),
                    )
                    ->native(false),

                Tables\Filters\TernaryFilter::make('has_foto_banner')
                    ->label('Banner')
                    ->placeholder('Semua')
                    ->trueLabel('Dengan Banner')
                    ->falseLabel('Tanpa Banner')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('foto_banner'),
                        false: fn (Builder $query) => $query->whereNull('foto_banner'),
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
                    Tables\Actions\Action::make('activate')
                        ->label('Jadikan Aktif')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Aktifkan Profil')
                        ->modalDescription('Profil lain yang sedang aktif akan dinonaktifkan. Lanjutkan?')
                        ->action(function (ProfilBangJai $record) {
                            ProfilBangJai::where('id', '!=', $record->id)
                                ->update(['is_active' => false]);
                            $record->update(['is_active' => true]);

                            \Filament\Notifications\Notification::make()
                                ->title('Profil berhasil diaktifkan')
                                ->success()
                                ->send();
                        })
                        ->visible(fn (ProfilBangJai $record): bool => !$record->is_active),
                    Tables\Actions\Action::make('view_website')
                        ->label('Lihat di Website')
                        ->icon('heroicon-o-globe-alt')
                        ->color('success')
                        ->url(fn (): string => url('/profil'))
                        ->openUrlInNewTab()
                        ->visible(fn (ProfilBangJai $record): bool => $record->is_active),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Profil')
                        ->modalDescription('Apakah Anda yakin ingin menghapus profil ini?')
                        ->modalSubmitActionLabel('Ya, Hapus')
                        ->before(function (ProfilBangJai $record) {
                            // Cegah hapus jika profil aktif
                            if ($record->is_active) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Tidak dapat menghapus')
                                    ->body('Profil yang sedang aktif tidak dapat dihapus. Nonaktifkan terlebih dahulu.')
                                    ->danger()
                                    ->send();

                                return false;
                            }
                        }),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Aksi')
                ->button()
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Profil Terpilih')
                        ->modalDescription('Profil yang sedang aktif tidak akan dihapus.')
                        ->modalSubmitActionLabel('Ya, Hapus')
                        ->before(function (\Illuminate\Database\Eloquent\Collection $records) {
                            // Filter hanya yang nonaktif
                            return $records->filter(fn($record) => !$record->is_active);
                        }),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Profil')
            ->emptyStateDescription('Mulai dengan membuat profil Bang Jai pertama.')
            ->emptyStateIcon('heroicon-o-user-circle')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Buat Profil Baru')
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
                Infolists\Components\Section::make('Informasi Profil')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\ImageEntry::make('foto_profil')
                                    ->label('Foto Profil')
                                    ->defaultImageUrl(url('/images/default-avatar.png'))
                                    ->height(200)
                                    ->columnSpan(1),

                                Infolists\Components\ImageEntry::make('foto_banner')
                                    ->label('Foto Banner')
                                    ->defaultImageUrl(url('/images/default-banner.png'))
                                    ->height(200)
                                    ->columnSpan(1),
                            ]),

                        Infolists\Components\TextEntry::make('judul')
                            ->label('Judul Profil')
                            ->size('lg')
                            ->weight('bold')
                            ->copyable()
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('is_active')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (bool $state): string =>
                                $state ? 'Profil Aktif' : 'Nonaktif'
                            )
                            ->color(fn (bool $state): string =>
                                $state ? 'success' : 'gray'
                            )
                            ->icon(fn (bool $state): string =>
                                $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'
                            )
                            ->size('lg'),

                        Infolists\Components\TextEntry::make('konten_profil')
                            ->label('Konten Profil')
                            ->html()
                            ->columnSpanFull(),

                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('word_count')
                                    ->label('Jumlah Kata')
                                    ->state(function (ProfilBangJai $record): string {
                                        $words = str_word_count(strip_tags($record->konten_profil));
                                        $minutes = ceil($words / 200);
                                        return number_format($words) . " kata (~{$minutes} menit baca)";
                                    })
                                    ->icon('heroicon-o-document-text')
                                    ->badge()
                                    ->color('primary'),

                                Infolists\Components\TextEntry::make('has_foto_profil')
                                    ->label('Status Foto Profil')
                                    ->state(fn (ProfilBangJai $record): string =>
                                        $record->foto_profil ? 'Tersedia' : 'Belum Ada'
                                    )
                                    ->badge()
                                    ->color(fn (ProfilBangJai $record): string =>
                                        $record->foto_profil ? 'success' : 'gray'
                                    )
                                    ->icon('heroicon-o-photo'),

                                Infolists\Components\TextEntry::make('has_foto_banner')
                                    ->label('Status Banner')
                                    ->state(fn (ProfilBangJai $record): string =>
                                        $record->foto_banner ? 'Tersedia' : 'Belum Ada'
                                    )
                                    ->badge()
                                    ->color(fn (ProfilBangJai $record): string =>
                                        $record->foto_banner ? 'success' : 'gray'
                                    )
                                    ->icon('heroicon-o-photo'),
                            ]),
                    ]),

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
            'index' => Pages\ListProfilBangJais::route('/'),
            'create' => Pages\CreateProfilBangJai::route('/create'),
            'view' => Pages\ViewProfilBangJai::route('/{record}'),
            'edit' => Pages\EditProfilBangJai::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $aktif = static::getModel()::where('is_active', true)->first();
        return $aktif ? '✓' : '!';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $aktif = static::getModel()::where('is_active', true)->exists();
        return $aktif ? 'success' : 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        $aktif = static::getModel()::where('is_active', true)->first();
        return $aktif
            ? "Profil aktif: {$aktif->judul}"
            : 'Belum ada profil aktif';
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Status' => $record->is_active ? 'Aktif' : 'Nonaktif',
            'Foto' => $record->foto_profil ? 'Ada' : 'Belum',
        ];
    }
}
