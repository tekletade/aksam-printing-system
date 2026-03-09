<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'HR Management';

    protected static ?string $navigationLabel = 'Employees';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Employee Information')
                    ->tabs([
                        Tab::make('Personal Information')
                            ->schema([
                                Section::make('Basic Details')
                                    ->schema([
                                        Forms\Components\TextInput::make('employee_id')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->default(fn () => 'EMP' . date('Y') . str_pad(Employee::count() + 1, 4, '0', STR_PAD_LEFT)),
                                        Forms\Components\TextInput::make('first_name')->required(),
                                        Forms\Components\TextInput::make('father_name')->required(),
                                        Forms\Components\TextInput::make('grandfather_name')->required(),
                                        Forms\Components\TextInput::make('last_name'),
                                        Forms\Components\DatePicker::make('date_of_birth')->required(),
                                        Forms\Components\Select::make('gender')
                                            ->options(['male' => 'Male', 'female' => 'Female'])
                                            ->required(),
                                        Forms\Components\Select::make('marital_status')
                                            ->options(['single' => 'Single', 'married' => 'Married']),
                                    ])->columns(3),

                                Section::make('Contact')
                                    ->schema([
                                        Forms\Components\TextInput::make('phone_primary')->tel()->required(),
                                        Forms\Components\TextInput::make('phone_secondary')->tel(),
                                        Forms\Components\TextInput::make('email')->email(),
                                        Forms\Components\TextInput::make('emergency_contact_name'),
                                        Forms\Components\TextInput::make('emergency_contact_phone')->tel(),
                                    ])->columns(2),
                            ]),

                        Tab::make('Employment')
                            ->schema([
                                Section::make('Employment Details')
                                    ->schema([
                                        Forms\Components\Select::make('department')
                                            ->options([
                                                'Production' => 'Production',
                                                'Design' => 'Design',
                                                'Sales' => 'Sales',
                                                'Admin' => 'Administration',
                                                'HR' => 'Human Resources',
                                                'Finance' => 'Finance',
                                            ])
                                            ->required(),
                                        Forms\Components\TextInput::make('position')->required(),
                                        Forms\Components\TextInput::make('job_title')->required(),
                                        Forms\Components\Select::make('employment_type')
                                            ->options([
                                                'permanent' => 'Permanent',
                                                'contract' => 'Contract',
                                                'probation' => 'Probation',
                                                'intern' => 'Intern',
                                            ])
                                            ->required(),
                                        Forms\Components\DatePicker::make('hire_date')->required(),
                                        Forms\Components\Select::make('reports_to')
                                            ->relationship('reportsTo', 'first_name'),
                                        Forms\Components\Select::make('status')
                                            ->options([
                                                'active' => 'Active',
                                                'inactive' => 'Inactive',
                                                'on_leave' => 'On Leave',
                                            ])
                                            ->required()
                                            ->default('active'),
                                    ])->columns(2),
                            ]),

                        Tab::make('Government IDs')
                            ->schema([
                                Section::make('ID Numbers')
                                    ->schema([
                                        Forms\Components\TextInput::make('pension_number'),
                                        Forms\Components\TextInput::make('tin_number'),
                                        Forms\Components\TextInput::make('passport_number'),
                                        Forms\Components\TextInput::make('kebele_id_number'),
                                    ])->columns(2),
                            ]),

                        Tab::make('Bank Details')
                            ->schema([
                                Section::make('Bank Account')
                                    ->schema([
                                        Forms\Components\Select::make('bank_name')
                                            ->options([
                                                'Commercial Bank of Ethiopia' => 'Commercial Bank of Ethiopia',
                                                'Dashen Bank' => 'Dashen Bank',
                                                'Awash Bank' => 'Awash Bank',
                                            ])
                                            ->searchable(),
                                        Forms\Components\TextInput::make('bank_account_number'),
                                        Forms\Components\TextInput::make('bank_branch'),
                                    ])->columns(2),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee_id')->searchable()->sortable(),
                TextColumn::make('full_name')->searchable(['first_name', 'father_name']),
                TextColumn::make('department')->searchable(),
                TextColumn::make('position')->searchable(),
                TextColumn::make('phone_primary')->icon('heroicon-m-phone'),
                BadgeColumn::make('employment_type')
                    ->colors([
                        'success' => 'permanent',
                        'warning' => 'contract',
                        'info' => 'probation',
                    ]),
                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'warning' => 'on_leave',
                    ]),
                TextColumn::make('hire_date')->date()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department')
                    ->options([
                        'Production' => 'Production',
                        'Design' => 'Design',
                        'Sales' => 'Sales',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
