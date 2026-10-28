<nav class="app-header navbar navbar-expand bg-body" aria-label="{{ __('Admin toolbar') }}">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link"
                   data-lte-toggle="sidebar"
                   href="#"
                   role="button"
                   aria-label="{{ __('Toggle sidebar') }}">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </a>
            </li>
        </ul>

        <div class="admin-navbar__actions ms-auto">
            <form method="GET"
                  action="{{ route('admin.search.index') }}"
                  class="admin-global-search"
                  role="search">
                <label class="visually-hidden" for="admin-global-search">
                    {{ __('Cari layanan, FAQ, dan pengajuan') }}
                </label>
                <div class="input-group">
                    <input id="admin-global-search"
                           class="form-control"
                           type="search"
                           name="q"
                           value="{{ request()->routeIs('admin.search.*') ? request('q') : '' }}"
                           placeholder="{{ __('Cari layanan, FAQ, atau pengajuan...') }}"
                           autocomplete="off">
                    <button class="btn btn-outline-secondary"
                            type="submit"
                            aria-label="{{ __('Cari') }}">
                        <i class="bi bi-search" aria-hidden="true"></i>
                    </button>
                </div>
            </form>

            @if (config('adminlte.color_mode_toggle', true))
                <ul class="navbar-nav">
                    @include('adminlte::partials.color-mode')
                </ul>
            @endif
        </div>
    </div>
</nav>
