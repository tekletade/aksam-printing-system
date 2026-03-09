<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EmployeeSalaryResource\Pages;
use App\Models\EmployeeSalary;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class EmployeeSalaryResource extends Resource
{
    protected static ?string $model = EmployeeSalary::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Payroll Management';

    protected static ?string $navigationLabel = 'Employee Salaries';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Salary Information')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->relationship('employee', 'first_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn ($record) =>
                                $record->first_name . ' ' . $record->father_name . ' - ' . $record->employee_id
                            ),

                        Forms\Components\TextInput::make('basic_salary')
                            ->required()
                            ->numeric()
                            ->prefix('ETB')
                            ->minValue(0)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) =>
                                $set('gross_salary', $state + $get('housing_allowance') + $get('transport_allowance') + $get('position_allowance') + $get('other_allowances'))
                            ),

                        Forms\Components\TextInput::make('housing_allowance')
                            ->numeric()
                            ->prefix('ETB')
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) =>
                                $set('gross_salary', $get('basic_salary') + $state + $get('transport_allowance') + $get('position_allowance') + $get('other_allowances'))
                            ),

                        Forms\Components\TextInput::make('transport_allowance')
                            ->numeric()
                            ->prefix('ETB')
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) =>
                                $set('gross_salary', $get('basic_salary') + $get('housing_allowance') + $state + $get('position_allowance') + $get('other_allowances'))
                            ),

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

                        Forms\Components\TextInput::make('gross_salary')
                            ->required()
                            ->numeric()
                            ->prefix('ETB')
                            ->disabled()
                            ->dehydrated(true),

                        Forms\Components\Select::make('pay_frequency')
                            ->options([
                                'monthly' => 'Monthly',
                                'bi-weekly' => 'Bi-Weekly',
                                'weekly' => 'Weekly',
                            ])
                            ->required()
                            ->default('monthly'),

                        Forms\Components\DatePicker::make('effective_from')
                            ->required()
                            ->default(now()),

                        Forms\Components\DatePicker::make('effective_to')
                            ->after('effective_from'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        Forms\Components\Textarea::make('notes')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Bank Details')
                    ->schema([
                        Forms\Components\Select::make('bank_name')
                            ->options([
                                'Commercial Bank of Ethiopia' => 'Commercial Bank of Ethiopia',
                                'Dashen Bank' => 'Dashen Bank',
                                'Awash Bank' => 'Awash Bank',
                                'Bank of Abyssinia' => 'Bank of Abyssinia',
                                'United Bank' => 'United Bank',
                                'NIB International Bank' => 'NIB International Bank',
                                'Zemen Bank' => 'Zemen Bank',
                            ])
                            ->searchable(),

                        Forms\Components\TextInput::make('bank_account')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('bank_branch')
                            ->maxLength(255),
                    ])->columns(2),
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

                TextColumn::make('employee.employee_id')
                    ->label('Employee ID')
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('basic_salary')
                    ->money('ETB')
                    ->sortable()
                    ->alignRight(),

                TextColumn::make('housing_allowance')
                    ->money('ETB')
                    ->alignRight()
                    ->toggleable(),

                TextColumn::make('transport_allowance')
                    ->money('ETB')
                    ->alignRight()
                    ->toggleable(),

                TextColumn::make('gross_salary')
                    ->money('ETB')
                    ->sortable()
                    ->alignRight()
                    ->weight('bold')
                    ->color('success'),

                BadgeColumn::make('pay_frequency')
                    ->colors([
                        'primary' => 'monthly',
                        'warning' => 'bi-weekly',
                        'info' => 'weekly',
                    ]),

                TextColumn::make('effective_from')
                    ->date()
                    ->sortable(),

                BadgeColumn::make('is_active')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Inactive')
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),

                SelectFilter::make('pay_frequency')
                    ->options([
                        'monthly' => 'Monthly',
                        'bi-weekly' => 'Bi-Weekly',
                        'weekly' => 'Weekly',
                    ]),

                SelectFilter::make('employee_id')
                    ->relationship('employee', 'first_name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->label('Employee'),

                Filter::make('effective_from')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('effective_from', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('effective_from', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployeeSalaries::route('/'),
            'create' => Pages\CreateEmployeeSalary::route('/create'),
            'view' => Pages\ViewEmployeeSalary::route('/{record}'),
            'edit' => Pages\EditEmployeeSalary::route('/{record}/edit'),
        ];
    }
}
