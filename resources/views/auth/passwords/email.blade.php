@extends('layouts.app')

@section('content')
<div>
    <a class="hiddenanchor" id="signup"></a>
    <a class="hiddenanchor" id="signin"></a>

    <div class="login_wrapper">
      <div class="animate form login_form">
        <section class="login_content">
            <form class="login100-form validate-form" method="POST" action="{{ route('password.email') }}">
                @csrf
                <h1>Restaurar contraseña</h1>

                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <div>
                    <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Correo electrónico" required="" />
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div>
                    <button type="submit" class="btn btn-default submit">
                        Continuar
                    </button>
                    <a class="reset_pass" href="{{ route('login') }}">Login</a>
                </div>

                <div class="clearfix"></div>

                <div class="separator">
                  <div class="clearfix"></div>
                  <br />

                  <div>
                    <h1><i class="fa fa-credit-card mr-2"></i> Tiquetera electrónica</h1>
                </div>
            </form>
        </section>
      </div>
    </div>
</div>
@endsection