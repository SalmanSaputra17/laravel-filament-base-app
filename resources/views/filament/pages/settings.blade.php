<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}
        
        <div class="flex gap-3 mt-4 justify-start">
            <x-filament::button type="submit" wire:loading.attr="disabled">
                Save
            </x-filament::button>
            <x-filament::button tag="a" href="/admin" color="gray">
                Cancel
            </x-filament::button>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>
