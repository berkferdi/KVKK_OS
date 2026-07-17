@extends('layouts.admin')

@section('title', 'Dashboard — KVKK 360')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Hoş geldiniz, '.auth()->user()->name)

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $companyCount }}</h3>
                <p>Firma</p>
            </div>
            <div class="icon"><i class="fas fa-building"></i></div>
            <a href="{{ route('companies.index') }}" class="small-box-footer">Firmalara git <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>
@endsection
