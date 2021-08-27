@extends('layouts.app')

@section('content')
<div class="container main">
    <user-profile homeurl="{{ url('/') }}" user="{{ $user }}"></user-profile>
</div>
@endsection
