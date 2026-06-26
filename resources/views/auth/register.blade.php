<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Register — Aruvi on the Cliff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        :root { --teal:#008C95; --teal-hover:#00737b; --deep:#073b3f; --ink:#0b2224; }
        *, *::before, *::after { box-sizing: border-box; }
        body { margin:0; font-family:'Inter',sans-serif; min-height:100vh; display:flex; }

        .auth-shell { display:flex; width:100%; min-height:100vh; }

        /* ── Left: brand panel ── */
        .auth-left {
            flex: 0 0 42%;
            background: linear-gradient(160deg, var(--deep) 0%, #0a5560 55%, var(--teal) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 2.5rem;
            position: relative;
            overflow: hidden;
        }
        .auth-left::before {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            top: -70px; right: -70px;
            pointer-events: none;
        }
        .auth-left::after {
            content: '';
            position: absolute;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            bottom: -50px; left: -50px;
            pointer-events: none;
        }
        .auth-left-overlay { display: none; }

        .auth-left-content {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .auth-brand-block {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }
        .auth-logo-img {
            height: 48px;
            width: auto;
            object-fit: contain;
            display: block;
        }
        .auth-brand-img {
            width: 100%;
            max-width: 260px;
            height: auto;
            object-fit: contain;
            display: block;
            border-radius: 8px;
        }

        .auth-left-content h2 {
            font-family: 'Playfair Display', serif;
            color: #fff;
            font-size: 1.55rem;
            font-weight: 700;
            margin: 0;
            line-height: 1.3;
        }
        .auth-left-content p {
            color: rgba(255,255,255,0.7);
            font-size: 0.83rem;
            line-height: 1.65;
            margin: 0;
            max-width: 290px;
        }
        .auth-steps {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .auth-steps li {
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            color: rgba(255,255,255,0.8);
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }
        .step-num {
            width: 20px; height: 20px;
            border-radius: 50%;
            background: rgba(255,255,255,0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.68rem;
            font-weight: 700;
            flex-shrink: 0;
            margin-top: 1px;
        }
        .auth-left-footer { color:rgba(255,255,255,0.32); font-size:0.7rem; }

        /* ── Right: form ── */
        .auth-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 2rem;
            background: #fff;
            overflow-y: auto;
        }
        .auth-form-wrap { width:100%; max-width:490px; padding:0.5rem 0; }

        .form-eyebrow {
            color: var(--teal);
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            display: block;
            margin-bottom: 0.35rem;
        }
        .auth-form-wrap h2 {
            font-family: 'Playfair Display', serif;
            color: var(--ink);
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.3rem;
        }
        .auth-subtitle { color:#6b8a8c; font-size:0.87rem; margin-bottom:1.5rem; }

        .fields-grid { display:grid; grid-template-columns:1fr 1fr; gap:0 1rem; }
        .fields-grid .full { grid-column:1/-1; }

        .field-wrap { position:relative; margin-bottom:1.1rem; }
        .field-wrap label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 0.3rem;
        }
        .field-icon {
            position: absolute;
            left: 0.85rem;
            bottom: 0.7rem;
            color: #9ab5b7;
            font-size: 0.88rem;
            pointer-events: none;
        }
        .field-wrap input {
            width: 100%;
            padding: 0.65rem 2.5rem 0.65rem 2.5rem;
            border: 1.5px solid #dde8e9;
            border-radius: 10px;
            font-size: 0.88rem;
            color: var(--ink);
            background: #f8fdfd;
            outline: none;
            transition: all 0.2s;
        }
        .field-wrap input:focus {
            border-color: var(--teal);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(0,140,149,0.1);
        }
        .field-wrap input.is-invalid { border-color:#dc3545; background:#fff8f8; }
        .invalid-feedback { display:block; color:#dc3545; font-size:0.76rem; margin-top:0.22rem; }
        .toggle-pw {
            position: absolute;
            right: 0.8rem;
            bottom: 0.7rem;
            background: none; border: none; padding: 0;
            color: #9ab5b7; cursor: pointer; font-size: 0.88rem;
        }
        .toggle-pw:hover { color: var(--teal); }

        .pw-strength-bar { height:3px; border-radius:2px; margin-top:0.35rem; background:#e0eced; overflow:hidden; }
        .pw-strength-bar-fill { height:100%; border-radius:2px; width:0%; transition:width 0.3s,background 0.3s; }

        .secret-hint {
            background: rgba(0,140,149,0.06);
            border: 1px solid rgba(0,140,149,0.15);
            border-radius: 10px;
            padding: 0.6rem 0.85rem;
            font-size: 0.78rem;
            color: #2a6b70;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .btn-auth {
            width: 100%;
            padding: 0.78rem;
            background: var(--teal);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 0.93rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.25s;
        }
        .btn-auth:hover {
            background: var(--teal-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0,140,149,0.25);
        }
        .auth-switch { text-align:center; margin-top:1.2rem; font-size:0.875rem; color:#6b8a8c; }
        .auth-switch a { color:var(--teal); font-weight:600; text-decoration:none; }
        .auth-switch a:hover { text-decoration:underline; }
        .auth-alert {
            background:#fff2f2; border:1px solid #fcc; border-radius:10px;
            padding:0.75rem 1rem; color:#b91c1c; font-size:0.8rem;
            display:flex; align-items:flex-start; gap:0.5rem; margin-bottom:1.1rem;
        }

        @media (max-width: 767.98px) {
            .auth-left { display:none; }
            .auth-right { padding:2rem 1.25rem; background:#f0fafa; }
            .auth-form-wrap {
                background:#fff; border-radius:18px;
                padding:1.75rem 1.5rem;
                box-shadow:0 8px 30px rgba(7,59,63,0.08);
            }
            .fields-grid { grid-template-columns:1fr; }
        }
    </style>
</head>
<body>
<div class="auth-shell">

    {{-- Left: brand panel --}}
    <div class="auth-left">
        <div class="auth-left-overlay"></div>
        <div class="auth-left-content">

            <div class="auth-brand-block">
                <img src="{{ asset($settings['admin_logo'] ?? 'images/default/logo.ico') }}"
                     alt="Aruvi Logo"
                     class="auth-logo-img">
                <img src="{{ asset($settings['site_brand_image'] ?? 'images/default/brand.png') }}"
                     alt="Aruvi on the Cliff"
                     class="auth-brand-img">
            </div>

            <h2>Create Your<br>Admin Account</h2>
            <p>Join the resort management team. You'll need an admin registration key to complete sign-up.</p>
            <ol class="auth-steps">
                <li><span class="step-num">1</span> Fill in your name and contact details</li>
                <li><span class="step-num">2</span> Set a strong password (min. 8 chars)</li>
                <li><span class="step-num">3</span> Enter the admin registration key</li>
                <li><span class="step-num">4</span> Access the full resort dashboard</li>
            </ol>
            <div class="auth-left-footer">&copy; {{ date('Y') }} Aruvi on the Cliff. All rights reserved.</div>
        </div>
    </div>

    {{-- Right: form --}}
    <div class="auth-right">
        <div class="auth-form-wrap">

            {{-- Logo moved to left panel --}}

            <span class="form-eyebrow"><i class="bi bi-person-plus me-1"></i>Admin Registration</span>
            <h2>Create account</h2>
            <p class="auth-subtitle">All fields required unless marked optional.</p>

            @if($errors->any())
                <div class="auth-alert">
                    <i class="bi bi-exclamation-circle-fill flex-shrink-0 mt-1"></i>
                    <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.register.store') }}">
                @csrf
                <div class="fields-grid">

                    <div class="field-wrap">
                        <label for="name">Full Name</label>
                        <i class="bi bi-person field-icon"></i>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                               placeholder="Your full name"
                               class="{{ $errors->has('name') ? 'is-invalid' : '' }}"
                               required autocomplete="name">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="field-wrap">
                        <label for="phone">Phone <span style="color:#9ab5b7;font-weight:400;">(optional)</span></label>
                        <i class="bi bi-telephone field-icon"></i>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                               placeholder="+91 90000 00000"
                               class="{{ $errors->has('phone') ? 'is-invalid' : '' }}"
                               autocomplete="tel">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="field-wrap full">
                        <label for="email">Email Address</label>
                        <i class="bi bi-envelope field-icon"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               placeholder="admin@aruvi.test"
                               class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                               required autocomplete="email">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="field-wrap">
                        <label for="password">Password</label>
                        <i class="bi bi-lock field-icon"></i>
                        <input type="password" id="password" name="password"
                               placeholder="Min. 8 characters"
                               class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                               required autocomplete="new-password"
                               oninput="checkStrength(this.value)">
                        <button type="button" class="toggle-pw" onclick="togglePw('password',this)"><i class="bi bi-eye"></i></button>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="pw-strength-bar"><div class="pw-strength-bar-fill" id="pwBar"></div></div>
                    </div>

                    <div class="field-wrap">
                        <label for="password_confirmation">Confirm Password</label>
                        <i class="bi bi-lock-fill field-icon"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               placeholder="Repeat password"
                               required autocomplete="new-password">
                        <button type="button" class="toggle-pw" onclick="togglePw('password_confirmation',this)"><i class="bi bi-eye"></i></button>
                    </div>

                    <div class="field-wrap full">
                        <label for="admin_secret">Admin Registration Key</label>
                        <i class="bi bi-key field-icon"></i>
                        <input type="password" id="admin_secret" name="admin_secret"
                               placeholder="Enter the secret registration key"
                               class="{{ $errors->has('admin_secret') ? 'is-invalid' : '' }}"
                               required>
                        <button type="button" class="toggle-pw" onclick="togglePw('admin_secret',this)"><i class="bi bi-eye"></i></button>
                        @error('admin_secret')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="secret-hint">
                    <i class="bi bi-info-circle-fill flex-shrink-0 mt-1" style="color:var(--teal);"></i>
                    The admin registration key is set by the resort system administrator in the server environment configuration.
                </div>

                <button type="submit" class="btn-auth">
                    <i class="bi bi-person-check-fill"></i> Create Admin Account
                </button>
            </form>

            <div class="auth-switch">
                Already have an account? <a href="{{ route('admin.login') }}">Sign in</a>
            </div>
        </div>
    </div>
</div>
<script>
function togglePw(id, btn) {
    const inp = document.getElementById(id), ic = btn.querySelector('i');
    inp.type = inp.type === 'password' ? 'text' : 'password';
    ic.className = inp.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
function checkStrength(v) {
    const bar = document.getElementById('pwBar');
    if (!bar) return;
    let s = 0;
    if (v.length >= 8) s++;
    if (v.length >= 12) s++;
    if (/[A-Z]/.test(v)) s++;
    if (/[0-9]/.test(v)) s++;
    if (/[^A-Za-z0-9]/.test(v)) s++;
    const w = ['0%','20%','40%','65%','85%','100%'];
    const c = ['transparent','#ef4444','#f97316','#eab308','#22c55e','#008C95'];
    bar.style.width = w[s]; bar.style.background = c[s];
}
</script>
</body>
</html>
