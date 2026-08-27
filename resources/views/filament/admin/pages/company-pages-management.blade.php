<x-filament-panels::page>
    <div x-data="{ activeTab: 'about' }" class="space-y-6">
        
        {{-- Navigation Tabs --}}
        <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700 pb-2">
            <button 
                type="button"
                @click="activeTab = 'about'"
                :class="activeTab === 'about' ? 'bg-primary-600 text-white font-bold shadow-md' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                class="px-4 py-2 rounded-lg text-sm transition-all duration-200 flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-information-circle" class="w-5 h-5" />
                <span>About Us (/about)</span>
            </button>

            <button 
                type="button"
                @click="activeTab = 'story'"
                :class="activeTab === 'story' ? 'bg-primary-600 text-white font-bold shadow-md' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                class="px-4 py-2 rounded-lg text-sm transition-all duration-200 flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-book-open" class="w-5 h-5" />
                <span>Our Story (/story)</span>
            </button>

            <button 
                type="button"
                @click="activeTab = 'how_we_work'"
                :class="activeTab === 'how_we_work' ? 'bg-primary-600 text-white font-bold shadow-md' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                class="px-4 py-2 rounded-lg text-sm transition-all duration-200 flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-cog-6-tooth" class="w-5 h-5" />
                <span>How We Work (/how-we-work)</span>
            </button>

            <button 
                type="button"
                @click="activeTab = 'terms'"
                :class="activeTab === 'terms' ? 'bg-primary-600 text-white font-bold shadow-md' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                class="px-4 py-2 rounded-lg text-sm transition-all duration-200 flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-shield-check" class="w-5 h-5" />
                <span>Terms of Service (/terms)</span>
            </button>

            <button 
                type="button"
                @click="activeTab = 'privacy'"
                :class="activeTab === 'privacy' ? 'bg-primary-600 text-white font-bold shadow-md' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                class="px-4 py-2 rounded-lg text-sm transition-all duration-200 flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-lock-closed" class="w-5 h-5" />
                <span>Privacy Policy (/privacy)</span>
            </button>
        </div>

        {{-- TAB 1: About Us --}}
        <div x-show="activeTab === 'about'" x-transition class="space-y-6">
            <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">About Us Page Content</h3>
                    <p class="text-xs text-gray-500">Edit hero, mission, vision, timeline milestones, team members, and CTA.</p>
                </div>
                <a href="{{ route('about') }}" target="_blank" class="inline-flex items-center gap-1 text-sm font-semibold text-primary-600 hover:underline">
                    <span>View Live Page</span>
                    <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="w-4 h-4" />
                </a>
            </div>

            <form wire:submit="saveAbout">
                {{ $this->aboutForm }}

                <div class="mt-6 flex items-center justify-end">
                    <x-filament::button type="submit" size="lg">
                        <x-filament::icon icon="heroicon-o-check" class="w-5 h-5 mr-1 inline" />
                        Save About Us Page
                    </x-filament::button>
                </div>
            </form>
        </div>

        {{-- TAB 2: Our Story --}}
        <div x-show="activeTab === 'story'" x-transition class="space-y-6">
            <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Our Story Page Content</h3>
                    <p class="text-xs text-gray-500">Edit origin story, vision, team narrative, and current mission stats.</p>
                </div>
                <a href="{{ route('story') }}" target="_blank" class="inline-flex items-center gap-1 text-sm font-semibold text-primary-600 hover:underline">
                    <span>View Live Page</span>
                    <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="w-4 h-4" />
                </a>
            </div>

            <form wire:submit="saveStory">
                {{ $this->storyForm }}

                <div class="mt-6 flex items-center justify-end">
                    <x-filament::button type="submit" size="lg">
                        <x-filament::icon icon="heroicon-o-check" class="w-5 h-5 mr-1 inline" />
                        Save Our Story Page
                    </x-filament::button>
                </div>
            </form>
        </div>

        {{-- TAB 3: How We Work --}}
        <div x-show="activeTab === 'how_we_work'" x-transition class="space-y-6">
            <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">How We Work Page Content</h3>
                    <p class="text-xs text-gray-500">Edit non-profit mission, core values, operational pillars, bullet points, and CTA.</p>
                </div>
                <a href="{{ route('how-we-work') }}" target="_blank" class="inline-flex items-center gap-1 text-sm font-semibold text-primary-600 hover:underline">
                    <span>View Live Page</span>
                    <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="w-4 h-4" />
                </a>
            </div>

            <form wire:submit="saveHowWeWork">
                {{ $this->howWeWorkForm }}

                <div class="mt-6 flex items-center justify-end">
                    <x-filament::button type="submit" size="lg">
                        <x-filament::icon icon="heroicon-o-check" class="w-5 h-5 mr-1 inline" />
                        Save How We Work Page
                    </x-filament::button>
                </div>
            </form>
        </div>

        {{-- TAB 4: Terms of Service --}}
        <div x-show="activeTab === 'terms'" x-transition class="space-y-6">
            <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Terms of Service Page Content</h3>
                    <p class="text-xs text-gray-500">Edit legal bento grid cards, TL;DR summaries, versioning, and support contact.</p>
                </div>
                <a href="{{ route('terms') }}" target="_blank" class="inline-flex items-center gap-1 text-sm font-semibold text-primary-600 hover:underline">
                    <span>View Live Page</span>
                    <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="w-4 h-4" />
                </a>
            </div>

            <form wire:submit="saveTerms">
                {{ $this->termsForm }}

                <div class="mt-6 flex items-center justify-end">
                    <x-filament::button type="submit" size="lg">
                        <x-filament::icon icon="heroicon-o-check" class="w-5 h-5 mr-1 inline" />
                        Save Terms Page
                    </x-filament::button>
                </div>
            </form>
        </div>

        {{-- TAB 5: Privacy Policy --}}
        <div x-show="activeTab === 'privacy'" x-transition class="space-y-6">
            <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Privacy Policy Page Content</h3>
                    <p class="text-xs text-gray-500">Edit privacy panes, encryption highlights, GDPR badges, and sub-notes.</p>
                </div>
                <a href="{{ route('privacy') }}" target="_blank" class="inline-flex items-center gap-1 text-sm font-semibold text-primary-600 hover:underline">
                    <span>View Live Page</span>
                    <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="w-4 h-4" />
                </a>
            </div>

            <form wire:submit="savePrivacy">
                {{ $this->privacyForm }}

                <div class="mt-6 flex items-center justify-end">
                    <x-filament::button type="submit" size="lg">
                        <x-filament::icon icon="heroicon-o-check" class="w-5 h-5 mr-1 inline" />
                        Save Privacy Policy Page
                    </x-filament::button>
                </div>
            </form>
        </div>

    </div>
</x-filament-panels::page>
