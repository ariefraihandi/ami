<!DOCTYPE html>

<html
  lang="en"
  class="light-style layout-wide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('assets') }}/"
  data-template="vertical-menu-template">
  
    @include('Index.Partials.head-print')
    <body>
        @yield('content')
    </body>
    @include('Index.Partials.scripts-print')
</html>