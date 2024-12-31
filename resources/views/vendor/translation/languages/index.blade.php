@extends('backend.layout.main')
@push('css')

@endpush
@section('content')

    @if(count($languages))
    <div class="container-fluid mt-3 mb-3">
        <div class="card w-1/2">

            <div class="card-header d-flex justify-content-between">

                {{ __('translation::translation.languages') }}


                <a href="{{ route('languages.create') }}" class="btn btn-default">
                    {{ __('translation::translation.add') }}
                </a>

            </div>

            <div class="card-body">
            <div class="table-responsive">
                <table class="table ">

                    <thead>
                        <tr>
                            <th>{{ __('translation::translation.language_name') }}</th>
                            <th>{{ __('translation::translation.locale') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($languages as $language => $name)
                            <tr>
                                <td>
                                    {{ $name }}
                                </td>
                                <td>
                                    <a href="{{ route('languages.translations.index', $language) }}">
                                        {{ $language }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            </div>

        </div>
    </div>
    @endif

@endsection