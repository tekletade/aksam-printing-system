<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PrintJobResource\Pages;
use App\Models\PrintJob;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class PrintJobResource extends Resource
{
    protected static ?string $model = PrintJob::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'Machine Management';

    protected static ?string $navigationLabel = 'Print Jobs';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('printer_id')
                    ->relationship('printer', 'name')
                    ->required(),
                Forms\Components\Select::make('order_id')
                    ->relationship('order', 'order_number'),
                Forms\Components\TextInput::make('job_id')->required(),
                Forms\Components\TextInput::make('document_name'),
                Forms\Components\TextInput::make('pages')->numeric(),
                Forms\Components\Select::make('color_mode')
                    ->options([
                        'black_white' => 'Black & White',
                        'color' => 'Color',
                    ]),
                Forms\Components\Select::make('print_side')
                    ->options([
                        'simplex' => 'Simplex',
                        'duplex' => 'Duplex',
                    ]),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('pending'),
                Forms\Components\DateTimePicker::make('completed_at'),
                Forms\Components\TextInput::make('total_price')->numeric()->prefix('ETB'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('job_id')->searchable()->sortable(),
                TextColumn::make('printer.name')->searchable(),
                TextColumn::make('document_name')->searchable()->limit(30),
                TextColumn::make('pages')->numeric()->sortable(),
                BadgeColumn::make('color_mode'),
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),
                TextColumn::make('total_price')->money('ETB')->sortable(),
                TextColumn::make('completed_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                    ]),
                Tables\Filters\SelectFilter::make('printer_id')
                    ->relationship('printer', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrintJobs::route('/'),
            'create' => Pages\CreatePrintJob::route('/create'),
            'view' => Pages\ViewPrintJob::route('/{record}'),
            'edit' => Pages\EditPrintJob::route('/{record}/edit'),
        ];
    }
}
