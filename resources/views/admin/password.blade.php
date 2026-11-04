<x-layouts.admin :title="__('Mon compte')">
    <h1 class="text-4xl">{{ __('Mon compte') }}</h1>
    <form method="post" action="{{ route('admin.account.update') }}" class="card mt-5 max-w-md space-y-4 p-5">
        @csrf @method('put')
        <div>
            <label for="email" class="field-label">{{ __('Identifiant (e-mail)') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email', auth()->user()->email) }}" required autocomplete="username" class="field">
        </div>
        <div>
            <label for="password" class="field-label">{{ __('Nouveau mot de passe') }}</label>
            <input id="password" name="password" type="password" autocomplete="new-password" class="field">
            <p class="field-help">{{ __('10 caractères minimum. Laissez vide pour ne pas le changer.') }}</p>
        </div>
        <div>
            <label for="password_confirmation" class="field-label">{{ __('Confirmer le nouveau mot de passe') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="field">
        </div>
        <div class="border-t border-ligne pt-4">
            <label for="current_password" class="field-label">{{ __('Mot de passe actuel') }}</label>
            <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="field">
        </div>
        <button class="a-btn-primary">{{ __('Mettre à jour') }}</button>
    </form>
</x-layouts.admin>
