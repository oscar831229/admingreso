<!-- ########## START: HEAD PANEL ########## -->
<div class="br-header">
    <div class="br-header-left">
      <div class="navicon-left hidden-md-down"><a id="btnLeftMenu" href=""><i class="icon ion-navicon-round"></i></a></div>
      <div class="navicon-left hidden-lg-up"><a id="btnLeftMenuMobile" href=""><i class="icon ion-navicon-round"></i></a></div>
    </div><!-- br-header-left -->
    <div class="br-header-right">
      <nav class="nav">

        @if (isset(auth()->user()->name))
        <div class="dropdown">
          <a href="" class="nav-link pd-x-7 pos-relative" data-toggle="dropdown">
            <i class="icon ion-ios-bell-outline tx-24"></i>
            <!-- start: if statement -->
            <span class="square-8 bg-danger pos-absolute t-15 r-5 rounded-circle"></span>
            <!-- end: if statement -->
          </a>
          <div class="dropdown-menu dropdown-menu-header wd-300 pd-0-force">
            <div class="d-flex align-items-center justify-content-between pd-y-10 pd-x-20 bd-b bd-gray-200">
              <label class="tx-12 tx-info tx-uppercase tx-semibold tx-spacing-2 mg-b-0">Notificaciones</label>
              <a href="" class="tx-11">Marcar todas como leidas.</a>
            </div><!-- d-flex -->

            <div class="media-list">
              <!-- loop starts here -->
              <a href="" class="media-list-link read">
                <div class="media pd-x-20 pd-y-15">
                  <div class="media-body">
                    <p class="tx-13 mg-b-0 tx-gray-700"><strong class="tx-medium tx-gray-800">Respuesta solicitud cotización</strong>Arnoldo Prada</p>
                    <span class="tx-12">Abril 03, 2020 8:45am</span>
                  </div>
                </div><!-- media -->
              </a>
              <div class="pd-y-10 tx-center bd-t">
                <a href="" class="tx-12"><i class="fa fa-angle-down mg-r-5"></i> Ver todas las Notificaciones</a>
              </div>
            </div><!-- media-list -->
          </div><!-- dropdown-menu -->
        </div>
        <!-- dropdown -->
        @endif
        <div class="dropdown">
          <a href="" class="nav-link nav-link-profile" data-toggle="dropdown">
            <span class="logged-name hidden-md-down">{{ auth()->user()->name ?? 'Usuario externo' }}</span>
            <img src="{{ asset(imgperfil()) }}" class="wd-32 rounded-circle" alt="">
            <span class="square-10 bg-success"></span>
          </a>
          <div class="dropdown-menu dropdown-menu-header wd-200">
            <ul class="list-unstyled user-profile-nav">
            @if (isset(auth()->user()->name))
              <li><a href="{{ route('logout') }}"
                onclick="event.preventDefault();
                              document.getElementById('logout-form').submit();"><i class="icon ion-power"></i> Sign Out</a></li>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @else
              <li><a href="{{ route('login') }}"><i class="icon ion-power"></i> Sign In</a></li>
            @endif
            </ul>
          </div><!-- dropdown-menu -->
         </div>

        </div><!-- dropdown -->
      </nav>
      
    </div>
    <!-- br-header-right -->
  </div><!-- br-header -->
<!-- ########## END: HEAD PANEL ########## -->