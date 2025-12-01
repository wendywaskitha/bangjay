<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Infolists;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\JenisKomoditas;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\JenisKomoditasResource\Pages;
use App\Filament\Resources\JenisKomoditasResource\RelationManagers;

class JenisKomoditasResource extends Resource
{
    protected static ?string $model = JenisKomoditas::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Jenis Komoditas';

    protected static ?string $modelLabel = 'Jenis Komoditas';

    protected static ?string $pluralModelLabel = 'Data Jenis Komoditas';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'nama_komoditas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Komoditas')
                    ->description('Data utama komoditas pertanian')
                    ->icon('heroicon-o-sparkles')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('nama_komoditas')
                            ->label('Nama Komoditas')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Padi, Jagung, Kakao')
                            ->helperText('Nama komoditas pertanian yang dibudidayakan')
                            ->prefixIcon('heroicon-o-beaker')
                            ->autocomplete(false)
                            ->unique(ignoreRecord: true)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                // Auto-set deskripsi default jika kosong
                                if ($state && !$get('deskripsi')) {
                                    $set('deskripsi', "Komoditas {$state} adalah salah satu komoditas pertanian yang dibudidayakan oleh kelompok tani.");
                                }
                            })
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->required()
                            ->default(true)
                            ->helperText('Aktifkan untuk menampilkan di form kelompok tani')
                            ->inline(false)
                            ->onColor('success')
                            ->offColor('gray')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detail Komoditas')
                    ->description('Informasi tambahan tentang komoditas')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\RichEditor::make('deskripsi')
                            ->label('Deskripsi')
                            ->columnSpanFull()
                            ->placeholder('Deskripsikan komoditas: karakteristik, manfaat, cara budidaya, dll...')
                            ->helperText('Penjelasan detail tentang komoditas ini')
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
                            ->maxLength(2000),
                    ]),

                Forms\Components\Section::make('Informasi Tambahan')
                    ->description('Data statistik dan penggunaan')
                    ->icon('heroicon-o-chart-bar')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('jumlah_petani')
                            ->label('Jumlah Petani')
                            ->content(function (?JenisKomoditas $record): string {
                                if (!$record) return '-';

                                $count = DB::table('kelompok_tani_anggotas')
                                    ->where('jenis_komoditas_id', $record->id)
                                    ->count();

                                return number_format($count) . ' Petani';
                            })
                            ->visible(fn (?JenisKomoditas $record) => $record !== null),

                        Forms\Components\Placeholder::make('jumlah_kelompok')
                            ->label('Jumlah Kelompok Tani')
                            ->content(function (?JenisKomoditas $record): string {
                                if (!$record) return '-';

                                $count = DB::table('kelompok_tani_anggotas')
                                    ->where('jenis_komoditas_id', $record->id)
                                    ->distinct('kelompok_tani_id')
                                    ->count('kelompok_tani_id');

                                return number_format($count) . ' Kelompok';
                            })
                            ->visible(fn (?JenisKomoditas $record) => $record !== null),

                        Forms\Components\Placeholder::make('total_luas_lahan')
                            ->label('Total Luas Lahan')
                            ->content(function (?JenisKomoditas $record): string {
                                if (!$record) return '-';

                                $total = DB::table('kelompok_tani_anggotas')
                                    ->where('jenis_komoditas_id', $record->id)
                                    ->sum('luas_lahan');

                                return number_format($total, 2) . ' Ha';
                            })
                            ->visible(fn (?JenisKomoditas $record) => $record !== null),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Terdaftar Sejak')
                            ->content(fn (?JenisKomoditas $record): string =>
                                $record?->created_at?->format('d F Y') . ' (' . $record?->created_at?->diffForHumans() . ')' ?? '-'
                            )
                            ->visible(fn (?JenisKomoditas $record) => $record !== null),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->content(fn (?JenisKomoditas $record): string =>
                                $record?->updated_at?->diffForHumans() ?? '-'
                            )
                            ->visible(fn (?JenisKomoditas $record) => $record !== null),
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

                Tables\Columns\TextColumn::make('nama_komoditas')
                    ->label('Nama Komoditas')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-sparkles')
                    ->iconColor('success')
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Nama komoditas tersalin!')
                    ->description(fn (JenisKomoditas $record): string =>
                        Str::limit(strip_tags($record->deskripsi), 60) ?: 'Belum ada deskripsi'
                    )
                    ->wrap()
                    ->tooltip('Klik untuk menyalin'),

                Tables\Columns\TextColumn::make('petani_count')
                    ->label('Petani')
                    ->state(function (JenisKomoditas $record): int {
                        return DB::table('kelompok_tani_anggotas')
                            ->where('jenis_komoditas_id', $record->id)
                            ->count();
                    })
                    ->badge()
                    ->color(fn ($state): string => match(true) {
                        $state == 0 => 'gray',
                        $state <= 10 => 'warning',
                        $state <= 50 => 'info',
                        $state > 50 => 'success',
                    })
                    ->icon('heroicon-o-user')
                    ->suffix(' Petani')
                    ->alignCenter()
                    ->sortable()
                    ->tooltip(fn ($state): string => $state . ' petani menanam komoditas ini'),

                Tables\Columns\TextColumn::make('luas_lahan_total')
                    ->label('Total Lahan')
                    ->state(function (JenisKomoditas $record): float {
                        return DB::table('kelompok_tani_anggotas')
                            ->where('jenis_komoditas_id', $record->id)
                            ->sum('luas_lahan');
                    })
                    ->numeric(decimalPlaces: 2)
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-o-map')
                    ->suffix(' Ha')
                    ->alignCenter()
                    ->sortable()
                    ->tooltip(fn ($state): string => number_format($state, 2) . ' hektar luas lahan'),

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
                            ->body($state ? 'Komoditas diaktifkan' : 'Komoditas dinonaktifkan')
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
                    ->tooltip(fn ($state): string => $state->diffForHumans())
                    ->since(),
            ])
            ->defaultSort('nama_komoditas', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif')
                    ->native(false),

                Tables\Filters\TernaryFilter::make('has_petani')
                    ->label('Memiliki Petani')
                    ->placeholder('Semua Komoditas')
                    ->trueLabel('Dengan Petani')
                    ->falseLabel('Tanpa Petani')
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('anggotaKelompokTanis'),
                        false: fn (Builder $query) => $query->whereDoesntHave('anggotaKelompokTanis'),
                    )
                    ->native(false),

                Tables\Filters\Filter::make('populer')
                    ->label('Komoditas Populer')
                    ->query(function (Builder $query): Builder {
                        return $query->whereHas('anggotaKelompokTanis', function($q) {
                            $q->havingRaw('COUNT(*) >= 10');
                        });
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
                        ->label(fn (JenisKomoditas $record): string =>
                            $record->is_active ? 'Nonaktifkan' : 'Aktifkan'
                        )
                        ->icon(fn (JenisKomoditas $record): string =>
                            $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle'
                        )
                        ->color(fn (JenisKomoditas $record): string =>
                            $record->is_active ? 'gray' : 'success'
                        )
                        ->requiresConfirmation()
                        ->action(function (JenisKomoditas $record) {
                            $record->update(['is_active' => !$record->is_active]);
                            \Filament\Notifications\Notification::make()
                                ->title('Status berhasil diubah')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Komoditas')
                        ->modalDescription('Apakah Anda yakin ingin menghapus komoditas ini? Data anggota kelompok tani yang menanam komoditas ini akan terpengaruh.')
                        ->modalSubmitActionLabel('Ya, Hapus')
                        ->icon('heroicon-o-trash'),
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
                                ->title('Komoditas berhasil diaktifkan')
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
                                ->title('Komoditas berhasil dinonaktifkan')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Komoditas Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus semua komoditas yang dipilih? Data terkait akan terpengaruh.')
                        ->modalSubmitActionLabel('Ya, Hapus Semua'),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Jenis Komoditas')
            ->emptyStateDescription('Mulai dengan menambahkan jenis komoditas pertanian pertama Anda.')
            ->emptyStateIcon('heroicon-o-sparkles')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Jenis Komoditas')
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
                Infolists\Components\Section::make('Informasi Komoditas')
                    ->icon('heroicon-o-sparkles')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('nama_komoditas')
                                    ->label('Nama Komoditas')
                                    ->icon('heroicon-o-sparkles')
                                    ->iconColor('success')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->copyable()
                                    ->color('success'),

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

                        Infolists\Components\TextEntry::make('deskripsi')
                            ->label('Deskripsi')
                            ->html()
                            ->default('Belum ada deskripsi')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Statistik Komoditas')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('petani_count')
                                    ->label('Jumlah Petani')
                                    ->state(function (JenisKomoditas $record): int {
                                        return DB::table('kelompok_tani_anggotas')
                                            ->where('jenis_komoditas_id', $record->id)
                                            ->count();
                                    })
                                    ->badge()
                                    ->icon('heroicon-o-user')
                                    ->color('success')
                                    ->suffix(' Petani')
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('kelompok_count')
                                    ->label('Jumlah Kelompok Tani')
                                    ->state(function (JenisKomoditas $record): int {
                                        return DB::table('kelompok_tani_anggotas')
                                            ->where('jenis_komoditas_id', $record->id)
                                            ->distinct('kelompok_tani_id')
                                            ->count('kelompok_tani_id');
                                    })
                                    ->badge()
                                    ->icon('heroicon-o-user-group')
                                    ->color('info')
                                    ->suffix(' Kelompok')
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('total_lahan')
                                    ->label('Total Luas Lahan')
                                    ->state(function (JenisKomoditas $record): float {
                                        return DB::table('kelompok_tani_anggotas')
                                            ->where('jenis_komoditas_id', $record->id)
                                            ->sum('luas_lahan');
                                    })
                                    ->numeric(decimalPlaces: 2)
                                    ->badge()
                                    ->icon('heroicon-o-map')
                                    ->color('warning')
                                    ->suffix(' Hektar')
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
            'index' => Pages\ListJenisKomoditas::route('/'),
            'create' => Pages\CreateJenisKomoditas::route('/create'),
            'view' => Pages\ViewJenisKomoditas::route('/{record}'),
            'edit' => Pages\EditJenisKomoditas::route('/{record}/edit'),
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
        $petaniCount = DB::table('kelompok_tani_anggotas')
            ->where('jenis_komoditas_id', $record->id)
            ->count();

        return [
            'Petani' => "{$petaniCount} petani",
            'Status' => $record->is_active ? 'Aktif' : 'Nonaktif',
        ];
    }
}
