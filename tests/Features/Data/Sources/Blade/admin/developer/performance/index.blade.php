@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card-header">PERFORMANCE</div>
    <v-row>
        <v-col>
            <performance-card
                :title="'{{$pageAvgResponse}}ms'"
                :subtitle="'average page response'"
            >
            </performance-card>
        </v-col>
        <v-col>
            <performance-card
                :title="'{{$sqlAvgResponse}}ms'"
                :subtitle="'average sql response'"
                :color="'bg-primary'"
            >
            </performance-card>
        </v-col>
    </v-row>

    <br>
    <h1>Date range: {{$from}} - {{$until}}</h1>
    @if($excluded)
            <h3>excluded main pages</h3>
    @endif
        Main pages:
        <a href="{{route('admin.developer.performance.dashboard', ['exclude'=>1])}}" >
            Exclude
            {{ __('app.developer.performance.index.found') }}<br>
        </a>
        <a href="{{route('admin.developer.performance.dashboard', ['exclude'=>0])}}" >
            Include
        </a>

    <load-graph
        title="pages weighted"
        :labels="{{$pageGraphLabels}}"
        :values="{{$pageGraphValues}}"
    >
    </load-graph>

    <load-graph
        title="sql weighted"
        :labels="{{$sqlGraphLabels}}"
        :values="{{$sqlGraphValues}}"
    >
    </load-graph>

    <v-row>
        <v-col cols="6">
            <weighted-pages
                title="Weighted pages"
                :values = "{{$pageWeightedAvg}}"
            ></weighted-pages>
        </v-col>
        <v-col cols="6">
            <weighted-pages
                title="Weighted SQL"
                :values = "{{$sqlWeightedAvg}}"
            ></weighted-pages>
        </v-col>
    </v-row>

    <v-row>
        <v-col cols="6">
            <top-pages
                title="Top pages"
                :values = "{{$pageUsage}}"
            ></top-pages>
        </v-col>
        <v-col cols="6">
            <slow-pages
                title="Slow pages"
                :values = "{{$pageSlow}}"
            ></slow-pages>
        </v-col>
    </v-row>


    <v-row>
        <v-col cols="6">
            <top-pages
                title="Top SQL"
                :values = "{{$sqlUsage}}"
            ></top-pages>
        </v-col>
        <v-col cols="6">
            <slow-pages
                title="Slow SQL"
                :values = "{{$sqlSlow}}"
            ></slow-pages>
        </v-col>
    </v-row>

</div>
@endsection
