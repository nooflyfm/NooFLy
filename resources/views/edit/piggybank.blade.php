@extends('layouts.app')

@section('content')
<div class="container main">
    <edit-piggybank homeurl="{{ url('/') }}" id="{{ $id }}"></edit-piggybank>
</div>
@endsection
