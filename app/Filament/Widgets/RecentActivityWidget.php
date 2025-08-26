<?php

namespace App\Filament\Widgets;

use App\Models\ConsumerUsage;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentActivityWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ConsumerUsage::query()
                    ->with(['property.consumer'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('property.consumer.name')
                    ->label('Consumer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('property.account_no')
                    ->label('Account No')
                    ->searchable(),

                Tables\Columns\TextColumn::make('date')
                    ->label('Usage Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_kva')
                    ->label('Total KVA')
                    ->numeric(2)
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_equipment_count')
                    ->label('Equipment Count')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Recorded')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->defaultSort('created_at', 'desc')
            ->heading('Recent Usage Activity')
            ->description('Latest 10 consumer usage records');
    }
}
