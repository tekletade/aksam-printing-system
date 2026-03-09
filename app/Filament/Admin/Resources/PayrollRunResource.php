<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PayrollRunResource\Pages;
use App\Models\PayrollRun;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class PayrollRunResource extends Resource
{
    protected static ?string $model = PayrollRun::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Payroll Management';

    protected static ?string $navigationLabel = 'Payroll Runs';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Payroll Information')
                    ->schema([
                        Forms\Components\Select::make('payroll_period_id')
                            ->relationship('payrollPeriod', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('employee_id')
                            ->relationship('employee', 'first_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn ($record) =>
                                $record->first_name . ' ' . $record->father_name . ' - ' . $record->employee_id
                            ),

                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'calculated' => 'Calculated',
                                'reviewed' => 'Reviewed',
                                'approved' => 'Approved',
                                'paid' => 'Paid',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('pending'),

                        Forms\Components\Textarea::make('notes')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Earnings')
                    ->schema([
                        Forms\Components\TextInput::make('basic_salary')
                            ->numeric()
                            ->prefix('ETB')
                            ->default(0)
                            ->reactive(),

                        Forms\Components\TextInput::make('housing_allowance')
                            ->numeric()
                            ->prefix('ETB')
                            ->default(0)
                            ->reactive(),

                        Forms\Components\TextInput::make('transport_allowance')
                            ->numeric()
                            ->prefix('ETB')
                            ->default(0)
                            ->reactive(),

                        Forms\Components\TextInput::make('position_allowance')
                            ->numeric()
                            ->prefix('ETB')
                            ->default(0)
                            ->reactive(),

                        Forms\Components\TextInput::make('other_allowances')
                            ->numeric()
                            ->prefix('ETB')
                            ->default(0)
                            ->reactive(),

                        Forms\Components\TextInput::make('overtime_pay')
                            ->numeric()
                            ->prefix('ETB')
                            ->default(0)
                            ->reactive(),

                        Forms\Components\TextInput::make('bonus')
                            ->numeric()
                            ->prefix('ETB')
                            ->default(0)
                            ->reactive(),

                        Forms\Components\TextInput::make('commission')
                            ->numeric()
                            ->prefix('ETB')
                            ->default(0)
                            ->reactive(),

                        Forms\Components\TextInput::make('gross_pay')
                            ->numeric()
                            ->prefix('ETB')
                            ->disabled()
                            ->dehydrated(true),
                    ])->columns(3),

                Section::make('Deductions')
                    ->schema([
                        Forms\Components\TextInput::make('income_tax')
                            ->numeric()
                            ->prefix('ETB')
                            ->default(0)
                            ->reactive(),

                        Forms\Components\TextInput::make('pension_employee')
                            ->numeric()
                            ->prefix('ETB')
                            ->default(0)
                            ->reactive(),

                        Forms\Components\TextInput::make('pension_employer')
                            ->numeric()
                            ->prefix('ETB')
                            ->default(0)
                            ->reactive(),

                        Forms\Components\TextInput::make('loan_deduction')
                            ->numeric()
                            ->prefix('ETB')
                            ->default(0)
                            ->reactive(),

                        Forms\Components\TextInput::make('advance_deduction')
                            ->numeric()
                            ->prefix('ETB')
                            ->default(0)
                            ->reactive(),

                        Forms\Components\TextInput::make('other_deductions')
                            ->numeric()
                            ->prefix('ETB')
                            ->default(0)
                            ->reactive(),

                        Forms\Components\TextInput::make('total_deductions')
                            ->numeric()
                            ->prefix('ETB')
                            ->disabled()
                            ->dehydrated(true),

                        Forms\Components\TextInput::make('net_pay')
                            ->numeric()
                            ->prefix('ETB')
                            ->disabled()
                            ->dehydrated(true)
                            ->weight('bold')
                            ->color('success'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('payrollPeriod.name')
                    ->label('Period')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('gross_pay')
                    ->money('ETB')
                    ->sortable()
                    ->alignRight()
                    ->toggleable(),

                TextColumn::make('total_deductions')
                    ->money('ETB')
                    ->sortable()
                    ->alignRight()
                    ->color('danger')
                    ->toggleable(),

                TextColumn::make('net_pay')
                    ->money('ETB')
                    ->sortable()
                    ->alignRight()
                    ->weight('bold')
                    ->color('success'),

                BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'pending',
                        'warning' => 'calculated',
                        'info' => 'reviewed',
                        'success' => 'approved',
                        'success' => 'paid',
                        'danger' => 'cancelled',
                    ]),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'calculated' => 'Calculated',
                        'reviewed' => 'Reviewed',
                        'approved' => 'Approved',
                        'paid' => 'Paid',
                    ])
                    ->multiple(),

                SelectFilter::make('payroll_period_id')
                    ->relationship('payrollPeriod', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Payroll Period'),

                SelectFilter::make('employee_id')
                    ->relationship('employee', 'first_name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->label('Employee'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('generate_payslip')
                    ->label('Payslip')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->url(fn ($record) => route('payslip.generate', $record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayrollRuns::route('/'),
            'create' => Pages\CreatePayrollRun::route('/create'),
            'view' => Pages\ViewPayrollRun::route('/{record}'),
            'edit' => Pages\EditPayrollRun::route('/{record}/edit'),
        ];
    }
}
