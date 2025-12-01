<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KatalogBantuanResource\Pages;
use App\Filament\Resources\KatalogBantuanResource\RelationManagers;
use App\Models\KatalogBantuan;
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
use Carbon\Carbon;

class KatalogBantuanResource extends Resource
{
    protected static ?string $model = KatalogBantuan::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Katalog Bantuan';

    protected static ?string $modelLabel = 'Katalog Bantuan';

    protected static ?string $pluralModelLabel = 'Data Katalog Bantuan';

    protected static ?string $navigationGroup = 'Data Utama';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'judul';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Katalog')
                    ->description('Data utama katalog bantuan')
                    ->icon('heroicon-o-book-open')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Select::make('jenis_bantuan_id')
                            ->relationship('jenisBantuan', 'nama_bantuan')
                            ->searchable()
                            ->preload()
                            ->label('Jenis Bantuan')
                            ->placeholder('Pilih Jenis Bantuan (Opsional)')
                            ->helperText('Pilih jenis bantuan jika katalog terkait dengan bantuan tertentu')
                            ->prefixIcon('heroicon-o-gift')
                            ->native(false)
                            ->createOptionForm([
                                Forms\Components\Select::make('kategori_bantuan_id')
                                    ->relationship('kategoriBantuan', 'nama_kategori')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\TextInput::make('nama_bantuan')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('periode_tahun')
                                    ->required()
                                    ->numeric()
                                    ->default(now()->year),
                                Forms\Components\Toggle::make('is_active')
                                    ->default(true),
                            ])
                            ->createOptionModalHeading('Tambah Jenis Bantuan Baru')
                            ->columnSpan(2),

                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true)
                            ->label('Status Aktif')
                            ->helperText('Aktifkan untuk menampilkan di katalog')
                            ->inline(false)
                            ->onColor('success')
                            ->offColor('gray')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('judul')
                            ->required()
                            ->maxLength(255)
                            ->label('Judul Katalog')
                            ->placeholder('Masukkan judul katalog yang menarik...')
                            ->helperText('Judul katalog bantuan')
                            ->prefixIcon('heroicon-o-tag')
                            ->autocomplete(false)
                            ->columnSpanFull()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                $set('slug', Str::slug($state));
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->label('Slug')
                            ->placeholder('Slug otomatis dibuat dari judul')
                            ->helperText('Bagian URL yang unik untuk katalog ini (diisi otomatis)')
                            ->prefixIcon('heroicon-o-link')
                            ->autocomplete(false)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('deskripsi')
                            ->required()
                            ->label('Deskripsi')
                            ->rows(4)
                            ->maxLength(1000)
                            ->placeholder('Deskripsikan katalog bantuan: manfaat, persyaratan, cara mendaftar...')
                            ->helperText('Penjelasan lengkap tentang katalog bantuan ini')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Periode Katalog')
                    ->description('Atur waktu aktif katalog')
                    ->icon('heroicon-o-calendar')
                    ->collapsible()
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_mulai')
                            ->required()
                            ->label('Tanggal Mulai')
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->default(now())
                            ->helperText('Tanggal mulai katalog ditampilkan')
                            ->prefixIcon('heroicon-o-calendar-days')
                            ->closeOnDateSelection()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                // Auto-set selesai 30 hari dari mulai jika kosong
                                if ($state && !$get('tanggal_selesai')) {
                                    $set('tanggal_selesai', Carbon::parse($state)->addDays(30));
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('tanggal_selesai')
                            ->required()
                            ->label('Tanggal Selesai')
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->helperText('Tanggal selesai katalog ditampilkan')
                            ->prefixIcon('heroicon-o-calendar')
                            ->closeOnDateSelection()
                            ->minDate(fn (Forms\Get $get) => $get('tanggal_mulai'))
                            ->columnSpan(1),

                        Forms\Components\Placeholder::make('durasi')
                            ->label('Durasi Katalog')
                            ->content(function (Forms\Get $get): string {
                                $mulai = $get('tanggal_mulai');
                                $selesai = $get('tanggal_selesai');

                                if (!$mulai || !$selesai) return '-';

                                $diff = Carbon::parse($mulai)->diffInDays(Carbon::parse($selesai));
                                return "📅 {$diff} hari aktif";
                            })
                            ->columnSpan(2)
                            ->visible(fn (?KatalogBantuan $record) => $record === null),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Media Katalog')
                    ->description('Upload foto katalog bantuan')
                    ->icon('heroicon-o-photo')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\FileUpload::make('foto')
                            ->image()
                            ->label('Foto Katalog')
                            ->directory('katalog-bantuan')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->imagePreviewHeight('250')
                            ->helperText('📸 Upload foto katalog (Rekomendasi: 1200x675px, max 2MB)')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048)
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('image_tips')
                            ->label('💡 Tips Foto')
                            ->content('Gunakan foto yang representatif dan berkualitas baik. Format landscape 16:9 atau 4:3 untuk hasil optimal.')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Informasi Tambahan')
                    ->description('Status dan statistik')
                    ->icon('heroicon-o-information-circle')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('status_periode')
                            ->label('Status Periode Saat Ini')
                            ->content(fn (?KatalogBantuan $record): string =>
                                $record ? self::getStatusPeriode($record) : 'Status akan tampil setelah disimpan'
                            ),

                        Forms\Components\Placeholder::make('sisa_waktu')
                            ->label('Sisa Waktu Aktif')
                            ->content(function (?KatalogBantuan $record): string {
                                if (!$record) return '-';

                                $now = now();
                                $selesai = Carbon::parse($record->tanggal_selesai);

                                if ($now->gt($selesai)) {
                                    return '⏰ Sudah berakhir';
                                }

                                $diff = $now->diffInDays($selesai);
                                return "⏰ {$diff} hari lagi";
                            })
                            ->visible(fn (?KatalogBantuan $record) => $record !== null),

                        Forms\Components\Placeholder::make('word_count')
                            ->label('Panjang Deskripsi')
                            ->content(function (Forms\Get $get): string {
                                $deskripsi = $get('deskripsi') ?? '';
                                $wordCount = str_word_count($deskripsi);
                                $charCount = strlen($deskripsi);
                                return "{$wordCount} kata, {$charCount} karakter";
                            }),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Dibuat pada')
                            ->content(fn (?KatalogBantuan $record): string =>
                                $record?->created_at?->format('d F Y, H:i') . ' (' . $record?->created_at?->diffForHumans() . ')' ?? '-'
                            )
                            ->visible(fn (?KatalogBantuan $record) => $record !== null),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->content(fn (?KatalogBantuan $record): string =>
                                $record?->updated_at?->diffForHumans() ?? '-'
                            )
                            ->visible(fn (?KatalogBantuan $record) => $record !== null),
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

                Tables\Columns\ImageColumn::make('foto')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-catalog.png'))
                    ->size(60),

                Tables\Columns\TextColumn::make('judul')
                    ->searchable()
                    ->label('Judul Katalog')
                    ->icon('heroicon-o-book-open')
                    ->iconColor('primary')
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Judul tersalin!')
                    ->description(fn (KatalogBantuan $record): string =>
                        Str::limit($record->deskripsi, 60)
                    )
                    ->wrap()
                    ->limit(40)
                    ->tooltip(fn (KatalogBantuan $record): string => $record->judul),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->label('Slug')
                    ->icon('heroicon-o-link')
                    ->iconColor('info')
                    ->copyable()
                    ->copyMessage('Slug tersalin!')
                    ->limit(30)
                    ->tooltip(fn (KatalogBantuan $record): string => $record->slug)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('jenisBantuan.nama_bantuan')
                    ->searchable()
                    ->sortable()
                    ->label('Jenis Bantuan')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-gift')
                    ->placeholder('Umum')
                    ->tooltip('Jenis bantuan terkait'),

                Tables\Columns\TextColumn::make('status_periode')
                    ->label('Status Periode')
                    ->badge()
                    ->state(fn (KatalogBantuan $record): string =>
                        self::getStatusPeriodeLabel($record)
                    )
                    ->color(fn (KatalogBantuan $record): string =>
                        self::getStatusPeriodeColor($record)
                    )
                    ->icon(fn (KatalogBantuan $record): string =>
                        self::getStatusPeriodeIcon($record)
                    )
                    ->alignCenter()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('tanggal_mulai', $direction);
                    })
                    ->tooltip(fn (KatalogBantuan $record): string =>
                        self::getStatusPeriode($record)
                    ),

                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->date('d M Y')
                    ->sortable()
                    ->label('Mulai')
                    ->icon('heroicon-o-calendar-days')
                    ->iconColor('success')
                    ->tooltip(fn ($state): string =>
                        Carbon::parse($state)->translatedFormat('l, d F Y')
                    ),

                Tables\Columns\TextColumn::make('tanggal_selesai')
                    ->date('d M Y')
                    ->sortable()
                    ->label('Selesai')
                    ->icon('heroicon-o-calendar')
                    ->iconColor('warning')
                    ->tooltip(fn ($state): string =>
                        Carbon::parse($state)->translatedFormat('l, d F Y')
                    ),

                Tables\Columns\TextColumn::make('durasi')
                    ->label('Durasi')
                    ->state(function (KatalogBantuan $record): string {
                        $mulai = Carbon::parse($record->tanggal_mulai);
                        $selesai = Carbon::parse($record->tanggal_selesai);
                        $diff = $mulai->diffInDays($selesai);
                        return $diff . ' hari';
                    })
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-clock')
                    ->alignCenter()
                    ->toggleable(),

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
                            ->body($state ? 'Katalog diaktifkan' : 'Katalog dinonaktifkan')
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
            ])
            ->defaultSort('tanggal_mulai', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_bantuan_id')
                    ->label('Jenis Bantuan')
                    ->relationship('jenisBantuan', 'nama_bantuan')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('Semua Jenis')
                    ->native(false)
                    ->indicator('Jenis'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif')
                    ->native(false),

                Tables\Filters\Filter::make('status_periode')
                    ->label('Status Periode')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Status Periode')
                            ->options([
                                'belum_mulai' => 'Belum Mulai',
                                'sedang_berjalan' => 'Sedang Berjalan',
                                'sudah_berakhir' => 'Sudah Berakhir',
                            ])
                            ->placeholder('Semua Status')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['status'],
                            function (Builder $query, string $status): Builder {
                                $now = now();
                                return match($status) {
                                    'belum_mulai' => $query->where('tanggal_mulai', '>', $now),
                                    'sedang_berjalan' => $query->where('tanggal_mulai', '<=', $now)
                                        ->where('tanggal_selesai', '>=', $now),
                                    'sudah_berakhir' => $query->where('tanggal_selesai', '<', $now),
                                    default => $query,
                                };
                            }
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['status'] ?? null) {
                            return match($data['status']) {
                                'belum_mulai' => 'Belum Mulai',
                                'sedang_berjalan' => 'Sedang Berjalan',
                                'sudah_berakhir' => 'Sudah Berakhir',
                            };
                        }
                        return null;
                    }),

                Tables\Filters\Filter::make('tanggal_mulai')
                    ->form([
                        Forms\Components\DatePicker::make('tanggal_mulai_from')
                            ->label('Tanggal Mulai Dari')
                            ->native(false),
                        Forms\Components\DatePicker::make('tanggal_mulai_until')
                            ->label('Tanggal Mulai Sampai')
                            ->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['tanggal_mulai_from'],
                                fn ($query, $date) => $query->whereDate('tanggal_mulai', '>=', $date)
                            )
                            ->when(
                                $data['tanggal_mulai_until'],
                                fn ($query, $date) => $query->whereDate('tanggal_mulai', '<=', $date)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['tanggal_mulai_from'] ?? null) {
                            $indicators[] = 'Mulai dari: ' . Carbon::parse($data['tanggal_mulai_from'])->format('d M Y');
                        }
                        if ($data['tanggal_mulai_until'] ?? null) {
                            $indicators[] = 'Mulai sampai: ' . Carbon::parse($data['tanggal_mulai_until'])->format('d M Y');
                        }
                        return $indicators;
                    }),

                Tables\Filters\Filter::make('tanggal_selesai')
                    ->form([
                        Forms\Components\DatePicker::make('tanggal_selesai_from')
                            ->label('Tanggal Selesai Dari')
                            ->native(false),
                        Forms\Components\DatePicker::make('tanggal_selesai_until')
                            ->label('Tanggal Selesai Sampai')
                            ->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['tanggal_selesai_from'],
                                fn ($query, $date) => $query->whereDate('tanggal_selesai', '>=', $date)
                            )
                            ->when(
                                $data['tanggal_selesai_until'],
                                fn ($query, $date) => $query->whereDate('tanggal_selesai', '<=', $date)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['tanggal_selesai_from'] ?? null) {
                            $indicators[] = 'Selesai dari: ' . Carbon::parse($data['tanggal_selesai_from'])->format('d M Y');
                        }
                        if ($data['tanggal_selesai_until'] ?? null) {
                            $indicators[] = 'Selesai sampai: ' . Carbon::parse($data['tanggal_selesai_until'])->format('d M Y');
                        }
                        return $indicators;
                    }),

                Tables\Filters\Filter::make('aktif_sekarang')
                    ->label('Aktif Sekarang')
                    ->query(fn (Builder $query): Builder =>
                        $query->where('is_active', true)
                            ->where('tanggal_mulai', '<=', now())
                            ->where('tanggal_selesai', '>=', now())
                    )
                    ->toggle(),

                Tables\Filters\TernaryFilter::make('has_foto')
                    ->label('Foto')
                    ->placeholder('Semua')
                    ->trueLabel('Dengan Foto')
                    ->falseLabel('Tanpa Foto')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('foto'),
                        false: fn (Builder $query) => $query->whereNull('foto'),
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
                        ->label(fn (KatalogBantuan $record): string =>
                            $record->is_active ? 'Nonaktifkan' : 'Aktifkan'
                        )
                        ->icon(fn (KatalogBantuan $record): string =>
                            $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle'
                        )
                        ->color(fn (KatalogBantuan $record): string =>
                            $record->is_active ? 'gray' : 'success'
                        )
                        ->requiresConfirmation()
                        ->action(function (KatalogBantuan $record) {
                            $record->update(['is_active' => !$record->is_active]);
                            \Filament\Notifications\Notification::make()
                                ->title('Status berhasil diubah')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Katalog')
                        ->modalDescription('Apakah Anda yakin ingin menghapus katalog bantuan ini?')
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
                                ->title('Katalog berhasil diaktifkan')
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
                                ->title('Katalog berhasil dinonaktifkan')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Katalog Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus semua katalog yang dipilih?')
                        ->modalSubmitActionLabel('Ya, Hapus Semua'),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Katalog Bantuan')
            ->emptyStateDescription('Mulai dengan membuat katalog bantuan pertama.')
            ->emptyStateIcon('heroicon-o-book-open')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Buat Katalog Baru')
                    ->icon('heroicon-o-plus')
                    ->color('primary'),
            ])
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('30s');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Katalog Bantuan')
                    ->icon('heroicon-o-book-open')
                    ->schema([
                        Infolists\Components\ImageEntry::make('foto')
                            ->label('Foto Katalog')
                            ->defaultImageUrl(url('/images/default-catalog.png'))
                            ->columnSpanFull(),

                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('is_active')
                                    ->label('Status Aktif')
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

                                Infolists\Components\TextEntry::make('status_periode')
                                    ->label('Status Periode')
                                    ->state(fn (KatalogBantuan $record): string =>
                                        self::getStatusPeriodeLabel($record)
                                    )
                                    ->badge()
                                    ->color(fn (KatalogBantuan $record): string =>
                                        self::getStatusPeriodeColor($record)
                                    ),

                                Infolists\Components\TextEntry::make('jenisBantuan.nama_bantuan')
                                    ->label('Jenis Bantuan')
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-gift')
                                    ->placeholder('Umum'),
                            ]),

                        Infolists\Components\TextEntry::make('judul')
                            ->label('Judul')
                            ->size('lg')
                            ->weight('bold')
                            ->copyable()
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('deskripsi')
                            ->label('Deskripsi')
                            ->columnSpanFull(),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('tanggal_mulai')
                                    ->label('Tanggal Mulai')
                                    ->date('d F Y')
                                    ->icon('heroicon-o-calendar-days'),

                                Infolists\Components\TextEntry::make('tanggal_selesai')
                                    ->label('Tanggal Selesai')
                                    ->date('d F Y')
                                    ->icon('heroicon-o-calendar'),
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
            'index' => Pages\ListKatalogBantuans::route('/'),
            'create' => Pages\CreateKatalogBantuan::route('/create'),
            'view' => Pages\ViewKatalogBantuan::route('/{record}'),
            'edit' => Pages\EditKatalogBantuan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $aktif = static::getModel()::where('is_active', true)
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->count();

        return $aktif > 0 ? (string) $aktif : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        $aktif = static::getModel()::where('is_active', true)
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->count();

        return "{$aktif} katalog aktif sekarang";
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Jenis' => $record->jenisBantuan?->nama_bantuan ?? 'Umum',
            'Status' => self::getStatusPeriodeLabel($record),
        ];
    }

    // Helper methods
    protected static function getStatusPeriode(KatalogBantuan $record): string
    {
        $now = now();
        $mulai = Carbon::parse($record->tanggal_mulai);
        $selesai = Carbon::parse($record->tanggal_selesai);

        if ($now->lt($mulai)) {
            $diff = $now->diffInDays($mulai);
            return "Akan dimulai dalam {$diff} hari";
        }

        if ($now->between($mulai, $selesai)) {
            $diff = $now->diffInDays($selesai);
            return "Sedang berjalan ({$diff} hari lagi)";
        }

        $diff = $now->diffInDays($selesai);
        return "Berakhir {$diff} hari yang lalu";
    }

    protected static function getStatusPeriodeLabel(KatalogBantuan $record): string
    {
        $now = now();
        $mulai = Carbon::parse($record->tanggal_mulai);
        $selesai = Carbon::parse($record->tanggal_selesai);

        if ($now->lt($mulai)) {
            return 'Belum Mulai';
        }

        if ($now->between($mulai, $selesai)) {
            return 'Sedang Berjalan';
        }

        return 'Sudah Berakhir';
    }

    protected static function getStatusPeriodeColor(KatalogBantuan $record): string
    {
        $now = now();
        $mulai = Carbon::parse($record->tanggal_mulai);
        $selesai = Carbon::parse($record->tanggal_selesai);

        if ($now->lt($mulai)) {
            return 'warning';
        }

        if ($now->between($mulai, $selesai)) {
            return 'success';
        }

        return 'gray';
    }

    protected static function getStatusPeriodeIcon(KatalogBantuan $record): string
    {
        $now = now();
        $mulai = Carbon::parse($record->tanggal_mulai);
        $selesai = Carbon::parse($record->tanggal_selesai);

        if ($now->lt($mulai)) {
            return 'heroicon-o-clock';
        }

        if ($now->between($mulai, $selesai)) {
            return 'heroicon-o-signal';
        }

        return 'heroicon-o-x-circle';
    }
}
