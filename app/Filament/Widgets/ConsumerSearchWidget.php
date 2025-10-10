<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasRoleVisibility;
use Filament\Widgets\Widget;
use App\Models\Consumer;
use App\Models\PropertyPart;
use Illuminate\Support\Collection;

class ConsumerSearchWidget extends Widget
{
    use HasRoleVisibility;

    protected static string $view = 'filament.widgets.consumer-search-widget';
    
    protected int|string|array $columnSpan = 'full';
    
    protected static ?int $sort = 1;
    
    public string $search = '';
    
    public ?int $selectedConsumerId = null;
    
    public Collection $searchResults;
    
    public bool $showResults = false;
    
    public function mount(): void
    {
        $this->searchResults = collect();
    }
    
    public function updatedSearch(): void
    {
        if (strlen($this->search) >= 2) {
            $this->searchConsumers();
            $this->showResults = true;
        } else {
            $this->searchResults = collect();
            $this->showResults = false;
        }
    }
    
    public function searchConsumers(): void
    {
        $query = Consumer::with(['properties'])
            ->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('address', 'like', '%' . $this->search . '%')
                  ->orWhere('nic', 'like', '%' . $this->search . '%');
            })
            ->orWhereHas('properties', function ($propertyQuery) {
                $propertyQuery->where('account_no', 'like', '%' . $this->search . '%')
                    ->orWhere('address', 'like', '%' . $this->search . '%');
            });
        
        $this->searchResults = $query->limit(10)->get()->map(function ($consumer) {
            $propertyInfo = $consumer->properties->map(function ($property) {
                return $property->account_no . ' - ' . $property->address;
            })->join(' | ');
            
            return [
                'id' => $consumer->id,
                'name' => $consumer->name,
                'address' => $consumer->address,
                'nic' => $consumer->nic,
                'property_info' => $propertyInfo,
                'display_text' => $consumer->name . ' - ' . $consumer->address . ($propertyInfo ? ' | ' . $propertyInfo : ''),
            ];
        });
    }
    
    public function selectConsumer(int $consumerId): void
    {
        $this->selectedConsumerId = $consumerId;
        $selectedConsumer = $this->searchResults->firstWhere('id', $consumerId);
        
        if ($selectedConsumer) {
            $this->search = $selectedConsumer['name'];
        }
        
        $this->showResults = false;
        
        // Emit event to update all chart widgets
        $this->dispatch('consumer-selected', $consumerId);
    }
    
    public function clearSearch(): void
    {
        $this->search = '';
        $this->selectedConsumerId = null;
        $this->searchResults = collect();
        $this->showResults = false;
        
        // Emit event to clear all chart widgets
        $this->dispatch('consumer-selected', null);
    }
    
    public function hideResults(): void
    {
        // Small delay to allow click events to process
        $this->showResults = false;
    }

    public static function canView(): bool
    {
        return static::canViewWidgetByRole();
    }
}
