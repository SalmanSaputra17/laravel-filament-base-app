@extends('filament::pages.layout')

@section('content')
    <x-filament-panels::form>
        {{ $this->form }}
    </x-filament-panels-form>

    <div class="mt-4">
        {{ $this->save() }}
    </div>
@endsection
