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



    {{-- <ul class="menu-inner py-1">
   
      <li class="menu-item active open">
        <a href="{{ url('/dashboard') }}" class="menu-link">
          <i class="menu-icon tf-icons bx bx-home-circle"></i>
          <div class="text-truncate" data-i18n="Dashboards">Dashboards</div>
        </a>
      </li>

      <!-- Apps & Pages -->
      <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Manajemen &amp; Bisnis</span>
      </li>
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bxs-user-voice"></i>
            <div class="text-truncate" data-i18n="Custumer">Custumer</div>
          </a>
        <ul class="menu-sub">
          <li class="menu-item active">
            <a href="{{ url('/custumer') }}" class="menu-link">
              <div class="text-truncate" data-i18n="Custumer List">Custumer List</div>
            </a>
          </li>
        </ul>
        <ul class="menu-sub">
          <li class="menu-item active">
            <a href="{{ url('/custumer-profile') }}" class="menu-link">
              <div class="text-truncate" data-i18n="Custumer Profile">Custumer Profile</div>
            </a>
          </li>
        </ul>
      </li>
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-food-menu"></i>
            <div class="text-truncate" data-i18n="Invoice">Invoice</div>
          </a>
        <ul class="menu-sub">
          <li class="menu-item active">
            <a href="{{ url('/invoice') }}" class="menu-link">
              <div class="text-truncate" data-i18n="List Invoice">List Invoice</div>
            </a>
          </li>
        </ul>
        <ul class="menu-sub">
          <li class="menu-item active">
            <a href="{{ url('/invoice/add') }}" class="menu-link">
              <div class="text-truncate" data-i18n="Add Invoice">Add Invoice</div>
            </a>
          </li>
        </ul>
      </li>
      <li class="menu-item">
        <a href="{{ url('/keuangan') }}" class="menu-link">
          <i class="menu-icon tf-icons bx bx-money-withdraw"></i>
          <div class="text-truncate" data-i18n="Kas Harian">Kas Harian</div>
        </a>
      </li>
      
      <!-- Produk & Stok -->
      <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Produk &amp; Stok</span>
      </li>
      <li class="menu-item">
        <a href="{{ url('/product') }}" class="menu-link">
          <i class="menu-icon tf-icons bx bx-package"></i>
          <div class="text-truncate" data-i18n="Produk">Produk</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="{{ url('/categories') }}" class="menu-link">
          <i class="menu-icon tf-icons bx bxs-bar-chart-square"></i>
          <div class="text-truncate" data-i18n="Category">Category</div>
        </a>
      </li>
      
      <!-- Produk & Stok -->
      <li class="menu-header small text-uppercase">
        <span class="menu-header-text">User &amp; Setting</span>
      </li>
      <li class="menu-item">
        <a href="app-email.html" class="menu-link">
          <i class="menu-icon tf-icons bx bxs-user-rectangle"></i>
          <div class="text-truncate" data-i18n="Profil">Profil</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="app-chat.html" class="menu-link">
          <i class="menu-icon tf-icons bx bxs-cog"></i>
          <div class="text-truncate" data-i18n="Setting">Setting</div>
        </a>
      </li>
      
      <!-- Produk & Stok -->
      <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Admin</span>
      </li>
      <li class="menu-item">
        <a href="app-email.html" class="menu-link">
          <i class="menu-icon tf-icons bx bx-menu-alt-left"></i>
          <div class="text-truncate" data-i18n="Menu">Menu</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="app-chat.html" class="menu-link">
          <i class="menu-icon tf-icons bx bx-check-shield"></i>
          <div class="text-truncate" data-i18n="Role">Role & Permission</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="app-chat.html" class="menu-link">
          <i class="menu-icon tf-icons bx bxs-traffic-cone"></i>
          <div class="text-truncate" data-i18n="Setting">Setting</div>
        </a>
      </li>
    </ul> --}}
  </aside>
  <!-- / Menu -->