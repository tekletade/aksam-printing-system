<?php

namespace App\Filament\Admin\Resources\PrinterResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaperInventoryRelationManager extends RelationManager
{
    protected static string $relationship = 'paperInventory';

    protected static ?string $title = 'Paper Trays';

    protected static ?string $recordTitleAttribute = 'tray_name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('tray_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('paper_size')
                    ->options([
                        'A4' => 'A4',
                        'A3' => 'A3',
                        'A2' => 'A2',
                        'A1' => 'A1',
                        'A0' => 'A0',
                        'Letter' => 'Letter',
                        'Legal' => 'Legal',
                    ])
                    ->required(),
                Forms\Components\Select::make('paper_type')
                    ->options([
                        'Plain' => 'Plain',
                        'Glossy' => 'Glossy',
                        'Recycled' => 'Recycled',
                        'Photo' => 'Photo',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('current_sheets')
                    ->required()
                    ->numeric()
                    ->minValue(0),
                Forms\Components\TextInput::make('max_capacity')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Forms\Components\TextInput::make('threshold_reorder')
                    ->numeric()
                    ->default(100),
                Forms\Components\TextInput::make('threshold_critical')
                    ->numeric()
                    ->default(50),
                Forms\Components\DateTimePicker::make('last_refilled_at'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tray_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('paper_size')
                    ->badge(),
                Tables\Columns\TextColumn::make('paper_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('current_sheets')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record): string => match (true) {
                        $record->current_sheets <= $record->threshold_critical => 'danger',
                        $record->current_sheets <= $record->threshold_reorder => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('max_capacity')
                    ->numeric(),
                Tables\Columns\TextColumn::make('last_refilled_at')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\Filter::make('low_paper')
                    ->label('Low Paper')
                    ->query(fn (Builder $query): Builder => $query->whereColumn('current_sheets', '<=', 'threshold_reorder')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
