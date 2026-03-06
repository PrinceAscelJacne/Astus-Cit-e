<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <img src="assets/astuslog.jpg" alt="" style="width: 150px">
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Avant de continuer, pouvez vous vérifier votre adresse email en cliquant sur le lien que nous venons de vous envoyer par email ? Si vous nje l\'avez pas recu, nous serons ravi de vous envoyer un autre.') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ __('Un nouveau lien de vérification d\'email a été à l\'adress que vous avez renseigné dans les parametres de votre profil.') }}
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <x-button type="submit">
                        {{ __('Renvoyer l\'email de verification') }}
                    </x-button>
                </div>
            </form>

            <div>
                <a
                    href="{{ route('profile.show') }}"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    {{ __('Modifier votre profil') }}</a>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf

                    <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ms-2">
                        {{ __('Deconnexion') }}
                    </button>
                </form>
            </div>
        </div>
    </x-authentication-card>
</x-guest-layout>
