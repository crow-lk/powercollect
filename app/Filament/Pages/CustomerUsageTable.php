<?php

namespace App\Filament\Pages;

use App\Models\ConsumerUsage;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;

class CustomerUsageTable extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationLabel = 'Usage Table';
    protected static ?string $navigationGroup = 'Usage';
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.consumer-usage-table';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('consumer.name')
                    ->label('Consumer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('equipment.type')
                    ->label('Equipment')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kVA')
                    ->label('kVA')
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('start_time')
                    ->label('Start Time')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('end_time')
                    ->label('End Time')
                    ->time('H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('consumer_id')
                    ->label('Consumer')
                    ->options(\App\Models\Consumer::pluck('name', 'id')),
                SelectFilter::make('equipment_id')
                    ->label('Equipment')
                    ->options(\App\Models\Equipment::pluck('type', 'id')),
                Filter::make('date')
                    ->form([
                        DatePicker::make('date_from'),
                        DatePicker::make('date_until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn ($query, $date) => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn ($query, $date) => $query->whereDate('date', '<=', $date),
                            );
                    }),
            ])
            ->defaultSort('date', 'desc');
    }

    protected function getTableQuery()
    {
        return ConsumerUsage::query();
    }
}
