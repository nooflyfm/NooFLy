@extends('layouts.app')

@section('content')
<div class="container main">
    <my-budget homeurl="{{ url('/') }}" currency="{{ $currency }}"></my-budget>
</div>
@endsection