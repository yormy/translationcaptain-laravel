@extends('layouts.app')

@section('content')
    BEDROCK welcome blade

    <div class="d-flex flex-column">
        <div>
        @if (Route::has('login'))
            <div class="top-right links">
                {{ __('app.language') }}
                @auth
                    <a href="{{ url('/home') }}">Home</a>
                @else
                    <a href="{{ route('login') }}">Login</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}">Register</a>
                    @endif
                @endauth
            </div>
        @endif
        </div>
        <div>
            <br>
            ==>  <i class="fad fa-lightbulb-exclamation"></i> <== <i class="fas fa-lightbulb"></i> ==>
            @php
                echo \App()->getLocale() ." <==";
            @endphp
            <div class="">
                {{ __('app.welcome.found') }}<br>
            </div>
        </div>
    </div>
@endsection
