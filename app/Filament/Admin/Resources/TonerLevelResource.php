<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TonerLevelResource\Pages;
use App\Filament\Admin\Resources\TonerLevelResource\RelationManagers;
use App\Models\TonerLevel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TonerLevelResource extends Resource
{
    protected static ?string $model = TonerLevel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    // TonerLevelResource.php
protected static ?string $navigationGroup = 'Machine Management';
protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('printer_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('toner_color')
                    ->required()
                    ->maxLength(255)
                    ->default('black'),
                Forms\Components\TextInput::make('toner_model')
                    ->maxLength(255),
                Forms\Components\TextInput::make('toner_serial')
                    ->maxLength(255),
                Forms\Components\TextInput::make('current_level')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('estimated_pages_remaining')
                    ->numeric(),
                Forms\Components\TextInput::make('threshold_warning')
                    ->required()
                    ->numeric()
                    ->default(15),
                Forms\Components\TextInput::make('threshold_critical')
                    ->required()
                    ->numeric()
                    ->default(5),
                Forms\Components\Toggle::make('is_low')
                    ->required(),
                Forms\Components\Toggle::make('is_critical')
                    ->required(),
                Forms\Components\DateTimePicker::make('last_replaced_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('printer_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('toner_color')
                    ->searchable(),
                Tables\Columns\TextColumn::make('toner_model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('toner_serial')
                    ->searchable(),
                Tables\Columns\TextColumn::make('current_level')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimated_pages_remaining')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('threshold_warning')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('threshold_critical')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_low')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_critical')
                    ->boolean(),
                Tables\Columns\TextColumn::make('last_replaced_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListTonerLevels::route('/'),
            'create' => Pages\CreateTonerLevel::route('/create'),
            'edit' => Pages\EditTonerLevel::route('/{record}/edit'),
        ];
    }
}
