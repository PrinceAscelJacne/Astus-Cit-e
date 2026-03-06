<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <img src="assets/astuslog.jpg" alt="" style="width: 150px">
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Avez-vous oublié votre mot de passe ? Pas de soucis. veuillez juste nous laisser votre adresse email et nous vous y enverrons un message avec un lien de réinitialisation de mot de passe qui vous permettra d\'en choisir un nouveau.') }}
        </div>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button class="boss focus:outline-none">
                    {{ __('Réinitialiser mot de passe') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
<style>
    .boss {
        border: none;
    }
    .boss:hover {
        background-color: darkorange;
        color: black;
    }
</style>
