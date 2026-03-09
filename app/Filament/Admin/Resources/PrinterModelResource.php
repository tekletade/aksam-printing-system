<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PrinterModelResource\Pages;
use App\Filament\Admin\Resources\PrinterModelResource\RelationManagers;
use App\Models\PrinterModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrinterModelResource extends Resource
{
    protected static ?string $model = PrinterModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Machine Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('brand')
                    ->required(),
                Forms\Components\TextInput::make('model_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('specifications'),
                Forms\Components\TextInput::make('supported_media_types'),
                Forms\Components\TextInput::make('default_paper_capacity')
                    ->numeric(),
                Forms\Components\Toggle::make('is_color')
                    ->required(),
                Forms\Components\Toggle::make('is_duplex_supported')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('brand'),
                Tables\Columns\TextColumn::make('model_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('default_paper_capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_color')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_duplex_supported')
                    ->boolean(),
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
            'index' => Pages\ListPrinterModels::route('/'),
            'create' => Pages\CreatePrinterModel::route('/create'),
            'edit' => Pages\EditPrinterModel::route('/{record}/edit'),
        ];
    }
}
