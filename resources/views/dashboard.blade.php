<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Vaccine Status') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (auth()->user()->status === $VaccineStatus::NOT_SCHEDULED)
                    <x-status-info status="{{ $VaccineStatus::NOT_SCHEDULED->name }}"></x-status-info>
                    @elseif (auth()->user()->status === $VaccineStatus::SCHEDULED)
                    <x-status-info status="{{ $VaccineStatus::SCHEDULED->name }}" color='bg-red-500 text-white'
                        message="Your appointment has been fixed for">
                    </x-status-info>
                    @elseif (auth()->user()->status === $VaccineStatus::VACCINATED)
                    <x-status-info status="{{ $VaccineStatus::VACCINATED->name }}" message="You were vaccinated on"
                        color='bg-green-600 text-white'>
                    </x-status-info>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
