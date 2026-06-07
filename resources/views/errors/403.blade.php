@extends('components.layouts.error')

@section('title', '403 — Acesso Negado')

@section('code', '403')

@section('heading', 'Acesso negado.')
@section('subtext', 'Você não tem permissão para acessar este recurso. Entre em contato com um administrador se acredita que isso é um erro.')

@section('actions')
    @if(auth()->check())
        <a href="{{ route('dashboard') }}" class="btn btn-primary">Ir para o painel</a>
    @else
        <a href="{{ route('home') }}" class="btn btn-primary">Voltar à home</a>
    @endif
@endsection
