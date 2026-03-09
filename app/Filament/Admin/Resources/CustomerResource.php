<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CustomerResource\Pages;
use App\Models\Customer;
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
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Customer Management';

    protected static ?string $navigationLabel = 'Customers';

    protected static ?string $modelLabel = 'Customer';

    protected static ?string $pluralModelLabel = 'Customers';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Customer Information')
                    ->tabs([
                        Tab::make('Basic Information')
                            ->schema([
                                Section::make('Customer Details')
                                    ->schema([
                                        Forms\Components\TextInput::make('customer_code')
                                            ->label('Customer Code')
                                            ->required()
                                            ->maxLength(50)
                                            ->default(fn () => 'CUST' . date('Y') . str_pad(Customer::count() + 1, 4, '0', STR_PAD_LEFT))
                                            ->unique(ignoreRecord: true),

                                        Forms\Components\TextInput::make('name')
                                            ->label('Full Name / Company Name')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\Select::make('type')
                                            ->options([
                                                'individual' => 'Individual',
                                                'company' => 'Company',
                                                'vip' => 'VIP',
                                            ])
                                            ->required()
                                            ->default('individual')
                                            ->reactive(),

                                        Forms\Components\TextInput::make('company_name')
                                            ->label('Company Name')
                                            ->visible(fn ($get) => $get('type') === 'company')
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('tin_number')
                                            ->label('TIN Number')
                                            ->visible(fn ($get) => $get('type') === 'company')
                                            ->maxLength(50),
                                    ])->columns(2),

                                Section::make('Contact Information')
                                    ->schema([
                                        Forms\Components\TextInput::make('email')
                                            ->email()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('phone')
                                            ->tel()
                                            ->required()
                                            ->maxLength(20),

                                        Forms\Components\TextInput::make('alternate_phone')
                                            ->tel()
                                            ->maxLength(20),
                                    ])->columns(2),
                            ]),

                        Tab::make('Address')
                            ->schema([
                                Section::make('Address Details')
                                    ->schema([
                                        Forms\Components\Textarea::make('address')
                                            ->maxLength(500)
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('city')
                                            ->default('Addis Ababa')
                                            ->required(),

                                        Forms\Components\TextInput::make('sub_city')
                                            ->label('Sub City'),

                                        Forms\Components\TextInput::make('woreda'),

                                        Forms\Components\TextInput::make('house_number')
                                            ->label('House Number'),
                                    ])->columns(2),
                            ]),

                        Tab::make('Communication')
                            ->schema([
                                Section::make('Messaging Channels')
                                    ->schema([
                                        Forms\Components\TextInput::make('telegram_chat_id')
                                            ->label('Telegram Chat ID')
                                            ->helperText('For receiving order notifications'),

                                        Forms\Components\TextInput::make('whatsapp_number')
                                            ->label('WhatsApp Number')
                                            ->tel(),

                                        Forms\Components\Select::make('preferred_channel')
                                            ->options([
                                                'telegram' => 'Telegram',
                                                'whatsapp' => 'WhatsApp',
                                                'sms' => 'SMS',
                                                'email' => 'Email',
                                            ])
                                            ->default('telegram'),
                                    ])->columns(2),
                            ]),

                        Tab::make('Financial')
                            ->schema([
                                Section::make('Credit Information')
                                    ->schema([
                                        Forms\Components\TextInput::make('credit_limit')
                                            ->label('Credit Limit (ETB)')
                                            ->numeric()
                                            ->default(0)
                                            ->prefix('ETB'),

                                        Forms\Components\TextInput::make('outstanding_balance')
                                            ->label('Outstanding Balance')
                                            ->numeric()
                                            ->default(0)
                                            ->prefix('ETB')
                                            ->disabled()
                                            ->dehydrated(false),

                                        Forms\Components\TextInput::make('total_purchases')
                                            ->label('Total Purchases')
                                            ->numeric()
                                            ->default(0)
                                            ->prefix('ETB')
                                            ->disabled()
                                            ->dehydrated(false),

                                        Forms\Components\TextInput::make('total_orders')
                                            ->numeric()
                                            ->default(0)
                                            ->disabled()
                                            ->dehydrated(false),
                                    ])->columns(2),
                            ]),

                        Tab::make('Settings')
                            ->schema([
                                Section::make('Account Status')
                                    ->schema([
                                        Forms\Components\Select::make('status')
                                            ->options([
                                                'active' => 'Active',
                                                'inactive' => 'Inactive',
                                                'blocked' => 'Blocked',
                                            ])
                                            ->required()
                                            ->default('active'),

                                        Forms\Components\Toggle::make('is_vip')
                                            ->label('VIP Customer')
                                            ->default(false),

                                        Forms\Components\Textarea::make('notes')
                                            ->maxLength(65535)
                                            ->columnSpanFull(),

                                        Forms\Components\KeyValue::make('preferences')
                                            ->keyLabel('Preference')
                                            ->valueLabel('Value'),
                                    ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'company',
                        'success' => 'vip',
                        'gray' => 'individual',
                    ]),

                TextColumn::make('phone')
                    ->searchable()
                    ->icon('heroicon-m-phone'),

                TextColumn::make('email')
                    ->searchable()
                    ->icon('heroicon-m-envelope'),

                TextColumn::make('total_orders')
                    ->label('Orders')
                    ->numeric()
                    ->sortable()
                    ->alignRight(),

                TextColumn::make('outstanding_balance')
                    ->label('Balance')
                    ->money('ETB')
                    ->sortable()
                    ->alignRight()
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),

                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger' => 'blocked',
                    ]),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'individual' => 'Individual',
                        'company' => 'Company',
                        'vip' => 'VIP',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'blocked' => 'Blocked',
                    ]),

                Tables\Filters\Filter::make('has_outstanding')
                    ->label('Has Outstanding Balance')
                    ->query(fn (Builder $query): Builder => $query->where('outstanding_balance', '>', 0)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('orders')
                    ->label('View Orders')
                    ->icon('heroicon-o-shopping-bag')
                    ->url(fn (Customer $record): string => route('filament.admin.resources.orders.index', ['customer_id' => $record->id])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update(['status' => 'active'])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->action(fn ($records) => $records->each->update(['status' => 'inactive'])),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // We'll add relation managers later
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'active')->count();
    }
}
