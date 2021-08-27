@extends('layouts.app')

@section('content')
<div class="container main">
    <cashes-list homeurl="{{ url('/') }}"></cashes-list>
</div>
@endsection
