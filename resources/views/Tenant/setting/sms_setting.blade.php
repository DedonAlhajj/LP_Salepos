@extends('Tenant.layout.main') @section('content')
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.SMS Setting')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        {!! Form::open(['route' => 'setting.smsStore', 'method' => 'post']) !!}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="hidden" name="type" value="sms">
                                        <input type="hidden" id="smsId" name="sms_id">

                                        @if(isset($smsSettings['revesms']))
                                            <input type="hidden" id="revesmsId" value="{{ $smsSettings['revesms']['sms_id'] }}">
                                        @endif
                                        @if(isset($smsSettings['bdbulksms']))
                                            <input type="hidden" id="bdbulksmsId" value="{{ $smsSettings['bdbulksms']['sms_id'] }}">
                                        @endif
                                        @if(isset($smsSettings['tonkra']))
                                            <input type="hidden" id="tonkraId" value="{{ $smsSettings['tonkra']['sms_id'] }}">
                                        @endif
                                        @if(isset($smsSettings['twilio']))
                                            <input type="hidden" id="twilioId" value="{{ $smsSettings['twilio']['sms_id'] }}">
                                        @endif
                                        @if(isset($smsSettings['clickatell']))
                                            <input type="hidden" id="clickatellId" value="{{ $smsSettings['clickatell']['sms_id'] ?? '' }}">
                                        @endif

                                        <input type="hidden" name="gateway_hidden" value="">

                                        <label>{{trans('file.Gateway')}} *</label>
                                        <select class="form-control" name="gateway">
                                            <option selected disabled>{{trans('file.Select SMS gateway...')}}</option>

                                            @if(isset($smsSettings['revesms']))
                                                <option value="revesms" data-active="{{ $smsSettings['revesms']['active'] }}"
                                                    {{ $smsSettings['revesms']['active'] ? 'selected' : '' }}>revesms</option>

                                            @else
                                                <option value="revesms" data-active="">revesms</option>
                                            @endif

                                            @if(isset($smsSettings['bdbulksms']))
                                                <option value="bdbulksms" data-active="{{ $smsSettings['bdbulksms']['active'] }}"
                                                    {{ $smsSettings['bdbulksms']['active'] ? 'selected' : '' }}>bdbulksms</option>
                                            @else
                                                <option value="bdbulksms" data-active="">bdbulksms</option>
                                            @endif

                                            @if(isset($smsSettings['tonkra']))
                                                <option value="tonkra" data-active="{{ $smsSettings['tonkra']['active'] }}"
                                                    {{ $smsSettings['tonkra']['active'] ? 'selected' : '' }}>Tonkra</option>
                                            @else
                                                <option value="tonkra" data-active="">Tonkra</option>
                                            @endif

                                            @if(isset($smsSettings['twilio']))
                                                <option value="twilio" data-active="{{ $smsSettings['twilio']['active'] }}"
                                                    {{ $smsSettings['twilio']['active'] ? 'selected' : '' }}>Twilio</option>
                                            @else
                                                <option value="twilio" data-active="">Twilio</option>
                                            @endif

                                            @if(isset($smsSettings['clickatell']))
                                                <option value="clickatell" data-active="{{ $smsSettings['clickatell']['active'] }}"
                                                    {{ $smsSettings['clickatell']['active'] ? 'selected' : '' }}>Clickatell</option>
                                            @else
                                                <option value="clickatell" data-active="">Clickatell</option>
                                            @endif
                                        </select>
                                    </div>


                                    <div class="form-group bdbulksms">
                                        <label>Token *</label>
                                        @if(isset($smsSettings['bdbulksms']))
                                            <input type="text" name="token" class="form-control bdbulksms-option"
                                                   value="{{ $smsSettings['bdbulksms']['token'] }}" />
                                        @else
                                            <input type="text" name="token" class="form-control bdbulksms-option"
                                                   value="" />
                                        @endif
                                    </div>

                                    <div class="form-group revesms">
                                        <label>API Key *</label>
                                        @if(isset($smsSettings['revesms']))
                                            <input type="text" name="apikey" class="form-control revesms-option"
                                                   value="{{ $smsSettings['revesms']['apikey'] }}" />
                                        @else
                                            <input type="text" name="apikey" class="form-control revesms-option"
                                                   value="" />
                                        @endif
                                    </div>

                                    <div class="form-group revesms">
                                        <label>Secret Key *</label>
                                        @if(isset($smsSettings['revesms']))
                                            <input type="text" name="secretkey" class="form-control revesms-option"
                                                   value="{{ $smsSettings['revesms']['secretkey'] }}" />
                                        @else
                                            <input type="text" name="secretkey" class="form-control revesms-option"
                                                   value="" />
                                        @endif
                                    </div>

                                    <div class="form-group revesms">
                                        <label>Caller ID *</label>
                                        @if(isset($smsSettings['revesms']))
                                            <input type="text" name="callerID" class="form-control revesms-option"
                                                   value="{{ $smsSettings['revesms']['callerID'] }}" />
                                        @else
                                            <input type="text" name="callerID" class="form-control revesms-option"
                                                   value="" />
                                        @endif
                                    </div>

                                    <div class="form-group tonkra">
                                        <label>API Token *</label>
                                        @if(isset($smsSettings['tonkra']))
                                            <input type="text" name="api_token" class="form-control tonkra-option"
                                                   value="{{ $smsSettings['tonkra']['api_token'] }}" />
                                        @else
                                            <input type="text" name="api_token" class="form-control tonkra-option"
                                                   value="" />
                                        @endif
                                    </div>

                                    <div class="form-group tonkra">
                                        <label>Sender ID *</label>
                                        @if(isset($smsSettings['tonkra']))
                                            <input type="text" name="sender_id" class="form-control tonkra-option"
                                                   value="{{ $smsSettings['tonkra']['sender_id'] }}" />
                                        @else
                                            <input type="text" name="sender_id" class="form-control tonkra-option"
                                                   value="" />
                                        @endif
                                    </div>

                                    <div class="form-group twilio">
                                        <label>ACCOUNT SID *</label>
                                        @if(isset($smsSettings['twilio']))
                                            <input type="text" name="account_sid" class="form-control twilio-option"
                                                   value="{{ $smsSettings['twilio']['account_sid'] ?? '' }}" />
                                        @else
                                            <input type="text" name="account_sid" class="form-control twilio-option"
                                                   value="" />
                                        @endif
                                    </div>

                                    <div class="form-group twilio">
                                        <label>AUTH TOKEN *</label>
                                        @if(isset($smsSettings['twilio']))
                                            <input type="text" name="auth_token" class="form-control twilio-option"
                                                   value="{{ $smsSettings['twilio']['auth_token'] ?? '' }}" />
                                        @else
                                            <input type="text" name="auth_token" class="form-control twilio-option"
                                                   value="" />
                                        @endif
                                    </div>

                                    <div class="form-group twilio">
                                        <label>Twilio Number *</label>
                                        @if(isset($smsSettings['twilio']))
                                            <input type="text" name="twilio_number" class="form-control twilio-option"
                                                   value="{{ $smsSettings['twilio']['twilio_number'] ?? '' }}" />
                                        @else
                                            <input type="text" name="twilio_number" class="form-control twilio-option"
                                                   value="" />
                                        @endif
                                    </div>

                                    <div class="form-group clickatell">
                                        <label>API Key *</label>
                                        @if(isset($smsSettings['clickatell']))
                                            <input type="text" name="api_key" class="form-control clickatell-option"
                                                   value="{{ $smsSettings['clickatell']['api_key'] ?? '' }}" />
                                        @else
                                            <input type="text" name="api_key" class="form-control clickatell-option"
                                                   value="" />
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <input class="mt-2 default" type="checkbox" name="active" value="1">
                                        <label class="mt-2"><strong>{{trans('file.Default')}}</strong></label>
                                      </div>
                                    <div class="form-group">
                                        <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                                        <a href="https://sms.tonkra.com/account/top-up" type="button" target="_blank" class="btn btn-secondary tonkra">{{ trans('file.Top Up') }}</a>
                                    </div>
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#setting").siblings('a').attr('aria-expanded','true');
    $("ul#setting").addClass("show");
    $("ul#setting #sms-setting-menu").addClass("active");

    $(document).ready(function(){
        var selectedOption = $(this).find(':selected').val();
        if( selectedOption == 'twilio' ){
            $('select[name="gateway"]').val('twilio');
            $('.clickatell').hide();
            $('.tonkra').hide();
            $('.revesms').hide();
            $('.bdbulksms').hide();
            var dataAtive = $(this).find(':selected').data('active');
            dataAtive == true ? $(".default").prop("checked",true) : $(".default").prop("checked", false);
        }
        if( selectedOption == 'revesms' ){
            $('select[name="gateway"]').val('revesms');
            $('.clickatell').hide();
            $('.tonkra').hide();
            $('.bdbulksms').hide();
            $('.twilio').hide();
            var dataAtive = $(this).find(':selected').data('active');
            dataAtive == true ? $(".default").prop("checked",true) : $(".default").prop("checked", false);
        }
        else if(selectedOption == 'clickatell' ){
            $('select[name="gateway"]').val('clickatell');
            $('.twilio').hide();
            $('.tonkra').hide();
            $('.revesms').hide();
            $('.bdbulksms').hide();
            var dataAtive = $(this).find(':selected').data('active');
            dataAtive == true ? $(".default").prop("checked",true) : $(".default").prop("checked", false);
        }
        else if( selectedOption == 'tonkra' ){
            $('select[name="gateway"]').val('tonkra');
            $('.twilio').hide();
            $('.clickatell').hide();
            $('.revesms').hide();
            $('.bdbulksms').hide();
            var dataAtive = $(this).find(':selected').data('active');
            dataAtive == true ? $(".default").prop("checked",true) : $(".default").prop("checked", false);
        }
        else if( selectedOption == 'bdbulksms' ){
            $('select[name="gateway"]').val('bdbulksms');
            $('.twilio').hide();
            $('.clickatell').hide();
            $('.revesms').hide();
            $('.tonkra').hide();
            var dataAtive = $(this).find(':selected').data('active');
            dataAtive == true ? $(".default").prop("checked",true) : $(".default").prop("checked", false);
        }
        else{
            $('.clickatell').hide();
            $('.twilio').hide();
            $('.tonkra').hide();
            $('.revesms').hide();
            $('.bdbulksms').hide();
        }
    });

    $('select[name="gateway"]').on('change', function(){
        if( $(this).val() == 'twilio' ){
            $('.clickatell').hide();
            $('.tonkra').hide();
            $('.revesms').hide();
            $('.bdbulksms').hide();
            $('.twilio').show(500);
            $('.twilio-option').prop('required',true);
            $('.clickatell-option').prop('required',false);
            $('.tonkra-option').prop('required',false);
            $('.revesms-option').prop('required',false);
            $('.bdbulksms-option').prop('required',false);
            $('#smsId').val($('#twilioId').val());
            var selectedOption = $(this).find(':selected');
            var dataId = selectedOption.data('active');
            dataId == true ? $(".default").prop("checked",true) : $(".default").prop("checked", false);
        }
        else if( $(this).val() == 'clickatell' ){
            $('.twilio').hide();
            $('.tonkra').hide();
            $('.revesms').hide();
            $('.clickatell').show(500);
            $('.bdbulksms').hide();
            $('.bdbulksms-option').prop('required',false);
            $('.twilio-option').prop('required',false);
            $('.revesms-option').prop('required',false);
            $('.tonkra-option').prop('required',false);
            $('.clickatell-option').prop('required',true);
            $('#smsId').val($('#clickatellId').val());
            var selectedOption = $(this).find(':selected');
            var dataId = selectedOption.data('active');
            dataId == true ? $(".default").prop("checked",true) : $(".default").prop("checked", false);
        }
        else if( $(this).val() == 'tonkra' ){
            $('.twilio').hide();
            $('.clickatell').hide();
            $('.revesms').hide();
            $('.tonkra').show(500);
            $('.bdbulksms').hide();
            $('.bdbulksms-option').prop('required',false);
            $('.tonkra-option').prop('required',true);
            $('.twilio-option').prop('required',false);
            $('.clickatell-option').prop('required',false);
            $('.revesms-option').prop('required',false);
            $('#smsId').val($('#tonkraId').val());
            var selectedOption = $(this).find(':selected');
            var dataId = selectedOption.data('active');
            dataId == true ? $(".default").prop("checked",true) : $(".default").prop("checked", false);
        }
        else if( $(this).val() == 'revesms' ){
            $('.twilio').hide();
            $('.clickatell').hide();
            $('.tonkra').hide();
            $('.revesms').show(500);
            $('.bdbulksms').hide();
            $('.bdbulksms-option').prop('required',false);
            $('.revesms-option').prop('required',true);
            $('.twilio-option').prop('required',false);
            $('.clickatell-option').prop('required',false);
            $('.tonkra-option').prop('required',false);
            $('#smsId').val($('#revesmsId').val());
            var selectedOption = $(this).find(':selected');
            var dataId = selectedOption.data('active');
            dataId == true ? $(".default").prop("checked",true) : $(".default").prop("checked", false);
        }
        else if( $(this).val() == 'bdbulksms' ){
            $('.twilio').hide();
            $('.clickatell').hide();
            $('.tonkra').hide();
            $('.revesms').hide();
            $('.bdbulksms').show(500);
            $('.bdbulksms-option').prop('required',true);
            $('.revesms-option').prop('required',false);
            $('.twilio-option').prop('required',false);
            $('.clickatell-option').prop('required',false);
            $('.tonkra-option').prop('required',false);
            $('#smsId').val($('#bdbulksmsId').val());
            var selectedOption = $(this).find(':selected');
            var dataId = selectedOption.data('active');
            dataId == true ? $(".default").prop("checked",true) : $(".default").prop("checked", false);
        }
    });

</script>
@endpush
