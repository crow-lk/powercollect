<?php

namespace App\Filament\Pages;

use App\Models\ConsumerUsage;
use App\Models\Property;
use App\Models\PropertyPart;
use App\Models\Equipment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\DB;

class LogConsumerUsage extends Page
{
    protected static ?string $navigationLabel = 'Log Usage';
    protected static ?string $navigationGroup = 'Usage';
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.log-consumer-usage';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('property_id')
                    ->label('Property')
                    ->options(Property::all()
                        ->mapWithKeys(fn ($property) => [$property->id => $property->name ?? '']))
                    ->required()
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('property_part_data', null)),
                DatePicker::make('date')
                    ->label('Date')
                    ->required()
                    ->default(now()),
                Repeater::make('usage_data')
                    ->label('Usage Details')
                    ->schema([
                        Select::make('property_part_id')
                            ->label('Property Part')
                            ->options(PropertyPart::all()
                                ->mapWithKeys(fn ($part) => [$part->id => $part->name ?? '']))
                            ->required()
                            ->reactive()
                            ->searchable(),
                        Select::make('equipment_id')
                            ->label('Equipment')
                            ->options(Equipment::all()
                                ->mapWithKeys(fn ($equipment) => [$equipment->id => $equipment->type ?? '']))
                            ->required()
                            ->reactive()
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('type')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('model')
                                    ->maxLength(255),
                                TextInput::make('serial_number')
                                    ->maxLength(255),
                                DatePicker::make('installation_date'),
                                DatePicker::make('last_maintenance_date'),
                                Select::make('status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                        'maintenance' => 'Under Maintenance',
                                    ])
                                    ->required(),
                            ])
                            ->createOptionUsing(function (array $data) {
                                return Equipment::create($data)->getKey();
                            }),
                        TextInput::make('watt')
                            ->label('Watt (Usage Value)')
                            ->numeric()
                            ->required()
                            ->suffix('W'),
                        Select::make('period_number')
                            ->label('Time Period (15-min interval)')
                            ->options(array_combine(range(1, 96), range(1, 96)))
                            ->required(),
                    ])
                    ->defaultItems(1)
                    ->columns(3)
                    ->required(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('Log Usage')
                ->action('create'),
        ];
    }

    public function create(): void
    {
        try {
            $data = $this->form->getState();

            ConsumerUsage::create([
                'property_id' => $data['property_id'],
                'date' => $data['date'],
                'usage_data' => $data['usage_data'], // usage_data is already an array of objects
            ]);

            $this->form->fill();
            
            
            
            
        } catch (\Exception $e) {
            
        }
    }
}