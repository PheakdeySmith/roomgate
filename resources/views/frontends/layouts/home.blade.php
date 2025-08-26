@extends('frontends.layouts.app')

@section('title', $content['seo']->title ?? 'RoomGate - Home')

@section('content')

    @include('frontends.layouts.partials.hero')

    @include('frontends.layouts.partials.moving')

    @include('frontends.layouts.partials.benefits')

    @include('frontends.layouts.partials.features')

    @include('frontends.layouts.partials.about').

    @include('frontends.layouts.partials.running_line')

    @include('frontends.layouts.partials.pricing')
    
    @include('frontends.layouts.partials.faq')

    @include('frontends.layouts.partials.banner')

@endsection