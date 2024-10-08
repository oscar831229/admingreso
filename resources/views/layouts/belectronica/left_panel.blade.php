<div class="col-md-3 left_col">

  <div class="left_col scroll-view">

    <div class="navbar nav_title" style="border: 0;">
      <a href="#" class="site_title" style="font-size: 18px;"><i class="fa fa-arrow-right"></i> <span>{{ env('APP_NAME')}}</span></a>
    </div>

    <div class="clearfix"></div>

    <!-- menu profile quick info -->
      <div class="profile clearfix">
          <div class="profile_pic">
          <img src="{{ asset(imgperfil()) }}" alt="..." class="img-circle profile_img">
          </div>
          <div class="profile_info">
          <span>Bienvenido,</span>
          <h2>{{ auth()->user()->name ?? 'Usuario externo' }}</h2>
          </div>
      </div>
    <!-- /menu profile quick info -->

    <br />

    <!-- sidebar menu -->
    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
      <div class="menu_section">
        <h3>General</h3>
        <ul class="nav side-menu">
          <li>
              <a href="{{ url('/home')}}">
                <i class="fa fa-laptop"></i> Home
              </a>
          </li>
          {{--  <li>
            <a>
              <i class="fa fa-edit"></i> Forms <span class="fa fa-chevron-down"></span>
            </a>
            <ul class="nav child_menu">
              <li><a href="form.html">General Form</a></li>
              <li><a href="form_advanced.html">Advanced Components</a></li>
              <li><a href="form_validation.html">Form Validation</a></li>
              <li><a href="form_wizards.html">Form Wizard</a></li>
              <li><a href="form_upload.html">Form Upload</a></li>
              <li><a href="form_buttons.html">Form Buttons</a></li>
            </ul>
          </li>  --}}
          @foreach ($menusComposer as $key => $item)
                @if ($item["menu_id"] != 0)
                    @break
                @endif
                @include("layouts.belectronica.menu-item", ["item" => $item])
          @endforeach
        </ul>
      </div>
    </div>
    <!-- /sidebar menu -->

  </div>
</div>
