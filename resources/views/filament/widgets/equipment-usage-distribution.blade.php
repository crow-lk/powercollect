<x-filament-widgets::widget class="fi-wi-chart">
    <x-filament::section>
        <x-slot name="heading">
            {{ $widget::$heading }}
        </x-slot>

        <div class="overflow-x-auto">
            <div style="min-width: 2400px; height: 400px;">
                <canvas
                    x-data="{
                        chart: null,
                        init() {
                            this.chart = new Chart(this.$el, @js($widget->getCachedData()));
                            
                            // Add resize listener
                            window.addEventListener('resize', () => {
                                if (this.chart) {
                                    this.chart.resize();
                                }
                            });
                        },
                        destroy() {
                            if (this.chart) {
                                this.chart.destroy();
                                this.chart = null;
                            }
                        }
                    }"
                    x-init="init()"
                    x-destroy="destroy()"
                ></canvas>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

<style>
    .overflow-x-auto {
        overflow-x: auto;
        overflow-y: hidden;
    }
    
    .overflow-x-auto::-webkit-scrollbar {
        height: 8px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>