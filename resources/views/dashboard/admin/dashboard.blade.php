@extends('dashboard.layouts.master')
@section('css')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
@endsection

@section('title')
{{ $title }}
@endsection
@section('content')
<x-theme-page-wrapper>
    <x-theme component="dashboard.index" />
</x-theme-page-wrapper>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection
