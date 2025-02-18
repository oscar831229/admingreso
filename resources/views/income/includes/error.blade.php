@extends('layouts.belectronica.principal')

@section('css_custom')
@endsection

@section('scripts_content')
@endsection


@section('content')
<div class="br-mainpanel">

    <div class="br-pageheader pd-y-15 pd-l-20">
      <nav class="breadcrumb pd-0 mg-0 tx-12">
        <a class="breadcrumb-item" href="#">HTC</a>
        <span class="breadcrumb-item active">{{ $resource }}</span>
      </nav>
    </div><!-- br-pageheader -->

    <div class="br-pagebody">
        <div class="br-section-wrapper mt-2">

            <table width='100%' style="margin-bottom: 20px;">
                <tr>
                    <td width='50' align="center" valign="top" class="pr-4">
                        <h1><i class="fa fa-sun-o" aria-hidden="true"></i></h1>
                    </td>
                    <td>
                        <h4 class="tx-gray-800 mg-b5" style="margin-bottom: 0px;">{{ $resource }}</h4>
                        <span class='titulos'><?php echo date('yy-m-d h:m'); ?></span>
                    </td>
                </tr>
            </table>

            <div class="ht-100v d-flex align-items-center justify-content-center">
                <div class="wd-lg-70p wd-xl-50p tx-center pd-x-40">
                    <h4 class="tx-50 tx-xs-100 tx-normal tx-inverse tx-roboto mg-b-0">410!</h4>
                    <h5 class="tx-xs-24 tx-normal tx-info mg-b-30 lh-5">{{ $error }}</h5>
                </div>
            </div>


        </div><!-- br-section-wrapper -->
    </div><!-- br-pagebody -->


</div>
@endsection
