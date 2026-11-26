@guest('web')
    <div class="modal fade auth-modal" id="loginModal" tabindex="-1" aria-labelledby="loginModalTitle"
         aria-hidden="true" @if (session('auth_modal') === 'login') data-auth-modal-autostart @endif>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <span class="auth-eyebrow">UNIT LAYANAN TERPADU POLINEMA</span>
                        <h2 class="modal-title" id="loginModalTitle">Masuk ke akun Anda</h2>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    @if (session('auth_modal') === 'login' && $errors->any())
                        <div class="auth-alert" role="alert">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('user.login.submit') }}" class="auth-form">
                        @csrf
                        <input type="hidden" name="layanan_id" value="{{ old('layanan_id') }}">

                        <label for="userLoginEmail">Email</label>
                        <input id="userLoginEmail" name="email" type="email" value="{{ old('email') }}"
                               autocomplete="email" maxlength="255" required autofocus>

                        <label for="userLoginPassword">Password</label>
                        <div class="auth-password-field">
                            <input id="userLoginPassword" name="password" type="password"
                                   autocomplete="current-password" required>
                            <button type="button" data-password-toggle="userLoginPassword"
                                    aria-label="Tampilkan password">Tampilkan</button>
                        </div>

                        <button class="auth-submit" type="submit">Masuk</button>
                    </form>

                    <p class="auth-switch">Belum punya akun?
                        <a href="#registerModal" data-auth-modal-switch="register">Daftar sekarang</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade auth-modal" id="registerModal" tabindex="-1" aria-labelledby="registerModalTitle"
         aria-hidden="true" @if (session('auth_modal') === 'register') data-auth-modal-autostart @endif>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <span class="auth-eyebrow">UNIT LAYANAN TERPADU POLINEMA</span>
                        <h2 class="modal-title" id="registerModalTitle">Buat akun ULT</h2>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    @if (session('auth_modal') === 'register' && $errors->any())
                        <div class="auth-alert" role="alert">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('user.register.submit') }}" class="auth-form">
                        @csrf
                        <input type="hidden" name="layanan_id" value="{{ old('layanan_id') }}">

                        <label for="userRegisterName">Nama Lengkap</label>
                        <input id="userRegisterName" name="name" type="text" value="{{ old('name') }}"
                               autocomplete="name" maxlength="150" required autofocus>

                        <label for="userRegisterEmail">Email</label>
                        <input id="userRegisterEmail" name="email" type="email" value="{{ old('email') }}"
                               autocomplete="email" maxlength="255" required>

                        <label for="userRegisterPassword">Password</label>
                        <div class="auth-password-field">
                            <input id="userRegisterPassword" name="password" type="password"
                                   autocomplete="new-password" minlength="8" required>
                            <button type="button" data-password-toggle="userRegisterPassword"
                                    aria-label="Tampilkan password">Tampilkan</button>
                        </div>
                        <small class="auth-hint">Password minimal 8 karakter.</small>

                        <button class="auth-submit" type="submit">Daftar</button>
                    </form>

                    <p class="auth-switch">Sudah punya akun?
                        <a href="#loginModal" data-auth-modal-switch="login">Masuk sekarang</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endguest

@auth('web')
    <div class="modal fade auth-modal" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="logoutModalTitle">Konfirmasi Logout</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="auth-logout-message">Apakah Anda yakin ingin keluar dari akun Anda?</p>
                    <div class="auth-logout-actions">
                        <button type="button" class="auth-cancel" data-bs-dismiss="modal">Batal</button>
                        <form action="{{ route('user.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="auth-submit">Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endauth
