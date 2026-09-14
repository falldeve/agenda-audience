@props([
    'optionsRoute' => 'passkey.login-options',
    'submitRoute' => 'passkey.login',
    'label' => __('Sign in with a passkey'),
    'loadingLabel' => __('Authenticating...'),
    'separator' => __('Or continue with email'),
    // Habillage crème/vert des écrans publics ; les écrans encore sous layout Flux gardent false
    'institutionnel' => false,
])

@assets
@vite('resources/js/passkeys.js')
@endassets

<div
    x-data="{
        supported: false,
        loading: false,
        error: null,
        updateSupport() {
            this.supported = Boolean(window.Passkeys?.isSupported());
        },
        init() {
            this.updateSupport();

            window.addEventListener('passkeys:ready', () => this.updateSupport(), { once: true });
        },
        async verify() {
            this.loading = true;
            this.error = null;
            try {
                const response = await window.Passkeys.verify({
                    routes: {
                        options: '{{ route($optionsRoute) }}',
                        submit: '{{ route($submitRoute) }}',
                    },
                });
                Livewire.navigate(response.redirect || '/dashboard');
            } catch (e) {
                if (e.constructor?.name !== 'UserCancelledError') {
                    this.error = e.message;
                }
            } finally {
                this.loading = false;
            }
        },
    }"
>
    <template x-if="supported">
        <div>
            @if ($institutionnel)
                <button type="button"
                        x-on:click="verify()"
                        x-bind:disabled="loading"
                        style="width:100%;padding:11px 18px;border-radius:999px;border:1px solid rgba(42,26,20,.20);background:#FFFFFF;color:#2A1A14;font-size:14px;font-weight:700;font-family:inherit;cursor:pointer">
                    <span x-show="!loading">{{ $label }}</span>
                    <span x-show="loading" x-cloak>{{ $loadingLabel }}</span>
                </button>

                <p x-show="error" x-text="error" x-cloak
                   style="margin:8px 0 0;text-align:center;font-size:12px;color:#A32A2A"></p>

                <div style="display:flex;align-items:center;gap:10px;margin:22px 0">
                    <span style="flex:1;height:1px;background:rgba(42,26,20,.12)"></span>
                    <span style="font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:#8A766C;font-weight:700">{{ $separator }}</span>
                    <span style="flex:1;height:1px;background:rgba(42,26,20,.12)"></span>
                </div>
            @else
                <div class="grid gap-2">
                    <flux:button
                        variant="outline"
                        icon="finger-print"
                        class="w-full"
                        x-on:click="verify()"
                        x-bind:disabled="loading"
                    >
                        <span x-show="!loading">{{ $label }}</span>
                        <span x-show="loading" x-cloak>{{ $loadingLabel }}</span>
                    </flux:button>
                    <p x-show="error" x-text="error" x-cloak
                       class="text-sm text-center text-red-600 dark:text-red-400"></p>
                </div>

                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-zinc-200 dark:border-zinc-700"></div>
                    </div>
                    <div class="relative flex justify-center text-xs uppercase">
                        <span class="px-2 text-zinc-500 dark:text-zinc-400 bg-white dark:bg-zinc-900">
                            {{ $separator }}
                        </span>
                    </div>
                </div>
            @endif
        </div>
    </template>
</div>
