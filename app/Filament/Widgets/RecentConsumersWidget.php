<?php

namespace App\Filament\Widgets;

use App\Models\Consumer;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Actions\Action;
use Filament\Support\Facades\FilamentView;
use Livewire\Component;

class RecentConsumersWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1; // Display at the top


    public function table(Table $table): Table
    {
        return $table
            ->query(
                Consumer::query()
                    ->with('properties')
                    ->latest()
                    ->limit(20)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->width('80px'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Consumer Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->wrap(),

                Tables\Columns\TextColumn::make('address')
                    ->label('Address')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(50),

                Tables\Columns\TextColumn::make('nic')
                    ->label('NIC')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('NIC copied to clipboard'),

                Tables\Columns\TextColumn::make('account_numbers')
                    ->label('Account Numbers')
                    ->getStateUsing(function (Consumer $record): string {
                        $properties = $record->properties;
                        if ($properties->isEmpty()) {
                            return 'No properties';
                        }
                        return $properties->pluck('account_no')->join(', ');
                    })
                    ->searchable(query: function ($query, $search) {
                        return $query->orWhereHas('properties', function ($propertyQuery) use ($search) {
                            $propertyQuery->where('account_no', 'like', "%{$search}%");
                        });
                    })
                    ->wrap()
                    ->tooltip(function (Consumer $record): string {
                        $properties = $record->properties;
                        if ($properties->isEmpty()) {
                            return 'This consumer has no properties assigned';
                        }
                        return 'Properties: ' . $properties->map(function ($property) {
                            return $property->account_no . ' (' . $property->address . ')';
                        })->join(', ');
                    }),
            ])
            ->actions([
                Action::make('filter_charts')
                    ->label('Filter Charts')
                    ->icon('heroicon-o-funnel')
                    ->color('success')
                    ->action(function (Consumer $record) {
                        // Dispatch event to update chart widgets
                        $this->dispatch('consumer-selected', consumerId: $record->id);
                    })
                    ->tooltip('Filter dashboard charts for this consumer'),
            ])
            ->defaultSort('created_at', 'desc')
            ->heading('Recently Registered Consumers')
            ->description('Latest 20 registered consumers with search functionality')
            ->searchable()
            ->searchPlaceholder('Search by name, ID, address, NIC, or account number...')
            ->striped()
            ->paginated([10, 20, 50])
            ->defaultPaginationPageOption(10)
            ->poll('30s') // Auto-refresh every 30 seconds
            ->emptyStateHeading('No consumers found')
            ->emptyStateDescription('No consumers have been registered yet.')
            ->emptyStateIcon('heroicon-o-users');
    }
}
