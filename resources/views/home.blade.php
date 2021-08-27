@extends('layouts.app')

@section('content')
<div class="container main">
    <home-page homeurl="{{ url('/') }}"></home-page>
</div>
@endsection
