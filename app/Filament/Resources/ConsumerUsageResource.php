<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsumerUsageResource\Pages;
use App\Models\Consumer;
use App\Models\ConsumerUsage;
use App\Models\Equipment;
use App\Models\Property;
use App\Models\PropertyPart;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ConsumerUsageResource extends Resource
{
    protected static ?string $model = ConsumerUsage::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Usage';

    protected static ?string $navigationLabel = 'Consumer Usage';

    protected static ?string $pluralModelLabel = 'Consumer Usage Records';

    public static function canViewAny(): bool
    {
        return true; // Auth::user()->can('view consumer usages');
    }

    public static function canCreate(): bool
    {
        return true; // Auth::user()->can('create consumer usages');
    }

    public static function canEdit($record): bool
    {
        return true; // Auth::user()->can('edit consumer usages');
    }

    public static function canDelete($record): bool
    {
        return true; // Auth::user()->can('delete consumer usages');
    }

    public static function canDeleteAny(): bool
    {
        return true; // Auth::user()->can('delete consumer usages');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Usage Information')
                    ->schema([
                        // Property Account Number Selection
                        Select::make('property_id')
                            ->label('Property Account Number')
                            ->options(Property::all()
                                ->mapWithKeys(fn ($property) => [
                                    $property->id => $property->account_no.' - '.$property->address,
                                ]))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('Select or type property account number'),

                        // Date input with today's date as default
                        DatePicker::make('date')
                            ->label('Date')
                            ->required()
                            ->default(now()->format('Y-m-d'))
                            ->native(false),

                        // Property Parts with Equipment Usage Details - Multiple property parts
                        Repeater::make('usage_data')
                            ->label('Property Part Usage Details')
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        // Property Part Selection for each entry
                                        Select::make('property_part')
                                            ->label('Property Part')
                                            ->options(PropertyPart::all()->pluck('name', 'name'))
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Select property part'),

                                        // Equipment Usage Details for this property part
                                        Repeater::make('equipment_data')
                                            ->label('Equipment Usage')
                                            ->schema([
                                                Select::make('equipment')
                                                    ->label('Equipment')
                                                    ->options(Equipment::all()
                                                        ->mapWithKeys(fn ($equipment) => [
                                                            $equipment->type.' - '.$equipment->brand.' '.$equipment->model => $equipment->type.' - '.$equipment->brand.' '.$equipment->model,
                                                        ]))
                                                    ->required()
                                                    ->searchable()
                                                    ->placeholder('Select equipment')
                                                    ->columnSpan(1),

                                                TextInput::make('watt')
                                                    ->label('W (watt)')
                                                    ->numeric()
                                                    ->required()
                                                    ->suffix('W')
                                                    ->step(0.01)
                                                    ->columnSpan(1),

                                                Forms\Components\Section::make('Time Period Selection')
                                                    ->schema([
                                                        Forms\Components\CheckboxList::make('time_period')
                                                            ->label('Time Period (15-min interval)')
                                                            ->options(collect(range(1, 96))->mapWithKeys(function ($period) {
                                                                $startTime = now()->startOfDay()->addMinutes(($period - 1) * 15);
                                                                $endTime = $startTime->copy()->addMinutes(15);
                                                                $label = $startTime->format('H:i').' - '.$endTime->format('H:i');

                                                                return [$period => $label];
                                                            }))
                                                            ->required()
                                                            ->searchable()
                                                            ->columns(6)
                                                            ->gridDirection('row')
                                                            ->bulkToggleable()
                                                            ->descriptions(collect(range(1, 96))->mapWithKeys(function ($period) {
                                                                return [$period => "Period {$period}"];
                                                            }))
                                                            ->helperText('Select multiple time periods for this equipment'),
                                                    ])
                                                    ->collapsible()
                                                    ->collapsed(true)
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(1)
                                            ->addActionLabel('Add Equipment')
                                            ->reorderableWithButtons()
                                            ->collapsible()
                                            ->cloneable(),
                                    ])
                                    ->heading(fn ($get) => 'Property Part: '.($get('property_part') ?? 'Not Selected'))
                                    ->collapsible()
                                    ->collapsed(false),
                            ])
                            ->defaultItems(1)
                            ->addActionLabel('Add Property Part')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->cloneable(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('property.consumer.name')
                    ->label('Consumer Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('property.account_no')
                    ->label('Property Account No')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('usage_data')
                    ->label('Equipment Count')
                    ->formatStateUsing(fn ($record): string => $record->total_equipment_count.' equipment(s)')
                    ->badge(),
                Tables\Columns\TextColumn::make('total_watt')
                    ->label('Total watt')
                    ->formatStateUsing(fn ($record): string => number_format($record->total_watt, 2).' W')
                    ->sortable(false),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('property_id')
                    ->label('Property')
                    ->options(Property::all()
                        ->mapWithKeys(fn ($property) => [
                            $property->id => $property->account_no.' - '.$property->address,
                        ]))
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Date From'),
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Date Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
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
                ]),
            ])->paginated([10, 25, 50])
            ->defaultSort('date', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Property Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('property.account_no')
                            ->label('Account Number'),
                        Infolists\Components\TextEntry::make('property.address')
                            ->label('Address'),
                        Infolists\Components\TextEntry::make('date')
                            ->label('Date')
                            ->date(),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Equipment Usage Details')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('usage_data')
                            ->schema([
                                // First row: Property Part, Equipment, and Wattage
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('property_part')
                                        ->label('Property Part')
                                        ->badge()
                                        ->color('info'),
                                    Infolists\Components\TextEntry::make('equipment')
                                        ->label('Equipment'),
                                    Infolists\Components\TextEntry::make('watt')
                                        ->label('W (watt)')
                                        ->formatStateUsing(fn ($state) => number_format($state, 2).' W')
                                        ->badge()
                                        ->color('success'),
                                ])
                                ->columns(3),
                                
                                // Second row: Time Periods with better column distribution
                                Infolists\Components\Section::make('Time Periods')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('time_period')
                                            ->label('Active Time Periods')
                                            ->formatStateUsing(function ($state) {
                                                if (is_array($state)) {
                                                    return collect($state)->map(function ($period) {
                                                        $startTime = now()->startOfDay()->addMinutes(($period - 1) * 15);
                                                        $endTime = $startTime->copy()->addMinutes(15);
                                                        return "Period {$period} ({$startTime->format('H:i')} - {$endTime->format('H:i')})";
                                                    })->chunk(6)->map(function ($chunk) {
                                                        return $chunk->join(' • ');
                                                    })->join('<br>');
                                                } else {
                                                    $startTime = now()->startOfDay()->addMinutes(($state - 1) * 15);
                                                    $endTime = $startTime->copy()->addMinutes(15);
                                                    return "Period {$state} ({$startTime->format('H:i')} - {$endTime->format('H:i')})";
                                                }
                                            })
                                            ->html()
                                            ->badge()
                                            ->color('warning'),
                                    ])
                                    ->compact()
                                    ->columnSpanFull(),
                            ])
                            ->label('Equipment Usage Records'),
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
            'index' => Pages\ListConsumerUsages::route('/'),
            'create' => Pages\CreateConsumerUsage::route('/create'),
            'view' => Pages\ViewConsumerUsage::route('/{record}'),
            'edit' => Pages\EditConsumerUsage::route('/{record}/edit'),
        ];
    }
}
