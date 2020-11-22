@extends('translationcaptain-laravel::layouts.app')

@section('content')
    <table class="table mt-5">
        <thead>
        <tr>
            <th>group</th>
            <th>key</th>
            <th>value</th>

        </tr>
        </thead>
        <tbody>
            @foreach (json_decode($overview) as $idLanguage => $language)
                <tr>
                    <td colspan="3" class=""><h1>{{$idLanguage}}</h1></td>
                </tr>
                @foreach ($language as $idGroup => $group)

                    @foreach ($group as $key => $value)
                        <tr>
                            <td class="">{{$idGroup}}</td>
                            <td class="">{{$key}}</td>
                            <td class="">{{$value}}</td>
                        </tr>
                    @endforeach

                @endforeach

            @endforeach
        </tbody>
    </table>
@endsection
