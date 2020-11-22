@extends('layouts.app')

@section('content')
    <h1> Admin overview</h1>
    <div class="d-flex flex-row">
        <div class="card flex-fill m-5">
            <div class="box p-2 rounded text-center blue">
                <h1 class="font-weight-light text-white">Total</h1>
                <h6 class="text-white">{{json_decode($points)->total}}</h6>
            </div>
        </div>

        <div class="card flex-fill m-5">
            <div class="box p-2 rounded text-center green">
                <h1 class="font-weight-light text-white">Paid</h1>
                <h6 class="text-white">{{json_decode($points)->paid}}</h6>
            </div>
        </div>

        <div class="card flex-fill m-5">
            <div class="box p-2 rounded text-center orange">
                <h1 class="font-weight-light text-white">Unpaid</h1>
                <h6 class="text-white">{{json_decode($points)->unpaid}}</h6>
            </div>
        </div>
    </div>

    <referrer-referrer-overview
        :data-table="{{$referrers}}"
    >
    </referrer-referrer-overview>

@endsection
