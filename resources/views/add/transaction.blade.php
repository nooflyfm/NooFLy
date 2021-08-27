@extends('layouts.app')

@section('content')
<div class="container main">
    <add-transaction homeurl="{{ url('/') }}"></add-transaction>
</div>
@endsection
