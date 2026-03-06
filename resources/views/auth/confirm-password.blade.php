<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <img src="assets/astuslog.jpg" alt="" style="width: 150px">
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Ceci est une partie très sécurisée de l\'application. Vueillez bien confirmer votre mot de passe avant de continuer.') }}
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div>
                <x-label for="password" value="{{ __('Mot de passe') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" autofocus />
            </div>

            <div class="flex justify-end mt-4">
                <x-button class="boss ms-4">
                    {{ __('Confirmer') }}
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
