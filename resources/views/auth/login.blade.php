@extends('layouts.app')

@section('content')
<div>
    <a class="hiddenanchor" id="signup"></a>
    <a class="hiddenanchor" id="signin"></a>

    <div class="login_wrapper">
        <div class="animate form login_form">
            <section class="login_content">
                <form class="login100-form validate-form" method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Logo del negocio -->
                    <img src="{{ asset('img/logo-negocio.png') }}" alt="Logo del Negocio">

                    <h1>Autenticación</h1>

                    <!-- Input de usuario -->
                    <div>
                        <input id="login" type="text" name="login" class="form-control @error('login') is-invalid @enderror" placeholder="Usuario" required />
                @error('login')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                    </div>

                    <!-- Input de contraseña -->
                    <div>
                        <input id="password" type="password" name="password" class="form-control" placeholder="Contraseña" required />
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                    </div>

                    <!-- Botón de acceso -->
                    <div>
                        <button type="submit" class="btn btn-default submit">
                            {{ __('Login') }}
                        </button>
                        <a class="reset_pass" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                    </div>

                    <div class="clearfix"></div>

                    <div class="separator">
                        <div class="clearfix"></div>
                        <br />
                        <div>
                            <h1><i class="fa fa-arrow-right mr-2"></i>{{ config('app.name', 'Laravel') }}</h1>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
@endsection
