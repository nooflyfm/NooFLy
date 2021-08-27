@extends('layouts.app')

@section('content')
<div class="container main">
    <transactions-list homeurl="{{ url('/') }}"></transactions-list>
</div>
@endsection
