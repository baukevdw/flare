@extends('layouts.app')

@section('content')
    <x-core.layout.info-container>
        @php
            $backUrl = route('units.list');

            if (!auth()->user()->hasRole('Admin')) {
                $backUrl = '/information/kingdom-units';
            }
        @endphp
        {{-- Spacer div. --}}
        <div class="pb-10"></div>
        <x-core.cards.card-with-title
            title="{{$unit->name}}"
            buttons="true"
            backUrl="{{$backUrl}}"
            editUrl="{{route('units.edit', ['gameUnit' => $unit->id])}}"
        >
            @include('admin.kingdoms.units.partials.unit-attributes', [
                'unit' => $unit
            ])
        </x-core.cards.card-with-title>
    </x-core.layout.info-container>

@endsection
