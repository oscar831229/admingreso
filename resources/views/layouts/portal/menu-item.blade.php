@if ($item["submenu"] == [])
    <li class="nav-item">
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
    <a href="#" class="br-menu-link">
        <div class="br-menu-item">
            <i class="menu-item-icon icon {{$item["icono"]}} tx-24"></i>
            <span class="menu-item-label">{{$item["nombre"]}}</span>
            <i class="menu-item-arrow fa fa-angle-down"></i>
        </div><!-- menu-item -->
    </a><!-- br-menu-link -->
    <ul class="br-menu-sub nav flex-column">
        @foreach ($item["submenu"] as $submenu)
            @include("layouts.portal.menu-item", ["item" => $submenu])
        @endforeach
        {{-- <li class="nav-item"><a href="accordion.html" class="nav-link">Informe gerencial</a></li>
        <li class="nav-item"><a href="alerts.html" class="nav-link">Informe solicitudes</a></li> --}}
    </ul>


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
