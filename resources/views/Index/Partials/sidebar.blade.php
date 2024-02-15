<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
      <a href="index.html" class="app-brand-link">
        <span class="app-brand-logo demo">
            <img src="{{ asset('assets') }}/img/icons/brands/ami-logo.png" alt="AMI Fast Logo" width="35">
        </span>

        <span class="app-brand-text demo menu-text fw-bold ms-2">
          <img src="{{ asset('assets') }}/img/icons/brands/fast.png" alt="AMI Fast Logo" width="120">
        </span>
      </a>

      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
        <i class="bx bx-chevron-left bx-sm align-middle"></i>
      </a>
    </div>

    <div class="menu-inner-shadow"></div>
    @if (!function_exists('isActiveSubMenu'))
    @php
        function isActiveSubMenu($title)
        {
            $currentUrl = request()->path();
            $urlParts = explode('/', $currentUrl);

            // Check if the title matches the first segment of the URL
            return $title == $urlParts[0] ? 'active' : '';
        }
    @endphp
@endif

@if (!function_exists('isActiveChildSubMenu'))
    @php
        function isActiveChildSubMenu($childRoute)
        {
            $currentUrl = str_replace('/', '.', request()->path());
            return $childRoute == $currentUrl ? 'active' : '';
        }
    @endphp
@endif





<ul class="menu-inner py-1">
  @foreach($menus as $menu)
      <li class="menu-header small text-uppercase">
          <span class="menu-header-text">{{ $menu->menu_name }}</span>
      </li>

      @php
          $filteredSubMenus = $subMenus->where('menu_id', $menu->id);
      @endphp

      @if($filteredSubMenus->isNotEmpty())
        @foreach($filteredSubMenus as $sub)
          <li class="menu-item {{ isActiveSubMenu($sub->title) }}">
            <a href="{{ $sub->itemsub == 1 ? 'javascript:void(0);' : route($sub->url) }}" class="menu-link{{ $sub->itemsub == 1 ? ' menu-toggle' : '' }}">
              <i class="menu-icon tf-icons {{ $sub->icon }}"></i>
              <div class="text-truncate" data-i18n="{{ ucwords($sub->title) }}">{{ $sub->title }}</div>
            </a>  
            @php
              $matchingChildSubMenus = $childSubMenus->where('id_submenu', $sub->id);
            @endphp
            @if($matchingChildSubMenus->isNotEmpty())
              <ul class="menu-sub">
                @foreach($matchingChildSubMenus as $childSub)
                  <li class="menu-item {{ isActiveChildSubMenu($childSub->url) }}">
                    <a href="{{ route($childSub->url) }}" class="menu-link">
                      <div class="text-truncate" data-i18n="{{ $childSub->title }}">{{ $childSub->title }}</div>
                    </a>
                  </li>
                @endforeach
              </ul>
            @endif
          </li>
        @endforeach           
      @endif
    @endforeach
  </ul>
</aside>