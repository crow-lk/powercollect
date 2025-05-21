<?php

namespace App\Filament\Pages;

use App\Models\Customer;
use App\Models\Equipment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class LogCustomerUsage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationLabel = 'Log Customer Usage';
    protected static ?string $navigationGroup = 'Usage';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.log-customer-usage';

    public $customer_id;
    public $date;
    public $logs = [];

    protected function getFormSchema(): array
    {
        return [
            Section::make('Customer Details')
                ->schema([
                    Select::make('customer_id')
                        ->label('Customer')
                        ->options(Customer::pluck('name', 'id'))
                        ->searchable()
                        ->reactive()
                        ->required(),
                ]),

            Section::make('Equipment Usage')
                ->schema([
                    Repeater::make('logs')
                        ->label('Equipment Usage')
                        ->schema([
                            Select::make('equipment_id')
                                ->label('Equipment')
                                ->options(Equipment::pluck('type', 'id'))
                                ->searchable()
                                ->reactive()
                                ->required(),
                            TextInput::make('kVA')
                                ->label('kVA'),
                            TimePicker::make('start_time')
                                ->required(),
                            TimePicker::make('end_time')
                                ->required(),
                        ])
                        ->columns(4),
                ])
        ];

        
    }



    public function submit(): void{
            // Validate the form data
            $this->validate();

            // Loop through the logs and save each entry
            foreach ($this->logs as $log) {
                \App\Models\CustomerUsage::create([
                    'customer_id' => $this->customer_id, // From the form
                    'equipment_id' => $log['equipment_id'], // From the repeater
                    'kVA' => $log['kVA'], // From the repeater
                    'start_time' => $log['start_time'], // From the repeater
                    'end_time' => $log['end_time'], // From the repeater
                    'date' => $this->date, // From the form
                ]);
            }

            // Notify the user of success
            Notification::make()
                ->title('Customer usage logged successfully!')
                ->success()
                ->send();

            // Optionally reset the form
            $this->reset(['customer_id', 'date', 'logs']);
            }
}
