<!DOCTYPE html>

<html
  lang="en"
  class="light-style layout-wide customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('assets') }}/"
  data-template="vertical-menu-template">
  
  @include('Index.Partials.Head.head')
    <body>
      @yield('content')
    </body>
    @include('Index.Partials.Head.script')
</html>