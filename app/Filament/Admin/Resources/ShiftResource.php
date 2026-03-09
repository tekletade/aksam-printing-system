<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ShiftResource\Pages;
use App\Filament\Admin\Resources\ShiftResource\RelationManagers;
use App\Models\Shift;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShiftResource extends Resource
{
    protected static ?string $model = Shift::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    // ShiftResource.php
protected static ?string $navigationGroup = 'HR Management';
protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('start_time')
                    ->required(),
                Forms\Components\TextInput::make('end_time')
                    ->required(),
                Forms\Components\TextInput::make('break_minutes')
                    ->required()
                    ->numeric()
                    ->default(60),
                Forms\Components\TextInput::make('break_start'),
                Forms\Components\TextInput::make('break_end'),
                Forms\Components\TextInput::make('type')
                    ->required(),
                Forms\Components\TextInput::make('applicable_days'),
                Forms\Components\TextInput::make('hourly_rate_multiplier')
                    ->required()
                    ->numeric()
                    ->default(1.00),
                Forms\Components\TextInput::make('overtime_multiplier')
                    ->required()
                    ->numeric()
                    ->default(1.50),
                Forms\Components\TextInput::make('night_differential_multiplier')
                    ->required()
                    ->numeric()
                    ->default(1.25),
                Forms\Components\TextInput::make('grace_late_minutes')
                    ->required()
                    ->numeric()
                    ->default(15),
                Forms\Components\TextInput::make('grace_early_leave_minutes')
                    ->required()
                    ->numeric()
                    ->default(15),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_time'),
                Tables\Columns\TextColumn::make('end_time'),
                Tables\Columns\TextColumn::make('break_minutes')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('break_start'),
                Tables\Columns\TextColumn::make('break_end'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('hourly_rate_multiplier')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('overtime_multiplier')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('night_differential_multiplier')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('grace_late_minutes')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('grace_early_leave_minutes')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
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
            'index' => Pages\ListShifts::route('/'),
            'create' => Pages\CreateShift::route('/create'),
            'edit' => Pages\EditShift::route('/{record}/edit'),
        ];
    }
}
