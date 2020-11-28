@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                admin.
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                            {{ __('app.home.found') }}<br>
                        </div>
                    @endif ADMIN You are logged in!

                    <ul>
                        <li><a href="user/terms">Terms Reset</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>



</div>
@endsection
