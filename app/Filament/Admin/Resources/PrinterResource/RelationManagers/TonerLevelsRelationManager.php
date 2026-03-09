<?php

namespace App\Filament\Admin\Resources\PrinterResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TonerLevelsRelationManager extends RelationManager
{
    protected static string $relationship = 'tonerLevels';

    protected static ?string $title = 'Toner Levels';

    protected static ?string $recordTitleAttribute = 'toner_color';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('toner_color')
                    ->options([
                        'black' => 'Black',
                        'cyan' => 'Cyan',
                        'magenta' => 'Magenta',
                        'yellow' => 'Yellow',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('current_level')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%'),
                Forms\Components\TextInput::make('threshold_warning')
                    ->numeric()
                    ->default(15)
                    ->suffix('%'),
                Forms\Components\TextInput::make('threshold_critical')
                    ->numeric()
                    ->default(5)
                    ->suffix('%'),
                Forms\Components\DateTimePicker::make('last_replaced_at'),
                Forms\Components\TextInput::make('toner_model')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('toner_color')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'black' => 'gray',
                        'cyan' => 'info',
                        'magenta' => 'danger',
                        'yellow' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('current_level')
                    ->numeric()
                    ->suffix('%')
                    ->badge()
                    ->color(fn ($record): string => match (true) {
                        $record->current_level <= $record->threshold_critical => 'danger',
                        $record->current_level <= $record->threshold_warning => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('threshold_warning')
                    ->numeric()
                    ->suffix('%'),
                Tables\Columns\TextColumn::make('threshold_critical')
                    ->numeric()
                    ->suffix('%'),
                Tables\Columns\TextColumn::make('last_replaced_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('toner_model'),
            ])
            ->filters([
                Tables\Filters\Filter::make('low_toner')
                    ->label('Low Toner')
                    ->query(fn (Builder $query): Builder => $query->whereColumn('current_level', '<=', 'threshold_warning')),
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
