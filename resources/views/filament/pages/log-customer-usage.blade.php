<x-filament-panels::page>
    
    {{-- {{ $this->form }}

    <x-filament::button wire:click="saveRecord" color="primary">
        Save Record
    </x-filament::button> --}}


    <form wire:submit.prevent="submit">
        {{ $this->form }}

        <x-filament::button type="submit" color="primary" class="w-full mt-4">
            Save Usage
        </x-filament::button>
    </form>


</x-filament-panels::page>
