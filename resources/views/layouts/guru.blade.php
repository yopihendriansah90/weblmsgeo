<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Guru Panel | {{ $title ?? 'Dashboard Guru' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    
    <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      crossorigin="anonymous"
    />
    <!--end::Fonts-->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->

    @vite(['resources/js/guru.js'])

    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="{{ asset('adminlte/css/adminlte.css') }}" />
    <!--end::Required Plugin(AdminLTE)-->

    @livewireStyles
    @stack('styles')
  </head>
  <!--end::Head-->

  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary guru-theme">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      @php
        $routeName = request()->route()?->getName();
        $breadcrumbSection = null;
        $breadcrumbSectionUrl = null;
        $pageTitle = $title ?? 'Dashboard Guru';
        $pageSubtitle = 'Ringkasan aktivitas dan akses cepat fitur utama.';
        $pageContext = 'Dashboard';

        switch (true) {
            case $routeName === 'guru.dashboard':
                $pageTitle = 'Dashboard Guru';
                $pageSubtitle = 'Ringkasan aktivitas dan akses cepat fitur utama.';
                $pageContext = 'Overview';
                break;
            case request()->routeIs('guru.courses.index'):
                $pageTitle = 'Daftar Materi';
                $pageSubtitle = 'Lihat, tambah, dan kelola daftar materi pembelajaran.';
                $breadcrumbSection = 'Materi';
                $breadcrumbSectionUrl = route('guru.courses.index');
                $pageContext = 'Materi';
                break;
            case request()->routeIs('guru.courses.create'):
                $pageTitle = 'Tambah Materi';
                $pageSubtitle = 'Buat materi baru beserta judul, status, dan cover.';
                $breadcrumbSection = 'Materi';
                $breadcrumbSectionUrl = route('guru.courses.index');
                $pageContext = 'Materi';
                break;
            case request()->routeIs('guru.courses.edit'):
                $pageTitle = 'Edit Materi';
                $pageSubtitle = 'Perbarui informasi materi yang sudah dibuat.';
                $breadcrumbSection = 'Materi';
                $breadcrumbSectionUrl = route('guru.courses.index');
                $pageContext = 'Materi';
                break;
            case request()->routeIs('guru.modules.index'):
                $pageTitle = 'Kelola Bab';
                $pageSubtitle = 'Susun urutan bab dan subbab pada materi ini.';
                $breadcrumbSection = 'Materi';
                $breadcrumbSectionUrl = route('guru.courses.index');
                $pageContext = 'Bab';
                break;
            case request()->routeIs('guru.modules.create'):
                $pageTitle = 'Tambah Bab';
                $pageSubtitle = 'Tambahkan bab baru ke dalam materi.';
                $breadcrumbSection = 'Materi';
                $breadcrumbSectionUrl = route('guru.courses.index');
                $pageContext = 'Bab';
                break;
            case request()->routeIs('guru.modules.edit'):
                $pageTitle = 'Edit Bab';
                $pageSubtitle = 'Perbarui detail bab yang sudah ada.';
                $breadcrumbSection = 'Materi';
                $breadcrumbSectionUrl = route('guru.courses.index');
                $pageContext = 'Bab';
                break;
            case request()->routeIs('guru.lessons.create'):
                $pageTitle = 'Tambah Subbab';
                $pageSubtitle = 'Tambahkan subbab baru ke dalam bab.';
                $breadcrumbSection = 'Materi';
                $breadcrumbSectionUrl = route('guru.courses.index');
                $pageContext = 'Subbab';
                break;
            case request()->routeIs('guru.lessons.edit'):
                $pageTitle = 'Edit Subbab';
                $pageSubtitle = 'Perbarui isi subbab yang sudah ada.';
                $breadcrumbSection = 'Materi';
                $breadcrumbSectionUrl = route('guru.courses.index');
                $pageContext = 'Subbab';
                break;
            case request()->routeIs('guru.lessons.preview'):
                $pageTitle = 'Preview Subbab';
                $pageSubtitle = 'Pratinjau tampilan subbab sebelum dipublikasikan.';
                $breadcrumbSection = 'Materi';
                $breadcrumbSectionUrl = route('guru.courses.index');
                $pageContext = 'Subbab';
                break;
            default:
                $pageTitle = $title ?? 'Dashboard Guru';
                $pageContext = 'Dashboard';
                break;
        }
      @endphp
      <!--begin::Header-->
      <nav class="app-header navbar navbar-expand guru-topbar">
        <!--begin::Container-->
        <div class="container-fluid">
          <div class="d-flex align-items-center gap-3 me-auto">
            <a class="nav-link p-0 d-inline-flex align-items-center justify-content-center guru-sidebar-toggle" data-lte-toggle="sidebar" href="#" role="button" aria-label="Toggle sidebar">
              <i class="bi bi-list"></i>
            </a>
            <div class="guru-topbar-context">
              <div class="guru-topbar-kicker">{{ $pageContext }}</div>
            </div>
          </div>

          <ul class="navbar-links ms-auto">
            <!--begin::User Menu Dropdown-->
            <li class="nav-item dropdown user-menu">
              <a href="#" class="nav-link dropdown-toggle guru-user-toggle" data-bs-toggle="dropdown">
                <span class="guru-user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                <span class="d-none d-md-inline guru-user-name">{{ auth()->user()->name }}</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end guru-user-menu">
                <li class="guru-user-card">
                  <div class="guru-user-card-name">{{ auth()->user()->name }}</div>
                  <div class="guru-user-card-role">Guru</div>
                </li>
                <li><hr class="dropdown-divider m-0"></li>
                <li>
                  <form action="{{ route('guru.logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="dropdown-item guru-user-logout">
                      <i class="bi bi-box-arrow-right me-2"></i> Sign out
                    </button>
                  </form>
                </li>
              </ul>
            </li>
            <!--end::User Menu Dropdown-->
          </ul>
        </div>
        <!--end::Container-->
      </nav>
      <!--end::Header-->

      <!--begin::Sidebar-->
      <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <!--begin::Sidebar Brand-->
        <div class="sidebar-brand">
          <!--begin::Brand Link-->
          <a href="{{ route('guru.dashboard') }}" class="brand-link">
            <!--begin::Brand Text-->
            <span class="brand-text fw-light">Guru Panel</span>
            <!--end::Brand Text-->
          </a>
          <!--end::Brand Link-->
        </div>
        <!--end::Sidebar Brand-->

        <!--begin::Sidebar Wrapper-->
        <div class="sidebar-wrapper">
          <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul
              class="nav sidebar-menu flex-column"
              data-lte-toggle="treeview"
              role="menu"
              data-accordion="false"
            >
              <li class="nav-item">
                <a href="{{ route('guru.dashboard') }}" class="nav-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-speedometer"></i>
                  <p>Dashboard</p>
                </a>
              </li>
              <li class="nav-header">MATERI</li>
              <li class="nav-item">
                <a href="{{ route('guru.courses.index') }}" class="nav-link {{ request()->routeIs('guru.courses.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-book"></i>
                  <p>Materi</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-journal-check"></i>
                  <p>Kuis</p>
                </a>
              </li>
              <li class="nav-header">PENILAIAN</li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-pencil-square"></i>
                  <p>Penilaian Essay</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-graph-up"></i>
                  <p>Laporan Siswa</p>
                </a>
              </li>
            </ul>
            <!--end::Sidebar Menu-->
          </nav>
        </div>
        <!--end::Sidebar Wrapper-->
      </aside>
      <!--end::Sidebar-->

      <!--begin::App Main-->
      <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="guru-page-header">
              <div>
                <div class="guru-section-label">{{ $pageContext }}</div>
                <h3 class="guru-page-title">{{ $pageTitle }}</h3>
                <p class="guru-page-subtitle">{{ $pageSubtitle }}</p>
              </div>
              <div>
                <ol class="breadcrumb guru-breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Beranda</a></li>
                  @if($breadcrumbSection)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumbSectionUrl }}">{{ $breadcrumbSection }}</a></li>
                  @endif
                  <li class="breadcrumb-item active" aria-current="page">
                    {{ $pageTitle }}
                  </li>
                </ol>
              </div>
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->

        <!--begin::App Content-->
        <div class="app-content guru-page">
          <!--begin::Container-->
          <div class="container-fluid">
            {{ $slot }}
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->

      <!--begin::Footer-->
      <footer class="app-footer">
        <!--begin::To the end-->
        <div class="float-end d-none d-sm-inline">{{ config('app.name') }}</div>
        <!--end::To the end-->
        <!--begin::Copyright-->
        <strong>
          Copyright &copy; 2024&nbsp;
          <a href="#" class="text-decoration-none">{{ config('app.name') }}</a>.
        </strong>
        All rights reserved.
        <!--end::Copyright-->
      </footer>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->

    <!--begin::Script-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Third Party Plugin(Bootstrap)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(Bootstrap)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <script src="{{ asset('adminlte/js/adminlte.js') }}"></script>
    <!--end::Required Plugin(AdminLTE)-->

    @livewireScripts
    @if (session('success'))
      <script>
        (() => {
          const dispatchNotify = () => document.dispatchEvent(new CustomEvent('guru-notify', {
            detail: {
              type: 'success',
              message: @js(session('success')),
            },
          }));

          if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', dispatchNotify, { once: true });
            return;
          }

          dispatchNotify();
        })();
      </script>
    @endif
    @if (session('error'))
      <script>
        (() => {
          const dispatchNotify = () => document.dispatchEvent(new CustomEvent('guru-notify', {
            detail: {
              type: 'error',
              message: @js(session('error')),
            },
          }));

          if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', dispatchNotify, { once: true });
            return;
          }

          dispatchNotify();
        })();
      </script>
    @endif
    @stack('scripts')
  </body>
  <!--end::Body-->
</html>
