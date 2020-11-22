@extends('layouts.app')

@section('content')
    <h1> Admin overview</h1>
    <div class="d-flex flex-row">
        <div class="card flex-fill m-5">
            <div class="box p-2 rounded text-center blue">
                <h1 class="font-weight-light text-white">Total</h1>
                <h6 class="text-white"><span id='total'>{{json_decode($points)->total}}</span></h6>
            </div>
        </div>

        <div class="card flex-fill m-5">
            <div class="box p-2 rounded text-center green">
                <h1 class="font-weight-light text-white">Paid</h1>
                <h6 class="text-white"><span id='paid'>{{json_decode($points)->paid}}</span></h6>
            </div>
        </div>

        <div class="card flex-fill m-5">
            <div class="box p-2 rounded text-center orange">
                <h1 class="font-weight-light text-white">Unpaid</h1>
                <h6 class="text-white"><span id='unpaid'>{{json_decode($points)->unpaid}}</span></h6>
            </div>
        </div>
    </div>

    <table class="table mt-5">
        <thead>
        <tr>
            <th>Affiliate</th>
            <th>Name</th>
            <th>total points</th>
            <th>paid</th>
            <th>unpaid</th>
            <th>data last action</th>
            <th>action</th>
        </tr>
        </thead>
        <tbody>

            @foreach (json_decode($referrers) as $id => $referrer)
            <tr>
                <td class="">@isset($referrer->id){{$referrer->id}}@endisset</td>
                <td class="">@isset($referrer->name){{$referrer->name}}@endisset</td>
                <td class="">@isset($referrer->total){{$referrer->total}}@endisset</td>
                <td class="">@isset($referrer->paid){{$referrer->paid}}@endisset</td>
                <td class="">@isset($referrer->unpaid){{$referrer->unpaid}}@endisset</td>
                <td class="">@isset($referrer->created_at){{$referrer->created_at}}@endisset</td>
                <td class=""><a href="{{url()->current(). "/". $referrer->id}}"><button class="bnt btn-link">[open]</button></a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
