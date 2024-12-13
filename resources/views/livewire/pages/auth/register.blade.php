<?php

use App\Models\User;
use App\Models\VaccineCenter;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {

    #[Validate]
    public string $name = '';
    #[Validate]
    public string $email = '';
    #[Validate]
    public string $phone = '';
    #[Validate]
    public int $nid;
    #[Validate]
    public int $vaccineCenter;
    public string $password = '';
    public string $password_confirmation = '';

    public array $vaccineCenters;

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone' => ['required', 'string'],
            'nid' => ['required', 'numeric', 'digits:10', 'unique:' . User::class],
            'vaccineCenter' => ['required', 'numeric', 'exists:vaccine_centers,id'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ];
    }

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->vaccineCenters = VaccineCenter::pluck('name', 'id')->toArray();
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate();

        $validated['password'] = Hash::make($validated['password']);
        $user = User::make($validated);
        $user->vaccineCenter()->associate($validated['vaccineCenter']);
        event(new Registered($user->save()));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <form wire:submit="register">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" wire:model.blur="name" class="block w-full mt-1" type="text" name="name" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" wire:model.blur="email" class="block w-full mt-1" type="email" name="email"
                required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Phone -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Phone')" />
            <x-text-input id="phone" wire:model="phone" class="block w-full mt-1" type="tel" name="phone" required
                autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Nid -->
        <div class="mt-4">
            <x-input-label for="nid" :value="__('NID')" />
            <x-text-input id="nid" wire:model="nid" class="block w-full mt-1" type="number" name="nid" required />
            <x-input-error :messages="$errors->get('nid')" class="mt-2" />
        </div>

        <!-- Vaccine Center -->
        <div class="mt-4">
            <x-input-label for="vaccineCenter" :value="__('Choose a vaccine Center')" />
            <select id="vaccineCenter" wire:model='vaccineCenter'
                class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                <option selected>Choose a vaccine Center</option>
                @foreach ($this->vaccineCenters as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('vaccineCenter')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" wire:model="password" class="block w-full mt-1" type="password" name="password"
                required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" wire:model="password_confirmation" class="block w-full mt-1"
                type="password" name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="text-sm text-gray-600 underline rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</div>
