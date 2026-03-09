<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PaperInventoryResource\Pages;
use App\Filament\Admin\Resources\PaperInventoryResource\RelationManagers;
use App\Models\PaperInventory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaperInventoryResource extends Resource
{
    protected static ?string $model = PaperInventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    // PaperInventoryResource.php
protected static ?string $navigationGroup = 'Machine Management';
protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListPaperInventories::route('/'),
            'create' => Pages\CreatePaperInventory::route('/create'),
            'edit' => Pages\EditPaperInventory::route('/{record}/edit'),
        ];
    }
}
