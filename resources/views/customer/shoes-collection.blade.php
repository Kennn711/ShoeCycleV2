@extends('layouts/frontend/index')
@section('title', $shoe->name . ' | ShoeCycle')

@section('frontend-content')
    {{-- Breadcrumb --}}
    <div class="bg-gray-50 border-b border-gray-100 py-4">
        <div class="container mx-auto px-4">
            <div class="text-sm breadcrumbs text-gray-500">
                <ul>
                    <li><a href="{{ route('landing-page') }}">Beranda</a></li>
                    <li><a href="#">{{ $shoe->category->category_name }}</a></li>
                    <li class="font-bold text-gray-900 overflow-hidden text-ellipsis whitespace-nowrap max-w-[200px]">{{ $shoe->name }}</li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@push('styles')
@endpush

@push('scripts')
@endpush
