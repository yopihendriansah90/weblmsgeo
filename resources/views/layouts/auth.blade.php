<!doctype html>
<html lang="id">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $title ?? 'Login' }} | Web LMS SIG</title>

    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!--end::Accessibility Meta Tags-->

    <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      crossorigin="anonymous"
    />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
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

    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="{{ asset('adminlte/css/adminlte.css') }}" />
    <!--end::Required Plugin(AdminLTE)-->

    <style>
      :root {
        --auth-bg: #f8f9ff;
        --auth-surface: #ffffff;
        --auth-panel: #eff4ff;
        --auth-panel-strong: #dce9ff;
        --auth-text: #0b1c30;
        --auth-muted: #464555;
        --auth-border: #c7c4d8;
        --auth-primary: #3525cd;
        --auth-primary-hover: #4f46e5;
        --auth-secondary: #006a61;
        --auth-error: #ba1a1a;
      }

      * {
        box-sizing: border-box;
      }

      body.auth-page {
        min-height: 100vh;
        margin: 0;
        background:
          linear-gradient(135deg, rgba(79, 70, 229, 0.12), transparent 34%),
          linear-gradient(315deg, rgba(0, 106, 97, 0.12), transparent 32%),
          var(--auth-bg);
        color: var(--auth-text);
        font-family: Inter, sans-serif;
      }

      .auth-shell {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
      }

      .auth-card {
        width: min(100%, 1100px);
        min-height: 620px;
        display: grid;
        grid-template-columns: minmax(0, 1.08fr) minmax(390px, 0.92fr);
        overflow: hidden;
        border: 1px solid rgba(199, 196, 216, 0.75);
        border-radius: 8px;
        background: var(--auth-surface);
        box-shadow: 0 18px 60px rgba(11, 28, 48, 0.10);
      }

      .auth-brand-panel {
        position: relative;
        min-height: 620px;
        overflow: hidden;
        background: var(--auth-panel);
      }

      .auth-brand-content {
        position: relative;
        z-index: 2;
        min-height: 620px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 48px 32px 32px;
      }

      .auth-brand-mark,
      .auth-mobile-brand {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        width: fit-content;
        color: var(--auth-primary);
        text-decoration: none;
        font-family: "Plus Jakarta Sans", sans-serif;
        font-size: 26px;
        font-weight: 800;
        letter-spacing: 0;
      }

      .auth-brand-icon {
        font-size: 32px;
        font-variation-settings: 'FILL' 1, 'wght' 600, 'GRAD' 0, 'opsz' 24;
      }

      .auth-brand-copy {
        max-width: 480px;
        margin-top: 44px;
      }

      .auth-brand-copy h1,
      .auth-heading h2 {
        margin: 0;
        color: var(--auth-text);
        font-family: "Plus Jakarta Sans", sans-serif;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: 0;
      }

      .auth-brand-copy h1 {
        font-size: 40px;
      }

      .auth-brand-copy p,
      .auth-heading p {
        margin: 18px 0 0;
        color: var(--auth-muted);
        font-size: 17px;
        line-height: 1.65;
      }

      .auth-stat-card {
        display: inline-flex;
        align-items: center;
        gap: 14px;
        width: fit-content;
        max-width: 100%;
        padding: 16px 18px;
        border: 1px solid rgba(199, 196, 216, 0.9);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.88);
        box-shadow: 0 8px 28px rgba(11, 28, 48, 0.10);
        backdrop-filter: blur(8px);
      }

      .auth-stat-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #86f2e4;
        color: var(--auth-secondary);
        font-size: 26px;
      }

      .auth-stat-card span:not(.material-symbols-outlined) {
        display: block;
        color: var(--auth-muted);
        font-size: 13px;
        font-weight: 600;
      }

      .auth-stat-card strong {
        display: block;
        color: var(--auth-text);
        font-size: 16px;
        line-height: 1.35;
      }

      .auth-brand-image {
        position: absolute;
        inset: auto 0 0 0;
        z-index: 1;
        width: 100%;
        height: 60%;
        object-fit: cover;
        filter: grayscale(1);
        opacity: 0.86;
      }

      .auth-brand-panel::after {
        content: "";
        position: absolute;
        inset: 0;
        z-index: 1;
        background: linear-gradient(180deg, var(--auth-panel) 0%, rgba(239, 244, 255, 0.84) 38%, rgba(11, 28, 48, 0.44) 100%);
      }

      .auth-form-panel {
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 56px 64px;
        background: var(--auth-surface);
      }

      .auth-mobile-brand {
        display: none;
        margin-bottom: 32px;
        font-size: 22px;
      }

      .auth-heading {
        margin-bottom: 34px;
      }

      .auth-heading h2 {
        font-size: 38px;
      }

      .auth-heading p {
        font-size: 16px;
      }

      .auth-form {
        display: flex;
        flex-direction: column;
        gap: 18px;
      }

      .auth-field label,
      .auth-label-row label,
      .auth-remember,
      .auth-muted-link,
      .auth-footer-note {
        font-size: 14px;
        line-height: 1.4;
      }

      .auth-field label,
      .auth-label-row label {
        display: block;
        margin-bottom: 8px;
        color: var(--auth-muted);
        font-weight: 600;
      }

      .auth-label-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
      }

      .auth-muted-link {
        margin-bottom: 8px;
        color: var(--auth-primary);
        font-weight: 600;
      }

      .auth-input-wrap {
        display: grid;
        grid-template-columns: 22px minmax(0, 1fr) auto;
        align-items: center;
        gap: 12px;
        min-height: 64px;
        padding: 0 14px;
        border: 1px solid var(--auth-border);
        border-radius: 8px;
        background: var(--auth-surface);
        transition: border-color 160ms ease, box-shadow 160ms ease;
      }

      .auth-input-wrap:focus-within {
        border-color: var(--auth-primary);
        box-shadow: 0 0 0 4px rgba(53, 37, 205, 0.12);
      }

      .auth-input-wrap.is-invalid {
        border-color: var(--auth-error);
        box-shadow: 0 0 0 4px rgba(186, 26, 26, 0.08);
      }

      .auth-input-wrap > .material-symbols-outlined {
        color: #777587;
        font-size: 22px;
      }

      .auth-input-wrap input[type="text"],
      .auth-input-wrap input[type="password"] {
        width: 100%;
        min-width: 0;
        height: 58px;
        border: 0;
        outline: 0;
        background: transparent;
        color: var(--auth-text);
        font-family: Inter, sans-serif;
        font-size: 16px;
        line-height: 1.4;
      }

      .auth-input-wrap input::placeholder {
        color: rgba(70, 69, 85, 0.52);
      }

      .auth-eye {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: #777587;
        transition: color 160ms ease, background-color 160ms ease;
      }

      .auth-eye:hover {
        background: var(--auth-panel);
        color: var(--auth-primary);
      }

      .auth-error {
        margin: 8px 0 0;
        color: var(--auth-error);
        font-size: 13px;
        font-weight: 600;
      }

      .auth-remember {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        width: fit-content;
        color: var(--auth-muted);
        font-weight: 500;
        cursor: pointer;
        user-select: none;
      }

      .auth-remember input {
        width: 18px;
        height: 18px;
        margin: 0;
        border-color: var(--auth-border);
        border-radius: 4px;
        accent-color: var(--auth-primary);
      }

      .auth-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        min-height: 64px;
        margin-top: 4px;
        border: 0;
        border-radius: 8px;
        background: var(--auth-primary);
        color: #ffffff;
        font-size: 16px;
        font-weight: 800;
        box-shadow: 0 10px 24px rgba(53, 37, 205, 0.18);
        transition: transform 140ms ease, background-color 160ms ease, box-shadow 160ms ease;
      }

      .auth-submit:hover {
        background: var(--auth-primary-hover);
        box-shadow: 0 14px 30px rgba(53, 37, 205, 0.22);
      }

      .auth-submit:active {
        transform: translateY(1px);
      }

      .auth-submit[disabled] {
        opacity: 0.75;
        cursor: wait;
      }

      .auth-footer-note {
        margin-top: 34px;
        text-align: center;
        color: var(--auth-muted);
      }

      .auth-footer-note span {
        color: var(--auth-primary);
        font-weight: 800;
      }

      .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        line-height: 1;
      }

      @media (max-width: 900px) {
        .auth-shell {
          align-items: stretch;
          padding: 18px;
        }

        .auth-card {
          min-height: auto;
          grid-template-columns: 1fr;
        }

        .auth-brand-panel {
          display: none;
        }

        .auth-form-panel {
          justify-content: flex-start;
          padding: 34px 22px;
        }

        .auth-mobile-brand {
          display: inline-flex;
        }

        .auth-heading h2 {
          font-size: 30px;
        }
      }

      @media (max-width: 520px) {
        .auth-shell {
          padding: 0;
        }

        .auth-card {
          min-height: 100vh;
          border: 0;
          border-radius: 0;
          box-shadow: none;
        }

        .auth-form-panel {
          padding: 28px 18px;
        }

        .auth-input-wrap,
        .auth-submit {
          min-height: 58px;
        }

        .auth-muted-link {
          max-width: 50%;
          text-align: right;
        }
      }
    </style>
    
    @livewireStyles
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="auth-page">
    {{ $slot }}

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    
    <!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)-->
    
    <!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(Bootstrap 5)-->
    
    <!--begin::Required Plugin(AdminLTE)-->
    <script src="{{ asset('adminlte/js/adminlte.js') }}"></script>
    <!--end::Required Plugin(AdminLTE)-->
    
    @livewireScripts
    @stack('scripts')
  </body>
  <!--end::Body-->
</html>
