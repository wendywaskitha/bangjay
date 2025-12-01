<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengumumanResource\Pages;
use App\Filament\Resources\PengumumanResource\RelationManagers;
use App\Models\Pengumuman;
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

class PengumumanResource extends Resource
{
    protected static ?string $model = Pengumuman::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'Pengumuman';

    protected static ?string $modelLabel = 'Pengumuman';

    protected static ?string $pluralModelLabel = 'Data Pengumuman';

    protected static ?string $navigationGroup = 'Blog & Konten';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'judul';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pengumuman')
                    ->description('Data utama pengumuman')
                    ->icon('heroicon-o-megaphone')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('judul')
                            ->required()
                            ->maxLength(255)
                            ->label('Judul Pengumuman')
                            ->placeholder('Masukkan judul pengumuman...')
                            ->helperText('Judul pengumuman yang menarik perhatian')
                            ->prefixIcon('heroicon-o-bell')
                            ->autocomplete(false)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                // Auto-generate slug
                                if ($state && !$get('slug')) {
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->label('Slug URL')
                            ->placeholder('judul-pengumuman')
                            ->helperText('URL-friendly identifier (otomatis dibuat dari judul)')
                            ->prefixIcon('heroicon-o-link')
                            ->prefix(url('/pengumuman/'))
                            ->alphaDash()
                            ->rules(['regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'])
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $set('slug', Str::slug($state));
                            })
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true)
                            ->label('Status Aktif')
                            ->helperText('Aktifkan untuk menampilkan pengumuman')
                            ->inline(false)
                            ->onColor('success')
                            ->offColor('gray')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Periode Tayang')
                    ->description('Atur waktu tayang pengumuman')
                    ->icon('heroicon-o-calendar')
                    ->collapsible()
                    ->schema([
                        Forms\Components\DatePicker::make('mulai_tayang')
                            ->required()
                            ->label('Mulai Tayang')
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->default(now())
                            ->helperText('Tanggal mulai pengumuman ditampilkan')
                            ->prefixIcon('heroicon-o-calendar-days')
                            ->closeOnDateSelection()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                // Auto-set selesai tayang 30 hari dari mulai jika kosong
                                if ($state && !$get('selesai_tayang')) {
                                    $set('selesai_tayang', Carbon::parse($state)->addDays(30));
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('selesai_tayang')
                            ->label('Selesai Tayang')
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->helperText('Kosongkan untuk tayang tanpa batas waktu')
                            ->prefixIcon('heroicon-o-calendar')
                            ->closeOnDateSelection()
                            ->minDate(fn (Forms\Get $get) => $get('mulai_tayang'))
                            ->columnSpan(1),

                        Forms\Components\Placeholder::make('durasi_tayang')
                            ->label('Durasi Tayang')
                            ->content(function (Forms\Get $get): string {
                                $mulai = $get('mulai_tayang');
                                $selesai = $get('selesai_tayang');

                                if (!$mulai) return '-';
                                if (!$selesai) return '📅 Tayang tanpa batas waktu';

                                $diff = Carbon::parse($mulai)->diffInDays(Carbon::parse($selesai));
                                return "📅 {$diff} hari tayang";
                            })
                            ->columnSpan(2)
                            ->visible(fn (?Pengumuman $record) => $record === null),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Isi Pengumuman')
                    ->description('Konten lengkap pengumuman')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->schema([
                        Forms\Components\RichEditor::make('isi')
                            ->required()
                            ->label('Isi Pengumuman')
                            ->columnSpanFull()
                            ->placeholder('Tulis isi pengumuman lengkap di sini...')
                            ->helperText('Konten pengumuman dengan formatting')
                            ->toolbarButtons([
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
                            ->maxLength(5000),
                    ]),

                Forms\Components\Section::make('Preview URL')
                    ->description('Pratinjau link pengumuman')
                    ->icon('heroicon-o-globe-alt')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('url_preview')
                            ->label('URL Pengumuman')
                            ->content(function (Forms\Get $get, ?Pengumuman $record): string {
                                $slug = $get('slug') ?: ($record?->slug ?? 'slug-belum-diisi');
                                return url('/pengumuman/' . $slug);
                            })
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($operation) => $operation === 'edit' || $operation === 'create'),

                Forms\Components\Section::make('Informasi Tambahan')
                    ->description('Status dan statistik')
                    ->icon('heroicon-o-information-circle')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('status_tayang')
                            ->label('Status Tayang Saat Ini')
                            ->content(fn (?Pengumuman $record): string =>
                                $record ? self::getStatusTayang($record) : 'Status akan tampil setelah disimpan'
                            ),

                        Forms\Components\Placeholder::make('sisa_waktu')
                            ->label('Sisa Waktu Tayang')
                            ->content(function (?Pengumuman $record): string {
                                if (!$record || !$record->selesai_tayang) return '-';

                                $now = now();
                                $selesai = Carbon::parse($record->selesai_tayang);

                                if ($now->gt($selesai)) {
                                    return '⏰ Sudah berakhir';
                                }

                                $diff = $now->diffInDays($selesai);
                                return "⏰ {$diff} hari lagi";
                            })
                            ->visible(fn (?Pengumuman $record) => $record !== null),

                        Forms\Components\Placeholder::make('word_count')
                            ->label('Jumlah Kata')
                            ->content(function (Forms\Get $get): string {
                                $isi = $get('isi') ?? '';
                                $wordCount = str_word_count(strip_tags($isi));
                                return number_format($wordCount) . ' kata';
                            }),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Dibuat pada')
                            ->content(fn (?Pengumuman $record): string =>
                                $record?->created_at?->format('d F Y, H:i') . ' (' . $record?->created_at?->diffForHumans() . ')' ?? '-'
                            )
                            ->visible(fn (?Pengumuman $record) => $record !== null),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->content(fn (?Pengumuman $record): string =>
                                $record?->updated_at?->diffForHumans() ?? '-'
                            )
                            ->visible(fn (?Pengumuman $record) => $record !== null),
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

                Tables\Columns\TextColumn::make('judul')
                    ->searchable()
                    ->label('Judul Pengumuman')
                    ->icon('heroicon-o-megaphone')
                    ->iconColor('primary')
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Judul tersalin!')
                    ->description(fn (Pengumuman $record): string =>
                        Str::limit(strip_tags($record->isi), 60)
                    )
                    ->wrap()
                    ->limit(50)
                    ->tooltip(fn (Pengumuman $record): string => $record->judul),

                Tables\Columns\TextColumn::make('status_periode')
                    ->label('Status Tayang')
                    ->badge()
                    ->state(fn (Pengumuman $record): string =>
                        self::getStatusTayangLabel($record)
                    )
                    ->color(fn (Pengumuman $record): string =>
                        self::getStatusTayangColor($record)
                    )
                    ->icon(fn (Pengumuman $record): string =>
                        self::getStatusTayangIcon($record)
                    )
                    ->alignCenter()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('mulai_tayang', $direction);
                    })
                    ->tooltip(fn (Pengumuman $record): string =>
                        self::getStatusTayang($record)
                    ),

                Tables\Columns\TextColumn::make('mulai_tayang')
                    ->date('d M Y')
                    ->sortable()
                    ->label('Mulai Tayang')
                    ->icon('heroicon-o-calendar-days')
                    ->iconColor('success')
                    ->tooltip(fn ($state): string =>
                        Carbon::parse($state)->translatedFormat('l, d F Y')
                    ),

                Tables\Columns\TextColumn::make('selesai_tayang')
                    ->date('d M Y')
                    ->sortable()
                    ->label('Selesai Tayang')
                    ->icon('heroicon-o-calendar')
                    ->iconColor('warning')
                    ->placeholder('Tanpa Batas')
                    ->tooltip(fn ($state): ?string =>
                        $state ? Carbon::parse($state)->translatedFormat('l, d F Y') : 'Tayang tanpa batas waktu'
                    ),

                Tables\Columns\TextColumn::make('durasi')
                    ->label('Durasi')
                    ->state(function (Pengumuman $record): string {
                        $mulai = Carbon::parse($record->mulai_tayang);
                        $selesai = $record->selesai_tayang ? Carbon::parse($record->selesai_tayang) : null;

                        if (!$selesai) return '∞ Tanpa Batas';

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
                            ->body($state ? 'Pengumuman diaktifkan' : 'Pengumuman dinonaktifkan')
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
            ->defaultSort('mulai_tayang', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif')
                    ->native(false),

                Tables\Filters\Filter::make('status_tayang')
                    ->label('Status Periode')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Status Tayang')
                            ->options([
                                'belum_tayang' => 'Belum Tayang',
                                'sedang_tayang' => 'Sedang Tayang',
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
                                    'belum_tayang' => $query->where('mulai_tayang', '>', $now),
                                    'sedang_tayang' => $query->where('mulai_tayang', '<=', $now)
                                        ->where(function($q) use ($now) {
                                            $q->whereNull('selesai_tayang')
                                              ->orWhere('selesai_tayang', '>=', $now);
                                        }),
                                    'sudah_berakhir' => $query->whereNotNull('selesai_tayang')
                                        ->where('selesai_tayang', '<', $now),
                                    default => $query,
                                };
                            }
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['status'] ?? null) {
                            return match($data['status']) {
                                'belum_tayang' => 'Belum Tayang',
                                'sedang_tayang' => 'Sedang Tayang',
                                'sudah_berakhir' => 'Sudah Berakhir',
                            };
                        }
                        return null;
                    }),

                Tables\Filters\Filter::make('mulai_tayang')
                    ->form([
                        Forms\Components\DatePicker::make('mulai_tayang_from')
                            ->label('Mulai Tayang Dari')
                            ->native(false),
                        Forms\Components\DatePicker::make('mulai_tayang_until')
                            ->label('Mulai Tayang Sampai')
                            ->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['mulai_tayang_from'],
                                fn ($query, $date) => $query->whereDate('mulai_tayang', '>=', $date)
                            )
                            ->when(
                                $data['mulai_tayang_until'],
                                fn ($query, $date) => $query->whereDate('mulai_tayang', '<=', $date)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['mulai_tayang_from'] ?? null) {
                            $indicators[] = 'Mulai dari: ' . Carbon::parse($data['mulai_tayang_from'])->format('d M Y');
                        }
                        if ($data['mulai_tayang_until'] ?? null) {
                            $indicators[] = 'Mulai sampai: ' . Carbon::parse($data['mulai_tayang_until'])->format('d M Y');
                        }
                        return $indicators;
                    }),

                Tables\Filters\Filter::make('selesai_tayang')
                    ->form([
                        Forms\Components\DatePicker::make('selesai_tayang_from')
                            ->label('Selesai Tayang Dari')
                            ->native(false),
                        Forms\Components\DatePicker::make('selesai_tayang_until')
                            ->label('Selesai Tayang Sampai')
                            ->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['selesai_tayang_from'],
                                fn ($query, $date) => $query->whereDate('selesai_tayang', '>=', $date)
                            )
                            ->when(
                                $data['selesai_tayang_until'],
                                fn ($query, $date) => $query->whereDate('selesai_tayang', '<=', $date)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['selesai_tayang_from'] ?? null) {
                            $indicators[] = 'Selesai dari: ' . Carbon::parse($data['selesai_tayang_from'])->format('d M Y');
                        }
                        if ($data['selesai_tayang_until'] ?? null) {
                            $indicators[] = 'Selesai sampai: ' . Carbon::parse($data['selesai_tayang_until'])->format('d M Y');
                        }
                        return $indicators;
                    }),

                Tables\Filters\Filter::make('aktif_sekarang')
                    ->label('Aktif Sekarang')
                    ->query(fn (Builder $query): Builder =>
                        $query->where('is_active', true)
                            ->where('mulai_tayang', '<=', now())
                            ->where(function($q) {
                                $q->whereNull('selesai_tayang')
                                  ->orWhere('selesai_tayang', '>=', now());
                            })
                    )
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
                        ->label(fn (Pengumuman $record): string =>
                            $record->is_active ? 'Nonaktifkan' : 'Aktifkan'
                        )
                        ->icon(fn (Pengumuman $record): string =>
                            $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle'
                        )
                        ->color(fn (Pengumuman $record): string =>
                            $record->is_active ? 'gray' : 'success'
                        )
                        ->requiresConfirmation()
                        ->action(function (Pengumuman $record) {
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
                        ->url(fn (Pengumuman $record): string => url('/pengumuman/' . $record->slug))
                        ->openUrlInNewTab(),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Pengumuman')
                        ->modalDescription('Apakah Anda yakin ingin menghapus pengumuman ini?')
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
                                ->title('Pengumuman berhasil diaktifkan')
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
                                ->title('Pengumuman berhasil dinonaktifkan')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Pengumuman Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus semua pengumuman yang dipilih?')
                        ->modalSubmitActionLabel('Ya, Hapus Semua'),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Pengumuman')
            ->emptyStateDescription('Mulai dengan membuat pengumuman pertama Anda.')
            ->emptyStateIcon('heroicon-o-megaphone')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Buat Pengumuman Baru')
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
                Infolists\Components\Section::make('Informasi Pengumuman')
                    ->icon('heroicon-o-megaphone')
                    ->schema([
                        Infolists\Components\TextEntry::make('judul')
                            ->label('Judul')
                            ->size('lg')
                            ->weight('bold')
                            ->copyable()
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
                                    ->label('Status Tayang')
                                    ->state(fn (Pengumuman $record): string =>
                                        self::getStatusTayangLabel($record)
                                    )
                                    ->badge()
                                    ->color(fn (Pengumuman $record): string =>
                                        self::getStatusTayangColor($record)
                                    ),

                                Infolists\Components\TextEntry::make('slug')
                                    ->label('Slug URL')
                                    ->icon('heroicon-o-link')
                                    ->copyable()
                                    ->prefix('/pengumuman/'),
                            ]),

                        Infolists\Components\TextEntry::make('isi')
                            ->label('Isi Pengumuman')
                            ->html()
                            ->columnSpanFull(),

                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('mulai_tayang')
                                    ->label('Mulai Tayang')
                                    ->date('d F Y')
                                    ->icon('heroicon-o-calendar-days'),

                                Infolists\Components\TextEntry::make('selesai_tayang')
                                    ->label('Selesai Tayang')
                                    ->date('d F Y')
                                    ->icon('heroicon-o-calendar')
                                    ->placeholder('Tanpa Batas Waktu'),
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
            'index' => Pages\ListPengumumen::route('/'),
            'create' => Pages\CreatePengumuman::route('/create'),
            'view' => Pages\ViewPengumuman::route('/{record}'),
            'edit' => Pages\EditPengumuman::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $aktif = static::getModel()::where('is_active', true)
            ->where('mulai_tayang', '<=', now())
            ->where(function($q) {
                $q->whereNull('selesai_tayang')
                  ->orWhere('selesai_tayang', '>=', now());
            })
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
            ->where('mulai_tayang', '<=', now())
            ->where(function($q) {
                $q->whereNull('selesai_tayang')
                  ->orWhere('selesai_tayang', '>=', now());
            })
            ->count();

        return "{$aktif} pengumuman aktif sekarang";
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Status' => self::getStatusTayangLabel($record),
            'Mulai' => Carbon::parse($record->mulai_tayang)->format('d M Y'),
        ];
    }

    // Helper methods
    protected static function getStatusTayang(Pengumuman $record): string
    {
        $now = now();
        $mulai = Carbon::parse($record->mulai_tayang);
        $selesai = $record->selesai_tayang ? Carbon::parse($record->selesai_tayang) : null;

        if ($now->lt($mulai)) {
            $diff = $now->diffInDays($mulai);
            return "Akan tayang dalam {$diff} hari";
        }

        if (!$selesai) {
            return "Sedang tayang (tanpa batas waktu)";
        }

        if ($now->between($mulai, $selesai)) {
            $diff = $now->diffInDays($selesai);
            return "Sedang tayang ({$diff} hari lagi)";
        }

        $diff = $now->diffInDays($selesai);
        return "Berakhir {$diff} hari yang lalu";
    }

    protected static function getStatusTayangLabel(Pengumuman $record): string
    {
        $now = now();
        $mulai = Carbon::parse($record->mulai_tayang);
        $selesai = $record->selesai_tayang ? Carbon::parse($record->selesai_tayang) : null;

        if ($now->lt($mulai)) {
            return 'Belum Tayang';
        }

        if (!$selesai || $now->between($mulai, $selesai)) {
            return 'Sedang Tayang';
        }

        return 'Sudah Berakhir';
    }

    protected static function getStatusTayangColor(Pengumuman $record): string
    {
        $now = now();
        $mulai = Carbon::parse($record->mulai_tayang);
        $selesai = $record->selesai_tayang ? Carbon::parse($record->selesai_tayang) : null;

        if ($now->lt($mulai)) {
            return 'warning';
        }

        if (!$selesai || $now->between($mulai, $selesai)) {
            return 'success';
        }

        return 'gray';
    }

    protected static function getStatusTayangIcon(Pengumuman $record): string
    {
        $now = now();
        $mulai = Carbon::parse($record->mulai_tayang);
        $selesai = $record->selesai_tayang ? Carbon::parse($record->selesai_tayang) : null;

        if ($now->lt($mulai)) {
            return 'heroicon-o-clock';
        }

        if (!$selesai || $now->between($mulai, $selesai)) {
            return 'heroicon-o-signal';
        }

        return 'heroicon-o-x-circle';
    }
}
