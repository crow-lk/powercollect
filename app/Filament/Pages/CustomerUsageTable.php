<?php

namespace App\Filament\Pages;

use App\Models\CustomerUsage;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;

class CustomerUsageTable extends Page implements HasTable
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.customer-usage-table';

    protected static ?string $navigationGroup = 'Usage';

    use InteractsWithTable;


    //write the table querry
    protected function getTableQuery()
    {
        return CustomerUsage::query();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('customer.name')
                ->label('Customer')
                ->sortable()
                ->searchable(),
            TextColumn::make('equipment.type')
                ->label('Equipment')
                ->sortable()
                ->searchable(),
            TextColumn::make('kVA')
                ->label('kVA')
                ->sortable(),
            TextColumn::make('start_time')
                ->label('Start Time')
                ->sortable(),
            TextColumn::make('end_time')
                ->label('End Time')
                ->sortable(),
            TextColumn::make('date')
                ->label('Date')
                ->sortable(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('customer_id')
                ->label('Customer')
                ->options(\App\Models\Customer::pluck('name', 'id')),
            SelectFilter::make('equipment_id')
                ->label('Equipment')
                ->options(\App\Models\Equipment::pluck('type', 'id')),
        ];
    }
}
