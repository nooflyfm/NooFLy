@extends('layouts.app')

@section('content')
<div class="container main">
    <edit-cash homeurl="{{ url('/') }}" id="{{ $id }}"></edit-cash>
</div>
@endsection
