@extends('layouts.app')

@section('content')
<div class="container main">
    <piggybanks-list homeurl="{{ url('/') }}"></piggybanks-list>
</div>
@endsection
