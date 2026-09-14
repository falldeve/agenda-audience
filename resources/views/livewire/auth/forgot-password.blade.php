<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body style="margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;background:#F4F2ED;color:#2A1A14;font-family:'DM Sans',system-ui,sans-serif">

        <div style="width:100%;max-width:400px">
            <div style="background:#FFFFFF;border:1px solid rgba(42,26,20,.12);border-radius:12px;padding:32px;box-shadow:0 6px 24px rgba(42,26,20,.06)">

                <div style="display:flex;flex-direction:column;align-items:center;text-align:center;margin-bottom:26px">
                    <div style="width:44px;height:44px;border-radius:10px;background:#2D5A27;color:#FFFFFF;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;letter-spacing:.04em;margin-bottom:14px">RS</div>

                    <h1 style="font-family:'Playfair Display',serif;font-size:24px;font-weight:700;margin:0 0 6px;color:#2A1A14">Mot de passe oublié</h1>

                    <div style="font-size:13px;color:#5A463D;line-height:1.5">Indiquez votre adresse électronique : un lien de réinitialisation vous sera envoyé.</div>
                </div>

                @if (session('status'))
                    <div style="margin-bottom:18px;padding:12px 14px;border-radius:8px;border-left:3px solid #2D5A27;background:#EAF0E6;color:#2A1A14;font-size:13px;font-weight:700">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <label style="display:flex;flex-direction:column;gap:6px;margin-bottom:22px">
                        <span style="font-size:13px;font-weight:700;color:#2A1A14">Adresse électronique</span>
                        <input type="email" name="email" value="{{ old('email') }}"
                               required autofocus autocomplete="email"
                               style="width:100%;box-sizing:border-box;padding:11px 13px;border:1px solid rgba(42,26,20,.22);border-radius:8px;font-size:14px;font-family:inherit;color:#2A1A14;background:#FFFFFF">
                        @error('email')
                            <span style="font-size:12px;color:#A32A2A">{{ $message }}</span>
                        @enderror
                    </label>

                    <button type="submit" data-test="email-password-reset-link-button"
                            style="width:100%;padding:12px 18px;border-radius:999px;border:none;background:#2D5A27;color:#FFFFFF;font-size:14px;font-weight:700;font-family:inherit;cursor:pointer">
                        Envoyer le lien de réinitialisation
                    </button>
                </form>

                <div style="text-align:center;margin-top:18px">
                    <a href="{{ route('login') }}"
                       style="font-size:13px;font-weight:700;color:#2D5A27;text-decoration:none">Retour à la connexion</a>
                </div>

            </div>

            <div style="text-align:center;margin-top:18px;font-size:12px;color:#8A766C">
                Accès réservé au personnel du secrétariat
            </div>
        </div>

    </body>
</html>
