<?php

namespace App\Filament\Admin\Resources\PrinterResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PrintJobsRelationManager extends RelationManager
{
    protected static string $relationship = 'printJobs';

    protected static ?string $title = 'Print Jobs';

    protected static ?string $recordTitleAttribute = 'job_id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('job_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('document_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('pages')
                    ->numeric()
                    ->default(0),
                Forms\Components\Select::make('color_mode')
                    ->options([
                        'black_white' => 'Black & White',
                        'color' => 'Color',
                    ])
                    ->default('black_white'),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('pending'),
                Forms\Components\TextInput::make('total_price')
                    ->numeric()
                    ->prefix('ETB'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('job_id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('document_name')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('pages')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('color_mode')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('ETB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
