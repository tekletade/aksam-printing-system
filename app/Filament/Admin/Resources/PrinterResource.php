<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PrinterResource\Pages;
use App\Models\Printer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PrinterResource extends Resource
{
    protected static ?string $model = Printer::class;

    protected static ?string $navigationIcon = 'heroicon-o-printer';

    protected static ?string $navigationGroup = 'Machine Management';

    protected static ?string $navigationLabel = 'Printers';

    protected static ?string $modelLabel = 'Printer';

    protected static ?string $pluralModelLabel = 'Printers';

    protected static ?int $navigationSort = 1;
    // Permission Methods
    public static function canViewAny(): bool
    {
        return auth()->user()->can('view printers');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create printers');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('edit printers');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('delete printers');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('delete printers');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Printer Information')
                    ->tabs([
                        Tab::make('Basic Information')
                            ->schema([
                                Section::make('Printer Details')
                                    ->schema([
                                        Forms\Components\Select::make('printer_model_id')
                                            ->label('Printer Model')
                                            ->relationship('printerModel', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\Select::make('brand')
                                                    ->options([
                                                        'Konica' => 'Konica',
                                                        'Canon' => 'Canon',
                                                        'KIP' => 'KIP',
                                                        'HP' => 'HP',
                                                    ])
                                                    ->required(),
                                                Forms\Components\TextInput::make('model_number')
                                                    ->required()
                                                    ->maxLength(100),
                                                Forms\Components\Toggle::make('is_color')
                                                    ->label('Color Printer')
                                                    ->default(true),
                                                Forms\Components\Toggle::make('is_duplex_supported')
                                                    ->label('Duplex Supported')
                                                    ->default(true),
                                            ])
                                            ->columns(2),

                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true),

                                        Forms\Components\TextInput::make('ip_address')
                                            ->required()
                                            ->ip()
                                            ->unique(ignoreRecord: true),

                                        Forms\Components\TextInput::make('mac_address')
                                            ->label('MAC Address')
                                            ->maxLength(17)
                                            ->placeholder('00:1A:2B:3C:4D:5E'),

                                        Forms\Components\TextInput::make('serial_number')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(100),

                                        Forms\Components\Select::make('status')
                                            ->options([
                                                'Ready' => 'Ready',
                                                'Printing' => 'Printing',
                                                'Error' => 'Error',
                                                'Offline' => 'Offline',
                                                'Maintenance' => 'Maintenance',
                                                'Paper Jam' => 'Paper Jam',
                                                'Toner Low' => 'Toner Low',
                                            ])
                                            ->required()
                                            ->default('Ready'),

                                        Forms\Components\TextInput::make('location')
                                            ->maxLength(255),

                                        Forms\Components\Select::make('department')
                                            ->options([
                                                'Production' => 'Production',
                                                'Design' => 'Design',
                                                'Sales' => 'Sales',
                                                'Administration' => 'Administration',
                                            ])
                                            ->searchable(),
                                    ])->columns(2),
                            ]),

                        Tab::make('Counters & Usage')
                            ->schema([
                                Section::make('Page Counts')
                                    ->schema([
                                        Forms\Components\TextInput::make('total_pages_count')
                                            ->label('Total Pages')
                                            ->numeric()
                                            ->default(0),

                                        Forms\Components\TextInput::make('black_white_pages')
                                            ->label('B&W Pages')
                                            ->numeric()
                                            ->default(0),

                                        Forms\Components\TextInput::make('color_pages')
                                            ->label('Color Pages')
                                            ->numeric()
                                            ->default(0),

                                        Forms\Components\TextInput::make('simplex_pages')
                                            ->label('Simplex Pages')
                                            ->numeric()
                                            ->default(0),

                                        Forms\Components\TextInput::make('duplex_pages')
                                            ->label('Duplex Pages')
                                            ->numeric()
                                            ->default(0),

                                        Forms\Components\TextInput::make('total_print_length_meters')
                                            ->label('Total Print Length (m)')
                                            ->numeric()
                                            ->default(0)
                                            ->step(0.01),
                                    ])->columns(3),
                            ]),

                        Tab::make('Maintenance')
                            ->schema([
                                Section::make('Maintenance Schedule')
                                    ->schema([
                                        Forms\Components\DatePicker::make('last_maintenance_date')
                                            ->label('Last Maintenance'),

                                        Forms\Components\DatePicker::make('next_maintenance_date')
                                            ->label('Next Maintenance'),

                                        Forms\Components\TextInput::make('maintenance_interval_days')
                                            ->label('Interval (days)')
                                            ->numeric()
                                            ->default(90),
                                    ])->columns(2),
                            ]),

                        Tab::make('SNMP Settings')
                            ->schema([
                                Section::make('SNMP Configuration')
                                    ->schema([
                                        Forms\Components\TextInput::make('snmp_community')
                                            ->label('SNMP Community')
                                            ->default('public')
                                            ->required(),

                                        Forms\Components\TextInput::make('snmp_port')
                                            ->label('SNMP Port')
                                            ->numeric()
                                            ->default(161),

                                        Forms\Components\Select::make('snmp_version')
                                            ->label('SNMP Version')
                                            ->options([
                                                'v1' => 'v1',
                                                'v2c' => 'v2c',
                                                'v3' => 'v3',
                                            ])
                                            ->default('v2c'),
                                    ])->columns(2),
                            ]),

                        Tab::make('Settings')
                            ->schema([
                                Section::make('Configuration')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_monitoring_enabled')
                                            ->label('Enable Monitoring')
                                            ->default(true),

                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Active')
                                            ->default(true),

                                        Forms\Components\DateTimePicker::make('last_polled_at')
                                            ->label('Last Polled'),
                                    ])->columns(2),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (Printer $record): string => $record->printerModel?->brand ?? 'Unknown'),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->icon('heroicon-m-computer-desktop'),

                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'Ready',
                        'warning' => 'Printing',
                        'danger' => fn ($state): bool => in_array($state, ['Error', 'Paper Jam', 'Offline']),
                        'info' => 'Maintenance',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'Ready',
                        'heroicon-o-printer' => 'Printing',
                        'heroicon-o-exclamation-triangle' => fn ($state): bool => in_array($state, ['Error', 'Paper Jam']),
                        'heroicon-o-x-circle' => 'Offline',
                    ]),

                TextColumn::make('location')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('department')
                    ->badge()
                    ->toggleable(),

                TextColumn::make('total_pages_count')
                    ->label('Total Pages')
                    ->numeric()
                    ->sortable()
                    ->alignRight()
                    ->formatStateUsing(fn ($state) => number_format($state)),

                IconColumn::make('is_monitoring_enabled')
                    ->label('Monitor')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                TextColumn::make('last_polled_at')
                    ->label('Last Poll')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Ready' => 'Ready',
                        'Printing' => 'Printing',
                        'Error' => 'Error',
                        'Offline' => 'Offline',
                        'Maintenance' => 'Maintenance',
                    ])
                    ->multiple(),

                SelectFilter::make('department')
                    ->options([
                        'Production' => 'Production',
                        'Design' => 'Design',
                        'Sales' => 'Sales',
                        'Administration' => 'Administration',
                    ])
                    ->multiple(),

                SelectFilter::make('is_active')
                    ->label('Active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('poll')
                    ->label('Poll Now')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->action(function (Printer $record) {
                        $record->update(['last_polled_at' => now()]);
                        // Dispatch job to poll printer
                        \Filament\Notifications\Notification::make()
                            ->title('Polling triggered')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Admin\Resources\PrinterResource\RelationManagers\TonerLevelsRelationManager::class,
            \App\Filament\Admin\Resources\PrinterResource\RelationManagers\PaperInventoryRelationManager::class,
            \App\Filament\Admin\Resources\PrinterResource\RelationManagers\PrintJobsRelationManager::class,
        ];
    }

    public static function getPages(): array
{
    return [
        'index' => Pages\ListPrinters::route('/'),
        'create' => Pages\CreatePrinter::route('/create'),
        'view' => Pages\ViewPrinter::route('/{record}'),
        'edit' => Pages\EditPrinter::route('/{record}/edit'),
    ];
}

}
