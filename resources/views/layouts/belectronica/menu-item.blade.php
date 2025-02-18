@if ($item["submenu"] == [])
    <li>
        <a href="{{url($item['url'])}}" class="nav-link {{getMenuActivo($item["url"])}}">
            {{$item["nombre"]}}
        </a>
    </li>
    {{-- <li class="nav-item">
        <a href="{{url($item['url'])}}" class="nav-link {{getMenuActivo($item["url"])}}">
            <i class="nav-icon fa {{$item["icono"]}}"></i>
            <p>
                {{$item["nombre"]}}
            </p>
        </a>
    </li> --}}
@else
    <li>
        <a>
            <i class="{{$item["icono"]}}"></i> {{$item["nombre"]}} <span class="fa fa-chevron-down"></span>
        </a><!-- br-menu-link -->
        
        <ul class="nav child_menu">
            @foreach ($item["submenu"] as $submenu)
                @include("layouts.portal.menu-item", ["item" => $submenu])
            @endforeach
            {{-- <li class="nav-item"><a href="accordion.html" class="nav-link">Informe gerencial</a></li>
            <li class="nav-item"><a href="alerts.html" class="nav-link">Informe solicitudes</a></li> --}}
        </ul>
    </li>
    


    {{-- <li class="nav-item has-treeview">
        <a href="javascript:;" class="nav-link">
          <i class="nav-icon fa {{$item["icono"]}}"></i>
          <p>
            {{$item["nombre"]}}
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
            @foreach ($item["submenu"] as $submenu)
                @include("layouts.portal.menu-item", ["item" => $submenu])
            @endforeach
        </ul>
    </li> --}}
@endif
