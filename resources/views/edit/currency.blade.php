@extends('layouts.app')

@section('content')
<div class="container main">
    <edit-currency homeurl="{{ url('/') }}" id="{{ $id }}"></edit-currency>
</div>
@endsection
