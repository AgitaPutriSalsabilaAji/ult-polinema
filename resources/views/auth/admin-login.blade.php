<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | ULT Polinema</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --navy: #123563;
            --navy-dark: #0c2443;
            --text: #172238;
            --muted: #71809a;
            --border: #d7dfeb;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            width: 100%;
            min-height: 100%;
        }

        body {
            min-height: 100vh;
            min-height: 100dvh;
            padding: 0;
            background: #fff;
            color: var(--text);
            font-family: 'Inter', Arial, sans-serif;
        }

        .page-label {
            display: none;
        }

        .login-card {
            width: 100%;
            max-width: none;
            min-height: 100vh;
            min-height: 100dvh;
            margin: 0;
            display: grid;
            grid-template-columns: minmax(0, 42%) minmax(0, 58%);
            background: #fff;
            border: 0;
            box-shadow: none;
            overflow: hidden;
        }

        /* Panel kiri */
        .brand-panel {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding: clamp(28px, 4vw, 64px);
            color: #fff;
            background: linear-gradient(145deg, #153967, #0d274b 78%);
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-mark {
            width: 42px;
            height: 46px;
            border: 1px solid #f4d16b;
            border-radius: 45% 45% 40% 40%;
            color: #ffdb74;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
            flex-shrink: 0;
        }

        .brand-small {
            font-size: 12px;
            line-height: 1.7;
            color: #e1e9f5;
        }

        .brand-name {
            font-size: 15px;
            font-weight: 800;
            letter-spacing: .2px;
        }

        .brand-copy {
            margin-top: clamp(55px, 9vh, 110px);
            max-width: 540px;
        }

        .brand-copy h1 {
            margin-bottom: 18px;
            font-size: clamp(26px, 2.5vw, 38px);
            line-height: 1.3;
            font-weight: 800;
            letter-spacing: -.7px;
        }

        .brand-copy p {
            max-width: 500px;
            color: #b9c9df;
            font-size: 14px;
            line-height: 1.8;
        }

        .brand-footer {
            margin-top: auto;
            padding-top: 22px;
            border-top: 1px solid rgba(255,255,255,.15);
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            color: #aebed4;
            font-size: 11px;
        }

        /* Panel kanan */
        .form-panel {
            min-width: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: #fff;
        }

        .form-content {
            width: 100%;
            max-width: 560px;
            margin: auto;
            padding: 48px clamp(28px, 5vw, 70px);
        }

        .form-heading {
            margin-bottom: 28px;
        }

        .form-heading h2 {
            margin-bottom: 9px;
            font-size: clamp(25px, 2vw, 32px);
            line-height: 1.35;
            font-weight: 800;
            letter-spacing: -.5px;
        }

        .form-heading p {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.7;
        }

        .alert,
        .success {
            margin-bottom: 20px;
            padding: 14px 16px;
            border-radius: 8px;
            font-size: 12px;
            line-height: 1.7;
        }

        .alert {
            border: 1px solid #fecdd3;
            background: #fff1f2;
            color: #be123c;
        }

        .alert-title {
            margin-bottom: 3px;
            font-weight: 700;
        }

        .alert ul {
            margin: 4px 0 0 18px;
        }

        .success {
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
            color: #166534;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 9px;
        }

        .form-group label {
            color: #344158;
            font-size: 13px;
            font-weight: 600;
        }

        .forgot-link {
            color: #2563eb;
            font-size: 12px;
            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .input-wrap {
            position: relative;
        }

        .form-control {
            display: block;
            width: 100%;
            height: 50px;
            padding: 0 15px;
            border: 1px solid var(--border);
            border-radius: 8px;
            outline: none;
            background: #f8fafc;
            color: #26354d;
            font-family: inherit;
            font-size: 14px;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-control:focus {
            border-color: #527db7;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
            background: #fff;
        }

        .password-input {
            padding-right: 48px;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 14px;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #8a9ab0;
            cursor: pointer;
            font-size: 19px;
        }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 2px 0 25px;
            color: #53627a;
            font-size: 12px;
            cursor: pointer;
        }

        .remember-row input {
            width: 15px;
            height: 15px;
            accent-color: var(--navy);
            cursor: pointer;
        }

        .submit-button {
            width: 100%;
            min-height: 50px;
            padding: 12px 18px;
            border: 0;
            border-radius: 8px;
            background: var(--navy-dark);
            color: white;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s, transform .2s;
        }

        .submit-button:hover {
            background: #173d6c;
        }

        .submit-button:active {
            transform: scale(.99);
        }

        .button-arrow {
            margin-left: 8px;
            font-size: 17px;
            vertical-align: -1px;
        }

        .form-footer {
            margin-top: auto;
            padding: 20px 16px;
            border-top: 1px solid #e7ebf0;
            background: #f8fafc;
            color: #718096;
            text-align: center;
            font-size: 11px;
        }

        /* Tampilan tablet dan HP */
        @media (max-width: 760px) {
            .login-card {
                grid-template-columns: 1fr;
                overflow: visible;
            }

            .brand-panel {
                min-height: auto;
                padding: 28px 24px 32px;
            }

            .brand-copy {
                margin-top: 32px;
                margin-bottom: 0;
            }

            .brand-copy h1 {
                font-size: clamp(24px, 5vw, 32px);
            }

            .brand-copy p {
                font-size: 13px;
            }

            .brand-footer {
                display: none;
            }

            .form-panel {
                min-height: auto;
            }

            .form-content {
                width: 100%;
                max-width: 560px;
                padding: 38px 24px 42px;
            }

            .form-heading h2 {
                font-size: 25px;
            }

            .form-footer {
                padding: 16px;
            }
        }

        @media (max-width: 380px) {
            .brand-panel {
                padding: 24px 20px 28px;
            }

            .form-content {
                padding: 32px 20px;
            }

            .form-heading h2 {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>
    <div class="page-label">Login Page</div>

    <main class="login-card">

        <section class="brand-panel">
            <div class="brand-logo">
                <div class="logo-mark" aria-hidden="true">✥</div>

                <div>
                    <div class="brand-small">Politeknik Negeri Malang</div>
                    <div class="brand-name">ULT POLINEMA</div>
                </div>
            </div>

            <div class="brand-copy">
                <h1>Pusat Layanan Terpadu Satu Pintu Civitas Akademika</h1>

                <p>
                    Platform pengelolaan administrasi akademik,
                    kemahasiswaan, keuangan, kehumasan, dan kepegawaian
                    POLINEMA secara transparan, akuntabel, dan terintegrasi.
                </p>
            </div>

            <div class="brand-footer">
                <span>© {{ date('Y') }} ULT POLINEMA</span>
                <span>Helpdesk: ult@polinema.ac.id</span>
            </div>
        </section>

        <section class="form-panel">
            <div class="form-content">

                <div class="form-heading">
                    <h2>Admin Service Management</h2>
                    <p>
                        Masuk dengan kredensial akun Unit Layanan Terpadu
                    </p>
                </div>

                @if (session('success'))
                    <div class="success">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert" role="alert">
                        <div class="alert-title">ⓘ &nbsp;Gagal Masuk</div>
                        <div>
                            {{ $errors->first() }}
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf

                    <div class="form-group">
                        <div class="label-row">
                            <label for="email">Username atau Email Polinema</label>
                        </div>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            placeholder="nama@polinema.ac.id"
                            value="{{ old('email') }}"
                            autocomplete="username"
                            required
                            autofocus
                        >
                    </div>

                    <div class="form-group">
                        <div class="label-row">
                            <label for="password">Kata Sandi</label>
                            <a href="#" class="forgot-link"
                               onclick="return false;"
                               title="Fitur lupa password belum tersedia">
                                Lupa password?
                            </a>
                        </div>

                        <div class="input-wrap">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control password-input"
                                placeholder="Masukkan kata sandi"
                                autocomplete="current-password"
                                required
                            >

                            <button
                                type="button"
                                class="toggle-password"
                                id="togglePassword"
                                aria-label="Tampilkan kata sandi"
                                title="Tampilkan/sembunyikan kata sandi"
                            >◉</button>
                        </div>
                    </div>

                    <label class="remember-row">
                        <input type="checkbox" name="remember" value="1">
                        Ingat perangkat ini
                    </label>

                    <button type="submit" class="submit-button">
                        MASUK SEKARANG
                        <span class="button-arrow">→</span>
                    </button>
                </form>
            </div>

            <footer class="form-footer">
                © {{ date('Y') }} Politeknik Negeri Malang
            </footer>
        </section>

    </main>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function () {
            const isPassword = passwordInput.type === 'password';

            passwordInput.type = isPassword ? 'text' : 'password';
            togglePassword.textContent = isPassword ? '◎' : '◉';
            togglePassword.setAttribute(
                'aria-label',
                isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'
            );
        });
    </script>
</body>
</html>