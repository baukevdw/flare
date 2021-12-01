@extends('layouts.information')

@section('content')
    <x-core.cards.card-with-title title="{{$class->name}}" css="tw-mt-20 tw-mb-10 tw-w-full lg:tw-w-1/2 tw-m-auto">
        <div class="tw-flex tw-flex-wrap tw--mx-2 tw-mb-8">
            <div class="tw-w-full md:tw-w-1/2 tw-px-2 tw-mb-4">
                <dl>
                    <dt>Strength Mofidfier</dt>
                    <dd>+ {{$class->str_mod}} pts.</dd>
                    <dt>Durability Modifier</dt>
                    <dd>+ {{$class->dur_mod}} pts.</dd>
                    <dt>Dexterity Modifier</dt>
                    <dd>+ {{$class->dex_mod}} pts.</dd>
                    <dt>Intelligence Modifier</dt>
                    <dd>+ {{$class->int_mod}} pts.</dd>
                    <dt>Charsima Modifier</dt>
                    <dd>+ {{$class->chr_mod}} pts.</dd>
                    <dt>Accuracy Modifier</dt>
                    <dd>+ {{$class->accuracy_mod * 100}} %</dd>
                    <dt>Dodge Modifier</dt>
                    <dd>+ {{$class->dodge_mod * 100}} %</dd>
                    <dt>Looting Modifier</dt>
                    <dd>+ {{$class->looting_mod * 100}} %</dd>
                    <dt>Defense Modifier</dt>
                    <dd>+ {{$class->deffense_mod * 100}} %</dd>
                </dl>
            </div>
            <div class="tw-w-full md:tw-w-1/2 tw-px-2 tw-mb-4">
                @if ($class->gameSkills->isNotEmpty())
                    <h5>Class Skills</h5>
                    <ul>
                        @foreach ($class->gameSkills as $skill)
                            <li><a href="{{route('info.page.skill', ['skill' => $skill->id])}}">{{$skill->name}}</a></li>
                        @endforeach
                    </ul>
                    <hr />
                @endif
                <h5>Class Attack Bonus</h5>
                <p class="mt-2">
                    {{$classBonus['description']}}
                </p>
                <hr />
                <dl className="mt-2">
                    <dt>Type:</dt>
                    <dd>{{$classBonus['type']}}</dd>
                    <dt>Base Chance:</dt>
                    <dd>{{$classBonus['base_chance'] * 100}}%</dd>
                    <dt>Requirements:</dt>
                    <dd>{{$classBonus['requires']}}</dd>
                </dl>
                @if (!is_null(auth()->user()))
                    @if (auth()->user()->hasRole('Admin'))
                        <a href="{{route('classes.edit', [
                                'class' => $class
                            ])}}" class="btn btn-primary mt-2">Edit</a>
                    @endif
                @endif
            </div>
        </div>
    </x-core.cards.card-with-title>
    <x-core.cards.card-with-title title="Hints" css="tw-mt-5 tw-w-full lg:tw-w-1/2 tw-m-auto tw-mb-10">
        @if ($class->type()->isFighter())
            @include('information.classes.partials.fighter')
        @endif

        @if ($class->type()->isRanger())
            @include('information.classes.partials.ranger')
        @endif

        @if ($class->type()->isThief())
            @include('information.classes.partials.thief')
        @endif

        @if ($class->type()->isProphet())
            @include('information.classes.partials.prophet')
        @endif

        @if ($class->type()->isHeretic())
            @include('information.classes.partials.heretic')
        @endif

        @if ($class->type()->isVampire())
            @include('information.classes.partials.vampire')
        @endif
    </x-core.cards.card-with-title>
@endsection
