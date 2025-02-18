<!-- ########## START: LEFT PANEL ########## -->
<div class="br-logo"><a href=""><span>[</span>{{ config('app.name', 'Laravel') }}<span>]</span></a></div>
<div class="br-sideleft overflow-y-auto ps ps--theme_default ps--active-y" data-ps-id="974e69c3-0ce1-29d2-6a61-cc93560eb874">
  <label class="sidebar-label pd-x-15 mg-t-20">Navigation</label>
  <div class="br-sideleft-menu">
    <a href="{{ url('/home')}}" class="br-menu-link">
      <div class="br-menu-item">
        <i class="menu-item-icon icon ion-ios-home-outline tx-22"></i>
        <span class="menu-item-label">Home</span>
      </div><!-- menu-item -->
    </a><!-- br-menu-link -->
    <!-- sidebar menu: : style can be found in sidebar.less -->
    @foreach ($meetingsComposer as $key => $item)
          @include("layouts.commitment.commitment-item", ["item" => $item])
    @endforeach
  </div><!-- br-sideleft-menu -->

  <br>
  <div class="ps__scrollbar-x-rail" style="left: 0px; bottom: 0px;">
    <div class="ps__scrollbar-x" tabindex="0" style="left: 0px; width: 0px;">
    </div>
  </div>
  <div class="ps__scrollbar-y-rail" style="top: 0px; height: 646px; right: 0px;">
    <div class="ps__scrollbar-y" tabindex="0" style="top: 0px; height: 488px;">
    </div>
  </div>
</div><!-- br-sideleft -->
<!-- ########## END: LEFT PANEL ########## -->