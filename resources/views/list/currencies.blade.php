@extends('layouts.app')

@section('content')
<div class="container main">
    <currencies-list homeurl="{{ url('/') }}"></currencies-list>
</div>
@endsection
