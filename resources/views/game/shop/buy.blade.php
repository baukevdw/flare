@extends('layouts.app')

@section('content')
    <div class="w-full lg:w-3/4 m-auto">

        <x-core.page-title
            title="Buying"
            route="{{route('game')}}"
            link="Game"
            color="primary"
        ></x-core.page-title>

        <div class="m-auto">
            <x-core.cards.card>
                @if ($isLocation)
                    <p class="mb-4 italic">
                        You enter the old and musty shop. Along the walls you and see various weapons, armor
                        and other types of items that might benefit you on your journeys. You see an old man behind the counter writing something in a book,
                        he looks up at you.
                    </p>

                    <p class="mb-4"><strong>Shop Keeper</strong>: <em>Hello! welcome! what can I get for you?</em></p>
                @else
                    <p class="mb-4 italic">On your journey you come across a merchant on the road. He is carrying his bag full of trinkets and goodies.</p>
                    <p class="mb-4 italic">As you approach, he takes off his backpack and warmly greets you:</p>
                    <p class="mb-4"><strong>Shop Keeper</strong>: <em>These roads are dangerous my friend! What can I get you?</em></p>
                @endif
            </x-core.cards.card>

            <x-core.cards.card css="mt-5">
                <p><strong>Your Gold</strong>: <span class="color-gold">{{number_format($gold)}}</span></p>
            </x-core.cards.card>

            @livewire('admin.items.items-table', ['isShop' => true])
        </div>
  </div>
@endsection
