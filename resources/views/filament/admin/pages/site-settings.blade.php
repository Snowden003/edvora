<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Company Info --}}
        <form wire:submit="saveCompany">
            {{ $this->companyForm }}

            <div class="mt-4">
                <x-filament::button type="submit">
                    Save Company Info
                </x-filament::button>
            </div>
        </form>

        {{-- Social Links --}}
        <form wire:submit="saveSocial">
            {{ $this->socialForm }}

            <div class="mt-4">
                <x-filament::button type="submit">
                    Save Social Links
                </x-filament::button>
            </div>
        </form>

        {{-- SEO --}}
        <form wire:submit="saveSeo">
            {{ $this->seoForm }}

            <div class="mt-4">
                <x-filament::button type="submit">
                    Save SEO Settings
                </x-filament::button>
            </div>
        </form>

        {{-- Footer --}}
        <form wire:submit="saveFooter">
            {{ $this->footerForm }}

            <div class="mt-4">
                <x-filament::button type="submit">
                    Save Footer Settings
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
