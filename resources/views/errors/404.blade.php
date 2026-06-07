@extends('components.layouts.error')

@section('title', '404 — Link Não Encontrado')

@section('code', '404')

@section('heading', 'Oops! Link não encontrado.')
@section('subtext', 'O atalho que você está tentando acessar não existe ou foi removido. Verifique a URL e tente novamente.')

@section('actions')
    <a href="{{ route('home') }}" class="btn btn-primary">Criar um novo link</a>
    <a href="{{ route('dashboard') }}" class="btn btn-ghost">Meus links</a>
@endsection
