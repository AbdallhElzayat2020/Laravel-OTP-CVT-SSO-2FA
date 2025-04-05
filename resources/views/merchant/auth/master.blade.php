<!DOCTYPE html>

<html
    lang="en"
    class="light-style layout-wide customizer-hide"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="{{asset('merchant-assets')}}/"
    data-template="vertical-menu-template-free">
@include('merchant.auth.head')

<body>
<!-- Content -->
@yield('content')
<!-- / Content -->

<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->

@include('merchant.auth.scripts')
</body>
</html>
