<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Artikel;
use Filament\Infolists;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ArtikelResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ArtikelResource\RelationManagers;

class ArtikelResource extends Resource
{
    protected static ?string $model = Artikel::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationLabel = 'Artikel';

    protected static ?string $modelLabel = 'Artikel';

    protected static ?string $pluralModelLabel = 'Data Artikel';

    protected static ?string $navigationGroup = 'Blog & Konten';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'judul';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Artikel')
                    ->description('Data utama artikel blog')
                    ->icon('heroicon-o-newspaper')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('kategori_id')
                                    ->relationship('kategori', 'nama_kategori')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Kategori Artikel')
                                    ->placeholder('Pilih Kategori')
                                    ->helperText('Kategori untuk mengelompokkan artikel')
                                    ->prefixIcon('heroicon-o-folder')
                                    ->native(false)
                                    ->live()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('nama_kategori')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('slug')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(),
                                        Forms\Components\Toggle::make('is_active')
                                            ->default(true),
                                    ])
                                    ->createOptionModalHeading('Tambah Kategori Baru')
                                    ->columnSpan(1),

                                Forms\Components\Select::make('author_id')
                                    ->relationship('author', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Penulis')
                                    ->placeholder('Pilih Penulis')
                                    ->helperText('Penulis artikel')
                                    ->prefixIcon('heroicon-o-user')
                                    ->native(false)
                                    ->default(Auth::id())
                                    ->columnSpan(1),

                                Forms\Components\Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'published' => 'Published',
                                    ])
                                    ->required()
                                    ->default('draft')
                                    ->label('Status')
                                    ->helperText('Status publikasi artikel')
                                    ->prefixIcon('heroicon-o-document-check')
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        // Auto-set published_at saat status jadi published
                                        if ($state === 'published' && !$get('published_at')) {
                                            $set('published_at', now());
                                        }
                                    })
                                    ->columnSpan(1),
                            ]),

                        Forms\Components\TextInput::make('judul')
                            ->required()
                            ->maxLength(255)
                            ->label('Judul Artikel')
                            ->placeholder('Masukkan judul artikel yang menarik...')
                            ->helperText('Judul artikel maksimal 255 karakter')
                            ->prefixIcon('heroicon-o-pencil')
                            ->autocomplete(false)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                // Auto-generate slug dan ringkasan
                                if ($state && !$get('slug')) {
                                    $set('slug', Str::slug($state));
                                }
                                if ($state && !$get('ringkasan')) {
                                    $set('ringkasan', Str::limit($state, 100));
                                }
                            })
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->label('Slug URL')
                                    ->placeholder('judul-artikel-anda')
                                    ->helperText('URL-friendly identifier (otomatis dibuat)')
                                    ->prefixIcon('heroicon-o-link')
                                    ->prefix(url('/artikel/'))
                                    ->alphaDash()
                                    ->rules(['regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'])
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $set('slug', Str::slug($state));
                                    }),

                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Tanggal & Waktu Publish')
                                    ->native(false)
                                    ->displayFormat('d F Y, H:i')
                                    ->helperText('Kosongkan untuk publish sekarang')
                                    ->prefixIcon('heroicon-o-calendar')
                                    ->default(fn (Forms\Get $get) =>
                                        $get('status') === 'published' ? now() : null
                                    )
                                    ->visible(fn (Forms\Get $get) => $get('status') === 'published'),
                            ]),
                    ]),

                Forms\Components\Section::make('Konten Artikel')
                    ->description('Isi dan ringkasan artikel')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Textarea::make('ringkasan')
                            ->required()
                            ->label('Ringkasan/Excerpt')
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('Ringkasan singkat artikel (maks 500 karakter)...')
                            ->helperText('Ringkasan yang menarik untuk pratinjau artikel')
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('konten')
                            ->required()
                            ->label('Konten Artikel')
                            ->columnSpanFull()
                            ->placeholder('Tulis konten artikel lengkap di sini...')
                            ->helperText('Konten lengkap artikel dengan formatting')
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
                            ->fileAttachmentsDirectory('artikel-attachments'),
                    ]),

                Forms\Components\Section::make('Media & Thumbnail')
                    ->description('Upload gambar thumbnail artikel')
                    ->icon('heroicon-o-photo')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\FileUpload::make('thumbnail')
                            ->image()
                            ->label('Thumbnail Artikel')
                            ->directory('artikel-thumbnails')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->imagePreviewHeight('250')
                            ->helperText('Upload gambar thumbnail (Rekomendasi: 1200x630px, max 2MB)')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Preview URL')
                    ->description('Pratinjau link artikel')
                    ->icon('heroicon-o-globe-alt')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('url_preview')
                            ->label('URL Artikel')
                            ->content(function (Forms\Get $get, ?Artikel $record): string {
                                $slug = $get('slug') ?: ($record?->slug ?? 'slug-belum-diisi');
                                return url('/artikel/' . $slug);
                            })
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($operation) => $operation === 'edit' || $operation === 'create'),

                Forms\Components\Section::make('Informasi Tambahan')
                    ->description('Statistik dan metadata')
                    ->icon('heroicon-o-information-circle')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('word_count')
                            ->label('Jumlah Kata')
                            ->content(function (Forms\Get $get): string {
                                $konten = $get('konten') ?? '';
                                $wordCount = str_word_count(strip_tags($konten));
                                return number_format($wordCount) . ' kata (~' . ceil($wordCount / 200) . ' menit baca)';
                            }),

                        Forms\Components\Placeholder::make('char_count')
                            ->label('Jumlah Karakter')
                            ->content(function (Forms\Get $get): string {
                                $konten = $get('konten') ?? '';
                                $charCount = strlen(strip_tags($konten));
                                return number_format($charCount) . ' karakter';
                            }),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Dibuat pada')
                            ->content(fn (?Artikel $record): string =>
                                $record?->created_at?->format('d F Y, H:i') . ' (' . $record?->created_at?->diffForHumans() . ')' ?? '-'
                            )
                            ->visible(fn (?Artikel $record) => $record !== null),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->content(fn (?Artikel $record): string =>
                                $record?->updated_at?->diffForHumans() ?? '-'
                            )
                            ->visible(fn (?Artikel $record) => $record !== null),
                    ])
                    ->columns(2)
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

                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-thumbnail.png'))
                    ->size(50),

                Tables\Columns\TextColumn::make('judul')
                    ->searchable()
                    ->label('Judul Artikel')
                    ->icon('heroicon-o-newspaper')
                    ->iconColor('primary')
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Judul tersalin!')
                    ->description(fn (Artikel $record): string =>
                        Str::limit(strip_tags($record->ringkasan), 60)
                    )
                    ->wrap()
                    ->limit(50)
                    ->tooltip(fn (Artikel $record): string => $record->judul),

                Tables\Columns\TextColumn::make('kategori.nama_kategori')
                    ->searchable()
                    ->sortable()
                    ->label('Kategori')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-folder')
                    ->tooltip('Kategori artikel'),

                Tables\Columns\TextColumn::make('author.name')
                    ->searchable()
                    ->sortable()
                    ->label('Penulis')
                    ->icon('heroicon-o-user')
                    ->iconColor('success')
                    ->weight('medium')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'published' => 'heroicon-o-check-circle',
                        'draft' => 'heroicon-o-pencil-square',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'published' => 'Published',
                        'draft' => 'Draft',
                    })
                    ->label('Status')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->label('Tanggal Publish')
                    ->icon('heroicon-o-calendar')
                    ->tooltip(fn ($state): ?string =>
                        $state ? \Carbon\Carbon::parse($state)->translatedFormat('l, d F Y \p\u\k\u\l H:i') : null
                    )
                    ->since()
                    ->placeholder('Belum dipublikasi')
                    ->color(fn ($state) => $state ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('word_count')
                    ->label('Kata')
                    ->state(function (Artikel $record): int {
                        return str_word_count(strip_tags($record->konten));
                    })
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-document-text')
                    ->suffix(' kata')
                    ->alignCenter()
                    ->tooltip(fn ($state): string => number_format($state) . ' kata (~' . ceil($state / 200) . ' menit baca)')
                    ->toggleable(),

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
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama_kategori')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('Semua Kategori')
                    ->native(false)
                    ->indicator('Kategori'),

                Tables\Filters\SelectFilter::make('author_id')
                    ->label('Penulis')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('Semua Penulis')
                    ->native(false)
                    ->indicator('Penulis'),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ])
                    ->label('Status')
                    ->multiple()
                    ->native(false)
                    ->indicator('Status'),

                Tables\Filters\Filter::make('published_at')
                    ->form([
                        Forms\Components\DatePicker::make('published_from')
                            ->label('Dari Tanggal')
                            ->native(false)
                            ->placeholder('Pilih tanggal mulai'),
                        Forms\Components\DatePicker::make('published_until')
                            ->label('Sampai Tanggal')
                            ->native(false)
                            ->placeholder('Pilih tanggal akhir'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['published_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '>=', $date),
                            )
                            ->when(
                                $data['published_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['published_from'] ?? null) {
                            $indicators[] = 'Dari: ' . \Carbon\Carbon::parse($data['published_from'])->format('d M Y');
                        }
                        if ($data['published_until'] ?? null) {
                            $indicators[] = 'Sampai: ' . \Carbon\Carbon::parse($data['published_until'])->format('d M Y');
                        }
                        return $indicators;
                    }),

                Tables\Filters\Filter::make('bulan_ini')
                    ->label('Bulan Ini')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereMonth('published_at', now()->month)
                            ->whereYear('published_at', now()->year)
                    )
                    ->toggle(),

                Tables\Filters\TernaryFilter::make('has_thumbnail')
                    ->label('Thumbnail')
                    ->placeholder('Semua')
                    ->trueLabel('Dengan Thumbnail')
                    ->falseLabel('Tanpa Thumbnail')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('thumbnail'),
                        false: fn (Builder $query) => $query->whereNull('thumbnail'),
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
                    Tables\Actions\Action::make('publish')
                        ->label('Publish')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Artikel $record) {
                            $record->update([
                                'status' => 'published',
                                'published_at' => now(),
                            ]);
                            \Filament\Notifications\Notification::make()
                                ->title('Artikel berhasil dipublikasikan')
                                ->success()
                                ->send();
                        })
                        ->visible(fn (Artikel $record): bool => $record->status === 'draft'),
                    Tables\Actions\Action::make('unpublish')
                        ->label('Jadikan Draft')
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Artikel $record) {
                            $record->update(['status' => 'draft']);
                            \Filament\Notifications\Notification::make()
                                ->title('Artikel dikembalikan ke draft')
                                ->success()
                                ->send();
                        })
                        ->visible(fn (Artikel $record): bool => $record->status === 'published'),
                    Tables\Actions\Action::make('view_website')
                        ->label('Lihat di Website')
                        ->icon('heroicon-o-globe-alt')
                        ->color('success')
                        ->url(fn (Artikel $record): string => url('/artikel/' . $record->slug))
                        ->openUrlInNewTab()
                        ->visible(fn (Artikel $record): bool => $record->status === 'published'),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Artikel')
                        ->modalDescription('Apakah Anda yakin ingin menghapus artikel ini?')
                        ->modalSubmitActionLabel('Ya, Hapus'),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Aksi')
                ->button()
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publish Terpilih')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each->update([
                                'status' => 'published',
                                'published_at' => now(),
                            ]);
                            \Filament\Notifications\Notification::make()
                                ->title('Artikel berhasil dipublikasikan')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('draft')
                        ->label('Jadikan Draft')
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each->update(['status' => 'draft']);
                            \Filament\Notifications\Notification::make()
                                ->title('Artikel dikembalikan ke draft')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Artikel Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus semua artikel yang dipilih?')
                        ->modalSubmitActionLabel('Ya, Hapus Semua'),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Artikel')
            ->emptyStateDescription('Mulai dengan menulis artikel pertama Anda.')
            ->emptyStateIcon('heroicon-o-newspaper')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tulis Artikel Baru')
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
                Infolists\Components\Section::make('Informasi Artikel')
                    ->icon('heroicon-o-newspaper')
                    ->schema([
                        Infolists\Components\ImageEntry::make('thumbnail')
                            ->label('Thumbnail')
                            ->defaultImageUrl(url('/images/default-thumbnail.png'))
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('judul')
                            ->label('Judul')
                            ->size('lg')
                            ->weight('bold')
                            ->copyable()
                            ->columnSpanFull(),

                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('kategori.nama_kategori')
                                    ->label('Kategori')
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-folder'),

                                Infolists\Components\TextEntry::make('author.name')
                                    ->label('Penulis')
                                    ->icon('heroicon-o-user')
                                    ->badge()
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'published' => 'success',
                                        'draft' => 'warning',
                                    })
                                    ->icon(fn (string $state): string => match ($state) {
                                        'published' => 'heroicon-o-check-circle',
                                        'draft' => 'heroicon-o-pencil-square',
                                    })
                                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                            ]),

                        Infolists\Components\TextEntry::make('ringkasan')
                            ->label('Ringkasan')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('konten')
                            ->label('Konten')
                            ->html()
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('slug')
                            ->label('Slug URL')
                            ->icon('heroicon-o-link')
                            ->copyable()
                            ->prefix('/artikel/')
                            ->url(fn (Artikel $record): string => url('/artikel/' . $record->slug))
                            ->openUrlInNewTab()
                            ->color('success'),

                        Infolists\Components\TextEntry::make('published_at')
                            ->label('Tanggal Publish')
                            ->dateTime('d F Y, H:i')
                            ->icon('heroicon-o-calendar')
                            ->placeholder('Belum dipublikasi'),

                        Infolists\Components\TextEntry::make('word_count')
                            ->label('Statistik Konten')
                            ->state(function (Artikel $record): string {
                                $words = str_word_count(strip_tags($record->konten));
                                $minutes = ceil($words / 200);
                                return number_format($words) . " kata (~{$minutes} menit baca)";
                            })
                            ->icon('heroicon-o-document-text'),
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
            'index' => Pages\ListArtikels::route('/'),
            'create' => Pages\CreateArtikel::route('/create'),
            'view' => Pages\ViewArtikel::route('/{record}'),
            'edit' => Pages\EditArtikel::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $published = static::getModel()::where('status', 'published')->count();
        $draft = static::getModel()::where('status', 'draft')->count();
        return "{$published}/{$draft}";
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $draft = static::getModel()::where('status', 'draft')->count();
        return $draft > 0 ? 'warning' : 'success';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        $published = static::getModel()::where('status', 'published')->count();
        $draft = static::getModel()::where('status', 'draft')->count();
        return "{$published} Published, {$draft} Draft";
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Kategori' => $record->kategori->nama_kategori,
            'Penulis' => $record->author->name,
            'Status' => ucfirst($record->status),
        ];
    }
}
