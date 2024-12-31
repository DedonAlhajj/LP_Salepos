@extends('backend.layout.main')
@push('css')

@endpush
@section('content')

    @include('includes.session_message')


    <form action="{{ route('languages.translations.index', ['language' => $language]) }}" method="get">

        <div class="container-fluid mt-3 mb-3">
            <div class="d-flex">

                <a href="{{ route('languages.create') }}" class="btn btn-primary mr-1">{{ __('Add') }}</a>
                <div class="w-20">
                    @include('vendor.translation.forms.select', ['name' => 'language', 'items' => $languages, 'submit' => true, 'selected' => $language])
                </div>


                <div class="ml-3 w-20">
                    <select class="form-control" id='lang_del'>
                        <option selected="selected">Delete Language</option>
                        <option>--------------------------</option>
                        @foreach($languages as $lang)
                            <option value="{{$lang}}">{{$lang}}</option>
                        @endforeach
                  </select>
                </div>

            </div>
        </div>


        @if(count($translations))

            <div class="table-responsive">
                <table id="language-table" class="table ">

                    <thead>
                    <tr>
                        <th class="w-1/5 uppercase font-thin">{{ __('translation::translation.key') }}</th>
                        <th class="uppercase font-thin">{{ config('app.locale') }}</th>
                        <th class="uppercase font-thin">{{ $language }}</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($translations as $type => $items)
                        @foreach($items as $group => $translationsData)
                            @foreach($translationsData as $key => $value)
                                @if(!is_array($value[config('app.locale')]))
                                    <tr>
                                        <td>{{ $key }}</td>
                                        <td>{{ $value[config('app.locale')] }}</td>
                                        <td class="d-flex">
                                            <textarea class="edit_textarea form-control">{{ $value[$language] }}</textarea>
                                            <button class="update_btn btn btn-primary btn-sm" style="display: none;" type="button" data-key="{{ $key }}" data-language="{{ $language }}" data-group="{{ $group }}" title="Update"><i class="dripicons-pencil" aria-hidden="true"></i></button><br>
                                            <button class="check_icon btn btn-success btn-sm" style="display: none;"><i class="dripicons-checkmark" aria-hidden="true"></i></button>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </form>

@endsection

@push('scripts')

<script type="text/javascript">
    (function($) {
        "use strict";

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).ready(function () {

            var dataSrc = [];

            var table = $('#language-table').DataTable({
                "order": [],
                'language': {
                    'lengthMenu': '_MENU_ {{__("records per page")}}',
                    "info": '{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)',
                    "search": '{{trans("file.Search")}}',
                    'paginate': {
                        'previous': '{{trans("file.Previous")}}',
                        'next': '{{trans("file.Next")}}'
                    }
                },
                'select': {style: 'multi', selector: 'td:first-child'},
                'lengthMenu': [[100, 200, 500,-1], [100, 200, 500,"All"]],
            });


            $(document).on('click','.edit_textarea',function(){
                $(".update_btn").hide(); //for all
                $(this).siblings('.update_btn').show();
            });

            $(document).on('click','.update_btn',function(){
                var language = $(this).data('language');
                var key   = $(this).data('key');
                var group = $(this).data('group');
                var value = $(this).siblings('textarea').val();

                $(this).siblings(".update_btn").hide();
                $(this).siblings('.check_icon').show();

                $.ajax({
                    url: "{{ route('language.translations.update') }}",
                    type: "POST",
                    data: {
                        language:language,
                        key:key,
                        group:group,
                        value:value
                    },
                    success: function (data) {
                        $(".update_btn").hide();
                        console.log(data);
                        setTimeout(function() {
                            $('.check_icon').fadeOut("slow");
                        }, 3000);
                    }
                });
            });

        });


        $(document).ready(function() {
            $("#localeChange").change(function(){
                var localeName = $('#localeChange :selected').text()
                console.log(localeName);
                var baseUrl = "{{url('/')}}";
                var path    = `languages/${localeName}/translations`;
                var url     = baseUrl + '/' + path;
                window.location.href = url;
            });
        });


        $(document).ready(function() {
            $("#lang_del").change(function(){
                var proceed = confirm("Are You Sure To Delete ?");
                if (proceed) {
                    var langVal = $('#lang_del :selected').text()
                    $.ajax({
                        url: "{{route('language.delete')}}",
                        method: "GET",
                        data: {langVal:langVal},
                        success: function (data) {
                            console.log(data);
                            if (data === 'success' || data ==='error') {
                                var baseUrl = "{{url('/')}}";
                                var path    = 'languages/';
                                var url     = baseUrl + '/' + path;
                                window.location.href = url;
                            }
                        }
                    })
                }
            });
        });
    })(jQuery);
</script>

@endpush



