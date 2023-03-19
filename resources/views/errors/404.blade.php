@extends('layout.layout')
@section('title', 'Not found | Chat-Wink')

@section('content')
    <div>
        Oops!!! Page not found
        <a href="{{ route('index') }}">Go home</a>
    </div>
@endsection
