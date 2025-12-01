<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoriArtikelResource\Pages;
use App\Filament\Resources\KategoriArtikelResource\RelationManagers;
use App\Models\KategoriArtikel;
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

class KategoriArtikelResource extends Resource
{
    protected static ?string $model = KategoriArtikel::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static ?string $navigationLabel = 'Kategori Artikel';

    protected static ?string $modelLabel = 'Kategori Artikel';

    protected static ?string $pluralModelLabel = 'Data Kategori Artikel';

    protected static ?string $navigationGroup = 'Blog & Konten';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'nama_kategori';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kategori Artikel')
                    ->description('Data utama kategori artikel blog')
                    ->icon('heroicon-o-folder-open')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('nama_kategori')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Kategori')
                            ->placeholder('Contoh: Berita Pertanian, Tips & Trik')
                            ->helperText('Nama kategori untuk mengelompokkan artikel')
                            ->prefixIcon('heroicon-o-tag')
                            ->autocomplete(false)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                // Auto-generate slug
                                if ($state && !$get('slug')) {
                                    $set('slug', Str::slug($state));
                                }
                                // Auto-generate deskripsi jika kosong
                                if ($state && !$get('deskripsi')) {
                                    $set('deskripsi', "Kumpulan artikel seputar {$state}");
                                }
                            })
                            ->columnSpan(2),

                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->label('Status Aktif')
                            ->default(true)
                            ->helperText('Aktifkan untuk menampilkan di website')
                            ->inline(false)
                            ->onColor('success')
                            ->offColor('gray')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->label('Slug URL')
                            ->placeholder('berita-pertanian')
                            ->helperText('URL-friendly identifier (otomatis dibuat dari nama)')
                            ->prefixIcon('heroicon-o-link')
                            ->prefix(url('/kategori/'))
                            ->alphaDash()
                            ->rules(['regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'])
                            ->validationMessages([
                                'regex' => 'Slug harus lowercase, angka, dan strip (-) saja',
                            ])
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $set('slug', Str::slug($state));
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Detail Kategori')
                    ->description('Deskripsi dan informasi tambahan')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\RichEditor::make('deskripsi')
                            ->label('Deskripsi')
                            ->columnSpanFull()
                            ->placeholder('Deskripsikan kategori artikel ini...')
                            ->helperText('Penjelasan tentang jenis artikel dalam kategori ini')
                            ->toolbarButtons([
                                'bold',
                                'bulletList',
                                'h2',
                                'h3',
                                'italic',
                                'orderedList',
                                'redo',
                                'undo',
                            ])
                            ->maxLength(1000),
                    ]),

                Forms\Components\Section::make('Preview URL')
                    ->description('Pratinjau link kategori')
                    ->icon('heroicon-o-globe-alt')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('url_preview')
                            ->label('URL Kategori')
                            ->content(function (Forms\Get $get, ?KategoriArtikel $record): string {
                                $slug = $get('slug') ?: ($record?->slug ?? 'slug-belum-diisi');
                                return url('/kategori/' . $slug);
                            })
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($operation) => $operation === 'edit' || $operation === 'create'),

                Forms\Components\Section::make('Informasi Tambahan')
                    ->description('Data statistik dan riwayat')
                    ->icon('heroicon-o-chart-bar')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('jumlah_artikel')
                            ->label('Jumlah Artikel')
                            ->content(fn (?KategoriArtikel $record): string =>
                                $record
                                    ? number_format($record->artikels()->count()) . ' artikel'
                                    : '-'
                            )
                            ->visible(fn (?KategoriArtikel $record) => $record !== null),

                        Forms\Components\Placeholder::make('artikel_published')
                            ->label('Artikel Terpublikasi')
                            ->content(fn (?KategoriArtikel $record): string =>
                                $record
                                    ? number_format($record->artikels()->where('status', 'published')->count()) . ' artikel'
                                    : '-'
                            )
                            ->visible(fn (?KategoriArtikel $record) => $record !== null),

                        Forms\Components\Placeholder::make('artikel_draft')
                            ->label('Artikel Draft')
                            ->content(fn (?KategoriArtikel $record): string =>
                                $record
                                    ? number_format($record->artikels()->where('status', 'draft')->count()) . ' artikel'
                                    : '-'
                            )
                            ->visible(fn (?KategoriArtikel $record) => $record !== null),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Dibuat pada')
                            ->content(fn (?KategoriArtikel $record): string =>
                                $record?->created_at?->format('d F Y, H:i') . ' (' . $record?->created_at?->diffForHumans() . ')' ?? '-'
                            )
                            ->visible(fn (?KategoriArtikel $record) => $record !== null),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->content(fn (?KategoriArtikel $record): string =>
                                $record?->updated_at?->diffForHumans() ?? '-'
                            )
                            ->visible(fn (?KategoriArtikel $record) => $record !== null),
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

                Tables\Columns\TextColumn::make('nama_kategori')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Kategori')
                    ->icon('heroicon-o-tag')
                    ->iconColor('primary')
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Nama kategori tersalin!')
                    ->description(fn (KategoriArtikel $record): string =>
                        Str::limit(strip_tags($record->deskripsi), 60) ?: 'Belum ada deskripsi'
                    )
                    ->wrap()
                    ->tooltip('Klik untuk menyalin'),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->label('Slug')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-link')
                    ->copyable()
                    ->copyMessage('Slug tersalin!')
                    ->prefix('/')
                    ->tooltip(fn (KategoriArtikel $record): string =>
                        url('/kategori/' . $record->slug)
                    ),

                Tables\Columns\TextColumn::make('artikels_count')
                    ->label('Total Artikel')
                    ->counts('artikels')
                    ->badge()
                    ->color(fn ($state): string => match(true) {
                        $state == 0 => 'gray',
                        $state <= 5 => 'warning',
                        $state <= 15 => 'info',
                        $state > 15 => 'success',
                    })
                    ->icon('heroicon-o-document-text')
                    ->suffix(' Artikel')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip(fn ($state): string => $state . ' artikel total'),

                Tables\Columns\TextColumn::make('published_count')
                    ->label('Terpublikasi')
                    ->state(function (KategoriArtikel $record): int {
                        return $record->artikels()->where('status', 'published')->count();
                    })
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->suffix(' Published')
                    ->alignCenter()
                    ->tooltip('Artikel yang sudah dipublikasikan'),

                Tables\Columns\TextColumn::make('draft_count')
                    ->label('Draft')
                    ->state(function (KategoriArtikel $record): int {
                        return $record->artikels()->where('status', 'draft')->count();
                    })
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-o-pencil-square')
                    ->suffix(' Draft')
                    ->alignCenter()
                    ->toggleable()
                    ->tooltip('Artikel draft yang belum dipublikasi'),

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
                            ->body($state ? 'Kategori diaktifkan' : 'Kategori dinonaktifkan')
                            ->success()
                            ->send();
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-calendar')
                    ->tooltip(fn ($state): string => $state->diffForHumans()),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-clock')
                    ->since(),
            ])
            ->defaultSort('nama_kategori', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif')
                    ->native(false),

                Tables\Filters\TernaryFilter::make('has_artikel')
                    ->label('Memiliki Artikel')
                    ->placeholder('Semua Kategori')
                    ->trueLabel('Dengan Artikel')
                    ->falseLabel('Tanpa Artikel')
                    ->queries(
                        true: fn (Builder $query) => $query->has('artikels'),
                        false: fn (Builder $query) => $query->doesntHave('artikels'),
                    )
                    ->native(false),

                Tables\Filters\Filter::make('populer')
                    ->label('Kategori Populer')
                    ->query(function (Builder $query): Builder {
                        return $query->has('artikels', '>=', 10);
                    })
                    ->toggle(),
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
                        ->label(fn (KategoriArtikel $record): string =>
                            $record->is_active ? 'Nonaktifkan' : 'Aktifkan'
                        )
                        ->icon(fn (KategoriArtikel $record): string =>
                            $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle'
                        )
                        ->color(fn (KategoriArtikel $record): string =>
                            $record->is_active ? 'gray' : 'success'
                        )
                        ->requiresConfirmation()
                        ->action(function (KategoriArtikel $record) {
                            $record->update(['is_active' => !$record->is_active]);
                            \Filament\Notifications\Notification::make()
                                ->title('Status berhasil diubah')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('view_website')
                        ->label('Lihat di Website')
                        ->icon('heroicon-o-globe-alt')
                        ->color('success')
                        ->url(fn (KategoriArtikel $record): string =>
                            url('/kategori/' . $record->slug)
                        )
                        ->openUrlInNewTab(),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Kategori Artikel')
                        ->modalDescription('Apakah Anda yakin ingin menghapus kategori ini? Artikel dalam kategori ini mungkin terpengaruh.')
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
                                ->title('Kategori berhasil diaktifkan')
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
                                ->title('Kategori berhasil dinonaktifkan')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Kategori Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus semua kategori yang dipilih? Data artikel terkait mungkin terpengaruh.')
                        ->modalSubmitActionLabel('Ya, Hapus Semua'),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Kategori Artikel')
            ->emptyStateDescription('Mulai dengan menambahkan kategori artikel untuk mengelompokkan konten.')
            ->emptyStateIcon('heroicon-o-folder-open')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Kategori Artikel')
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
                Infolists\Components\Section::make('Informasi Kategori')
                    ->icon('heroicon-o-folder-open')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('nama_kategori')
                                    ->label('Nama Kategori')
                                    ->icon('heroicon-o-tag')
                                    ->iconColor('primary')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->copyable()
                                    ->color('primary'),

                                Infolists\Components\TextEntry::make('slug')
                                    ->label('Slug URL')
                                    ->icon('heroicon-o-link')
                                    ->badge()
                                    ->color('info')
                                    ->copyable()
                                    ->prefix('/'),

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
                                    )
                                    ->size('lg'),
                            ]),

                        Infolists\Components\TextEntry::make('url_lengkap')
                            ->label('URL Website')
                            ->state(fn (KategoriArtikel $record): string =>
                                url('/kategori/' . $record->slug)
                            )
                            ->icon('heroicon-o-globe-alt')
                            ->color('success')
                            ->copyable()
                            ->url(fn (KategoriArtikel $record): string =>
                                url('/kategori/' . $record->slug)
                            )
                            ->openUrlInNewTab()
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('deskripsi')
                            ->label('Deskripsi')
                            ->html()
                            ->default('Belum ada deskripsi')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Statistik Kategori')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('total_artikel')
                                    ->label('Total Artikel')
                                    ->state(fn (KategoriArtikel $record): int => $record->artikels()->count())
                                    ->badge()
                                    ->icon('heroicon-o-document-text')
                                    ->color('info')
                                    ->suffix(' Artikel')
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('published')
                                    ->label('Terpublikasi')
                                    ->state(fn (KategoriArtikel $record): int =>
                                        $record->artikels()->where('status', 'published')->count()
                                    )
                                    ->badge()
                                    ->icon('heroicon-o-check-circle')
                                    ->color('success')
                                    ->suffix(' Published')
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('draft')
                                    ->label('Draft')
                                    ->state(fn (KategoriArtikel $record): int =>
                                        $record->artikels()->where('status', 'draft')->count()
                                    )
                                    ->badge()
                                    ->icon('heroicon-o-pencil-square')
                                    ->color('warning')
                                    ->suffix(' Draft')
                                    ->size('lg'),
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
            'index' => Pages\ListKategoriArtikels::route('/'),
            'create' => Pages\CreateKategoriArtikel::route('/create'),
            'view' => Pages\ViewKategoriArtikel::route('/{record}'),
            'edit' => Pages\EditKategoriArtikel::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $active = static::getModel()::where('is_active', true)->count();
        $total = static::getModel()::count();
        return "{$active}/{$total}";
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $active = static::getModel()::where('is_active', true)->count();
        $total = static::getModel()::count();

        if ($total == 0) return 'gray';

        $percentage = ($active / $total) * 100;
        return match(true) {
            $percentage < 50 => 'danger',
            $percentage < 80 => 'warning',
            default => 'success',
        };
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        $artikelCount = $record->artikels()->count();

        return [
            'Slug' => $record->slug,
            'Artikel' => "{$artikelCount} artikel",
            'Status' => $record->is_active ? 'Aktif' : 'Nonaktif',
        ];
    }
}
