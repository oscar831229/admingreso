<!-- top navigation -->
<div class="top_nav">
  <div class="nav_menu">
        <div class="nav toggle">
            <a id="menu_toggle"><i class="fa fa-bars"></i></a>
        </div>
        <nav class="nav navbar-nav" style="float: left; margin: 0; width: 600px !important;">
            <div style="margin-top: 13px;"><h6><i class="fa fa-calendar" aria-hidden="true"></i> Fecha sistema : {{ formatDate(getSystemDate(), 'Y-m-d') }}  :  {!! obtenerTemporadaNameDate(getSystemDate()) !!}</h6></div>
        </nav>
        <nav class="nav navbar-nav">

            <ul class=" navbar-right">

                <li class="nav-item dropdown open" style="padding-left: 15px;">

                    <a href="javascript:;" class="user-profile dropdown-toggle" aria-haspopup="true" id="navbarDropdown" data-toggle="dropdown" aria-expanded="false">
                        <img src="{{ asset(imgperfil()) }}" alt="">{{ auth()->user()->name ?? 'Usuario externo' }}
                    </a>
                    <div class="dropdown-menu dropdown-usermenu pull-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-sign-out pull-right"></i> Cerrar sesión</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </nav>
  </div>
</div>
<!-- /top navigation -->
