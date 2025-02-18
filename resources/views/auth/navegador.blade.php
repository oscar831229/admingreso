@extends('layouts.app')

@section('content')
<div class="ht-100v d-flex align-items-center justify-content-center">
      <div class="wd-lg-70p wd-xl-50p tx-center pd-x-40">
        <h1 class="tx-100 tx-xs-140 tx-normal tx-inverse tx-roboto mg-b-0">Error!</h1>
        <h5 class="tx-xs-24 tx-normal tx-info mg-b-30 lh-5">Favor utilizar otro navegador diferente a "Internet Explorer".</h5>
        <p class="tx-16 mg-b-30">URL SIH GESTIÓN HOSPITALARIA : {{ url("/") }}</p>

        <div class="d-flex justify-content-center">
        </div><!-- d-flex -->
      </div>
    </div>
@endsection

