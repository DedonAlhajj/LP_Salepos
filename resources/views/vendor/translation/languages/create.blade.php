@extends('backend.layout.main')

@section('content')

@include('includes.session_message')

<div class="container-fluid mt-4">
    <div class="card">

        <div class="card-header">
            <h3 class="card-title">{{ __('Add Language') }}</h3>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('languages.store') }}" method="POST">

                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <label>{{ __('New Language Name')}}</label>
                <input type="text" name="name" class="form-control">
                <label>{{ __('New Language Short Key')}}</label>
                <input type="text" name="locale" class="form-control" placeholder="example: 'en', 'es' etc.">

                <button class="btn btn-primary mt-3">
                    {{ __('Save') }}
                </button>

            </form>
        </div>
    </div>
</div>

@endsection
