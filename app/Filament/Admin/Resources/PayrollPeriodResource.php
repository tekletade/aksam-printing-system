<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PayrollPeriodResource\Pages;
use App\Models\PayrollPeriod;
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

class PayrollPeriodResource extends Resource
{
    protected static ?string $model = PayrollPeriod::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Payroll Management';

    protected static ?string $navigationLabel = 'Payroll Periods';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Period Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->default(fn () => 'Payroll ' . now()->format('F Y')),

                        Forms\Components\Select::make('type')
                            ->options([
                                'monthly' => 'Monthly',
                                'bi-weekly' => 'Bi-Weekly',
                                'weekly' => 'Weekly',
                            ])
                            ->required()
                            ->default('monthly')
                            ->reactive(),

                        Forms\Components\DatePicker::make('start_date')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($get('type') === 'monthly' && $state) {
                                    $set('end_date', $state->copy()->endOfMonth());
                                }
                            }),

                        Forms\Components\DatePicker::make('end_date')
                            ->required()
                            ->after('start_date'),

                        Forms\Components\DatePicker::make('payment_date')
                            ->required()
                            ->after('end_date'),

                        Forms\Components\Select::make('status')
                            ->options([
                                'open' => 'Open',
                                'processing' => 'Processing',
                                'review' => 'Review',
                                'approved' => 'Approved',
                                'paid' => 'Paid',
                                'closed' => 'Closed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('open'),

                        Forms\Components\Textarea::make('notes')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'monthly',
                        'warning' => 'bi-weekly',
                        'info' => 'weekly',
                    ]),

                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),

                BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'open',
                        'warning' => 'processing',
                        'info' => 'review',
                        'success' => 'approved',
                        'success' => 'paid',
                        'secondary' => 'closed',
                        'danger' => 'cancelled',
                    ]),

                TextColumn::make('employee_count')
                    ->label('Employees')
                    ->numeric()
                    ->sortable()
                    ->alignRight(),

                TextColumn::make('total_gross_pay')
                    ->money('ETB')
                    ->sortable()
                    ->alignRight()
                    ->toggleable(),

                TextColumn::make('total_net_pay')
                    ->money('ETB')
                    ->sortable()
                    ->alignRight()
                    ->weight('bold')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'monthly' => 'Monthly',
                        'bi-weekly' => 'Bi-Weekly',
                        'weekly' => 'Weekly',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'processing' => 'Processing',
                        'review' => 'Review',
                        'approved' => 'Approved',
                        'paid' => 'Paid',
                        'closed' => 'Closed',
                    ])
                    ->multiple(),

                Filter::make('start_date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('end_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('process')
                    ->label('Process Payroll')
                    ->icon('heroicon-o-calculator')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'open')
                    ->action(function ($record) {
                        // Dispatch job to process payroll
                        $record->update(['status' => 'processing']);
                        // ProcessPayrollJob::dispatch($record);
                    }),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'review')
                    ->action(fn ($record) => $record->update([
                        'status' => 'approved',
                        'approved_at' => now(),
                    ])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Admin\Resources\PayrollPeriodResource\RelationManagers\PayrollRunsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayrollPeriods::route('/'),
            'create' => Pages\CreatePayrollPeriod::route('/create'),
            'view' => Pages\ViewPayrollPeriod::route('/{record}'),
            'edit' => Pages\EditPayrollPeriod::route('/{record}/edit'),
        ];
    }
}
