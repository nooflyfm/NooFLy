@extends('layouts.app')

@section('content')
@guest
<h1 class="text-white" style="float: left;margin-top: 200px;text-align: center;width: 100%;">404 | Page not found</h1>
@else
<div class="container main">
    <h1 class="text-danger" style="float: left;margin-top: 200px;text-align: center;width: 100%;">404 | Page not found</h1>
</div>
@endguest
@endsection