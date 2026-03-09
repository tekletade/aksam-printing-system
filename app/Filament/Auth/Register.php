<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class Register extends BaseRegister
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),

                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->unique(User::class)
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('Phone Number')
                    ->tel()
                    ->required()
                    ->maxLength(20),

                Select::make('role')
                    ->label('Register as')
                    ->options([
                        'customer' => 'Customer',
                        'employee' => 'Employee',
                    ])
                    ->required()
                    ->default('customer')
                    ->reactive(),

                // Customer specific fields
                TextInput::make('company_name')
                    ->label('Company Name')
                    ->visible(fn ($get) => $get('role') === 'customer')
                    ->maxLength(255),

                // Employee specific fields (if registering as employee)
                TextInput::make('employee_id')
                    ->label('Employee ID')
                    ->visible(fn ($get) => $get('role') === 'employee')
                    ->maxLength(50),

                DatePicker::make('date_of_birth')
                    ->label('Date of Birth')
                    ->visible(fn ($get) => $get('role') === 'employee')
                    ->maxDate(now()->subYears(18)),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->same('passwordConfirmation'),

                TextInput::make('passwordConfirmation')
                    ->label('Confirm Password')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->dehydrated(false),
            ]);
    }

    protected function afterRegister(): void
    {
        $user = $this->getUser();

        // Assign role based on registration type
        $role = $this->form->getState()['role'] ?? 'customer';

        if ($role === 'customer') {
            $user->assignRole('Customer');

            // Create customer record
            \App\Models\Customer::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $this->form->getState()['phone'],
                'company_name' => $this->form->getState()['company_name'] ?? null,
                'customer_code' => 'CUST' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
            ]);

            Notification::make()
                ->title('Welcome!')
                ->body('Your customer account has been created successfully.')
                ->success()
                ->send();
        } elseif ($role === 'employee') {
            // For employee registration, they might need approval
            $user->assignRole('Operator');

            // You might want to send notification to HR/Admin
            Notification::make()
                ->title('Registration Pending Approval')
                ->body('Your employee account has been created and is pending approval.')
                ->warning()
                ->send();
        }
    }

    protected function getRegisterFormAction(): \Filament\Actions\Action
    {
        return parent::getRegisterFormAction()
            ->label('Create Account');
    }
}
