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
                    // DatePicker::make('date')
                    //     ->label('Date')
                    //     ->required(),
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
                                ->required()
                                ->columnSpan(2),
                            TextInput::make('kVA')
                                ->label('kVA')
                                ->columnSpan(2),
                            TextInput::make('start_time')
                                ->label('Start Time')
                                ->placeholder('HH:MM')
                                ->mask('99:99')
                                ->required()
                                ->rules(['regex:/^([0-9]|0[0-9]|1[0-2]):[0-5][0-9]$/'])
                                ->helperText('Format: HH:MM (12-hour format)')
                                ->columnSpan(1),
                            Select::make('start_period')
                                ->label('AM/PM')
                                ->options([
                                    'AM' => 'AM',
                                    'PM' => 'PM',
                                ])
                                ->required()
                                ->columnSpan(1),
                            TextInput::make('end_time')
                                ->label('End Time')
                                ->placeholder('HH:MM')
                                ->mask('99:99')
                                ->required()
                                ->rules(['regex:/^([0-9]|0[0-9]|1[0-2]):[0-5][0-9]$/'])
                                ->helperText('Format: HH:MM (12-hour format)')
                                ->columnSpan(1),
                            Select::make('end_period')
                                ->label('AM/PM')
                                ->options([
                                    'AM' => 'AM',
                                    'PM' => 'PM',
                                ])
                                ->required()
                                ->columnSpan(1),
                        ])
                        ->columns(6)
                        ->addActionLabel('Add Equipment Usage'),
                ])
        ];
    }

    public function submit(): void
    {
        // Validate the form data
        $this->validate();

        

        // Loop through the logs and save each entry
        foreach ($this->logs as $log) {

            $startTime = \Carbon\Carbon::createFromFormat('h:i A', $log['start_time'] . ' ' . $log['start_period'])->format('H:i:s');
            $endTime = \Carbon\Carbon::createFromFormat('h:i A', $log['end_time'] . ' ' . $log['end_period'])->format('H:i:s');

            \App\Models\CustomerUsage::create([
                'customer_id' => $this->customer_id,
                'equipment_id' => $log['equipment_id'],
                'kVA' => $log['kVA'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'date' => $this->date,
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