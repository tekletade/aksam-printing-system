<?php

namespace App\Filament\Admin\Resources\PayrollPeriodResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class PayrollRunsRelationManager extends RelationManager
{
    protected static string $relationship = 'payrollRuns';

    protected static ?string $title = 'Payroll Entries';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->relationship('employee', 'first_name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('gross_pay')
                    ->numeric()
                    ->prefix('ETB')
                    ->required(),
                Forms\Components\TextInput::make('total_deductions')
                    ->numeric()
                    ->prefix('ETB')
                    ->required(),
                Forms\Components\TextInput::make('net_pay')
                    ->numeric()
                    ->prefix('ETB')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'paid' => 'Paid',
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.full_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('gross_pay')
                    ->money('ETB')
                    ->sortable(),
                TextColumn::make('total_deductions')
                    ->money('ETB')
                    ->sortable(),
                TextColumn::make('net_pay')
                    ->money('ETB')
                    ->sortable()
                    ->weight('bold'),
                BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'pending',
                        'success' => 'approved',
                        'success' => 'paid',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'paid' => 'Paid',
                    ]),
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
