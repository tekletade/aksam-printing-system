<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
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
use Carbon\Carbon;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'HR Management';

    protected static ?string $navigationLabel = 'Attendance';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Attendance Information')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->relationship('employee', 'first_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn ($record) =>
                                $record->first_name . ' ' . $record->father_name . ' ' . $record->grandfather_name
                            ),

                        Forms\Components\DatePicker::make('attendance_date')
                            ->required()
                            ->default(now()),

                        Forms\Components\DateTimePicker::make('check_in')
                            ->required()
                            ->default(now()),

                        Forms\Components\DateTimePicker::make('check_out')
                            ->after('check_in'),

                        Forms\Components\Select::make('shift_id')
                            ->relationship('shift', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('status')
                            ->options([
                                'present' => 'Present',
                                'absent' => 'Absent',
                                'late' => 'Late',
                                'half_day' => 'Half Day',
                                'on_leave' => 'On Leave',
                                'holiday' => 'Holiday',
                                'weekend' => 'Weekend',
                            ])
                            ->required()
                            ->default('present'),

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
                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable(['first_name', 'father_name', 'grandfather_name'])
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('attendance_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('check_in')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('check_out')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('total_hours')
                    ->label('Hours')
                    ->formatStateUsing(fn ($record) =>
                        $record->total_hours ? number_format($record->total_hours, 1) . ' hrs' : '-'
                    )
                    ->badge()
                    ->color(fn ($state) => $state > 8 ? 'success' : 'gray'),

                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'present',
                        'danger' => 'absent',
                        'warning' => 'late',
                        'info' => 'half_day',
                        'gray' => 'on_leave',
                        'secondary' => 'holiday',
                        'secondary' => 'weekend',
                    ]),

                BadgeColumn::make('is_late')
                    ->label('Late')
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                    ->color(fn ($state) => $state ? 'danger' : 'success'),

                BadgeColumn::make('is_overtime')
                    ->label('Overtime')
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                    ->color(fn ($state) => $state ? 'warning' : 'gray'),

                TextColumn::make('shift.name')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'late' => 'Late',
                        'half_day' => 'Half Day',
                        'on_leave' => 'On Leave',
                    ])
                    ->multiple(),

                SelectFilter::make('employee_id')
                    ->relationship('employee', 'first_name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->label('Employee'),

                Filter::make('attendance_date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('attendance_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('attendance_date', '<=', $date),
                            );
                    }),

                Filter::make('is_late')
                    ->label('Late Check-ins')
                    ->query(fn (Builder $query): Builder => $query->where('is_late', true)),

                Filter::make('is_overtime')
                    ->label('With Overtime')
                    ->query(fn (Builder $query): Builder => $query->where('is_overtime', true)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_present')
                        ->label('Mark as Present')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['status' => 'present'])),
                ]),
            ])
            ->defaultSort('attendance_date', 'desc');
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'view' => Pages\ViewAttendance::route('/{record}'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
{
    return static::getModel()::whereDate('attendance_date', today())
        ->where('status', 'present')->count();
}
}
