<?php

namespace App\Filament\Admin\Resources\PrinterResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StatusLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'statusLogs';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('previous_status')
                    ->maxLength(255),
                Forms\Components\TextInput::make('error_code')
                    ->maxLength(100),
                Forms\Components\Textarea::make('error_message')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\KeyValue::make('details')
                    ->keyLabel('Property')
                    ->valueLabel('Value'),
                Forms\Components\DateTimePicker::make('logged_at')
                    ->required()
                    ->default(now()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('status')
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Ready' => 'success',
                        'Printing' => 'info',
                        'Error', 'Paper Jam', 'Offline' => 'danger',
                        'Maintenance' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('previous_status')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('error_code')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('error_message')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('logged_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Ready' => 'Ready',
                        'Printing' => 'Printing',
                        'Error' => 'Error',
                        'Offline' => 'Offline',
                        'Maintenance' => 'Maintenance',
                        'Paper Jam' => 'Paper Jam',
                    ])
                    ->multiple(),
                Tables\Filters\Filter::make('logged_date')
                    ->form([
                        Forms\Components\DatePicker::make('logged_from'),
                        Forms\Components\DatePicker::make('logged_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['logged_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('logged_at', '>=', $date),
                            )
                            ->when(
                                $data['logged_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('logged_at', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('logged_at', 'desc');
    }
}
