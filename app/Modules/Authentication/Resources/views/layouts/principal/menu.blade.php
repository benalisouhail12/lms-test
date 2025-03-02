

        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo">
            <a href="" class="app-brand-link">
              <span class="app-brand-logo demo">
               <img class="img-fuild" style="    width: 54px;
                height: 44px;"
                src="{{ asset('assets/img/logo/Logo.png') }}">
              </span>
              <span class="app-brand-text demo menu-text fw-bold">LMS</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
              <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
              <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
            </a>
          </div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
 <!-- Dashboards -->
          {{--   <li class="menu-item {{ request()->routeIs('home') ? 'active' : '' }} ">
              <a href="{{ route('home') }}" class="menu-link ">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div data-i18n="Dashboards">Dashboards</div>

              </a>

            </li> --}}

              <li class="menu-item ">
                <a href="" class="menu-link  ">
                  <i class="menu-icon tf-icons ti  ti-wallet "></i>
                  <div data-i18n="Cours">Cours</div>
                </a>
              </li>
              <li class="menu-item ">
                <a href="" class="menu-link  ">
                  <i class="menu-icon tf-icons ti  ti-wallet "></i>
                  <div data-i18n="Uitilisateurs">Uitilisateurs</div>
                </a>
              </li>
              <li class="menu-item ">
                <a href="" class="menu-link  ">
                  <i class="menu-icon tf-icons ti  ti-wallet "></i>
                  <div data-i18n="Étudiants">Étudiants</div>
                </a>
              </li>
              <li class="menu-item ">
                <a href="" class="menu-link  ">
                  <i class="menu-icon tf-icons ti  ti-wallet "></i>
                  <div data-i18n="Enseignants">Enseignants</div>
                </a>
              </li>


          </ul>
        </aside>
