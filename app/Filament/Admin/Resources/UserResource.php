<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'System Administration';

    protected static ?string $navigationLabel = 'Users';

    protected static ?string $modelLabel = 'User';

    protected static ?string $pluralModelLabel = 'Users';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('User Information')
                    ->tabs([
                        Tab::make('Basic Information')
                            ->schema([
                                Section::make('User Details')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('email')
                                            ->email()
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('password')
                                            ->password()
                                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                            ->dehydrated(fn ($state) => filled($state))
                                            ->required(fn (string $context): bool => $context === 'create')
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('phone')
                                            ->tel()
                                            ->maxLength(20),

                                        Forms\Components\FileUpload::make('avatar')
                                            ->image()
                                            ->avatar()
                                            ->imageEditor()
                                            ->circleCropper()
                                            ->directory('avatars')
                                            ->columnSpanFull(),
                                    ])->columns(2),
                            ]),

                        Tab::make('Roles & Permissions')
                            ->schema([
                                Section::make('Role Assignment')
                                    ->schema([
                                        Forms\Components\Select::make('roles')
                                            ->relationship('roles', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable()
                                            ->required()
                                            ->helperText('Select the roles for this user'),

                                        Forms\Components\Placeholder::make('permissions')
                                            ->label('Permissions Summary')
                                            ->content(function ($get) {
                                                $roles = $get('roles') ?? [];
                                                if (empty($roles)) {
                                                    return 'No permissions assigned yet.';
                                                }

                                                $permissions = Role::whereIn('id', $roles)
                                                    ->with('permissions')
                                                    ->get()
                                                    ->pluck('permissions')
                                                    ->flatten()
                                                    ->pluck('name')
                                                    ->unique()
                                                    ->sort()
                                                    ->values();

                                                if ($permissions->isEmpty()) {
                                                    return 'No specific permissions assigned.';
                                                }

                                                return view('filament.admin.components.permissions-list', [
                                                    'permissions' => $permissions
                                                ]);
                                            })
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Settings')
                            ->schema([
                                Section::make('Account Settings')
                                    ->schema([
                                        Forms\Components\DateTimePicker::make('email_verified_at')
                                            ->label('Email Verified At'),

                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Active')
                                            ->default(true)
                                            ->helperText('Inactive users cannot log in'),
                                    ])->columns(2),
                            ]),

                        Tab::make('Linked Records')
                            ->schema([
                                Section::make('Associated Profiles')
                                    ->schema([
                                        Forms\Components\Select::make('employee_id')
                                            ->label('Linked Employee')
                                            ->relationship('employee', 'first_name')
                                            ->searchable()
                                            ->preload()
                                            ->getOptionLabelFromRecordUsing(fn ($record) =>
                                                $record->first_name . ' ' . $record->father_name
                                            ),

                                        Forms\Components\Select::make('customer_id')
                                            ->label('Linked Customer')
                                            ->relationship('customer', 'name')
                                            ->searchable()
                                            ->preload(),
                                    ])->columns(2),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->circular()
                    ->defaultImageUrl(url('https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&color=7F9CF5&background=EBF4FF')),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('email')
                    ->searchable()
                    ->icon('heroicon-m-envelope'),

                TextColumn::make('phone')
                    ->searchable()
                    ->icon('heroicon-m-phone')
                    ->toggleable(),

                BadgeColumn::make('roles.name')
                    ->label('Roles')
                    ->color('primary')
                    ->separator(',')
                    ->getStateUsing(fn ($record) => $record->roles->pluck('name')->join(', ')),

                BadgeColumn::make('email_verified_at')
                    ->label('Verified')
                    ->formatStateUsing(fn ($state) => $state ? 'Verified' : 'Unverified')
                    ->color(fn ($state): string => $state ? 'success' : 'danger'),

                BadgeColumn::make('is_active')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Inactive')
                    ->color(fn ($state): string => $state ? 'success' : 'danger'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),

                SelectFilter::make('is_active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),

                SelectFilter::make('email_verified')
                    ->label('Email Verification')
                    ->options([
                        'verified' => 'Verified',
                        'unverified' => 'Unverified',
                    ])
                    ->query(function ($query, array $data) {
                        return match ($data['value'] ?? null) {
                            'verified' => $query->whereNotNull('email_verified_at'),
                            'unverified' => $query->whereNull('email_verified_at'),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('impersonate')
                    ->label('Login as User')
                    ->icon('heroicon-o-user-switch')
                    ->color('warning')
                    ->visible(fn () => auth()->user()->hasRole('Super Admin'))
                    ->action(function ($record) {
                        // You'll need to install a package like "lab404/laravel-impersonate" for this
                        // auth()->user()->impersonate($record);
                    }),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
