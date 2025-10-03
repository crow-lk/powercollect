<x-filament-widgets::widget>
    <x-filament::section>
        <div class="relative">
            <!-- Search Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Consumer Search
                </h3>
                @if($selectedConsumerId)
                    <button 
                        wire:click="clearSearch"
                        class="text-sm text-red-600 transition-colors duration-200 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                    >
                        Clear Selection
                    </button>
                @endif
            </div>

            <!-- Search Input -->
            <div class="relative">
                
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    wire:focus="$set('showResults', true)"
                    placeholder="🔍︎ Search consumers by name, address, NIC, or property account number..."
                    class="block w-full py-3 pl-16 pr-4 text-sm border rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-amber-400 dark:focus:border-amber-400"
                />
                @if($search && !$selectedConsumerId)
                    <button 
                        wire:click="clearSearch"
                        class="absolute inset-y-0 right-0 flex items-center pr-3"
                    >
                        <svg class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                @endif
            </div>

            <!-- Search Results -->
            @if($showResults && $searchResults->count() > 0)
                <div class="absolute z-50 w-full mt-1 overflow-y-auto bg-white border border-gray-300 rounded-lg shadow-lg dark:bg-gray-800 dark:border-gray-600 max-h-64">
                    @foreach($searchResults as $result)
                        <button 
                            wire:click="selectConsumer({{ $result['id'] }})"
                            class="w-full px-4 py-3 text-left transition-colors duration-150 border-b border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 dark:border-gray-600 last:border-b-0"
                        >
                            <div class="font-medium text-gray-900 dark:text-white">
                                {{ $result['name'] }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $result['address'] }} | NIC: {{ $result['nic'] }}
                            </div>
                            @if($result['property_info'])
                                <div class="mt-1 text-xs text-amber-600 dark:text-amber-400">
                                    Properties: {{ $result['property_info'] }}
                                </div>
                            @endif
                        </button>
                    @endforeach
                </div>
            @elseif($showResults && $search && $searchResults->count() === 0)
                <div class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg dark:bg-gray-800 dark:border-gray-600">
                    <div class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                        No consumers found matching "{{ $search }}"
                    </div>
                </div>
            @endif

            <!-- Selected Consumer Display -->
            @if($selectedConsumerId)
                @php
                    $selectedConsumer = \App\Models\Consumer::with(['properties'])->find($selectedConsumerId);
                @endphp
                @if($selectedConsumer)
                    <div class="p-4 mt-4 border rounded-lg bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-semibold text-amber-800 dark:text-amber-200">
                                    Selected Consumer: {{ $selectedConsumer->name }}
                                </h4>
                                <p class="text-sm text-amber-700 dark:text-amber-300">
                                    {{ $selectedConsumer->address }} | NIC: {{ $selectedConsumer->nic }}
                                </p>
                                @if($selectedConsumer->properties->count() > 0)
                                    <div class="mt-2">
                                        <span class="text-xs font-medium text-amber-700 dark:text-amber-300">Properties:</span>
                                        @foreach($selectedConsumer->properties as $property)
                                            <div class="text-xs text-amber-600 dark:text-amber-400">
                                                {{ $property->account_no }} - {{ $property->address }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </x-filament::section>

    <!-- Click outside to hide results -->
    <script>
        document.addEventListener('click', function(event) {
            const widget = event.target.closest('[wire\\:id]');
            if (!widget || !widget.querySelector('input[wire\\:model\\.live\\.debounce\\.300ms="search"]')) {
                // Clicked outside the search widget
                @this.call('hideResults');
            }
        });
    </script>
</x-filament-widgets::widget>
