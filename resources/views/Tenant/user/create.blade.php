@extends('Tenant.layout.main') @section('content')

@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.Add User')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        {!! Form::open(['route' => 'user.store', 'method' => 'post', 'files' => true]) !!}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>{{trans('file.UserName')}} *</strong> </label>
                                        <input type="text" name="name" required class="form-control">
                                        @if($errors->has('name'))
                                       <small>
                                           <strong>{{ $errors->first('name') }}</strong>
                                        </small>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label><strong>{{trans('file.Password')}} *</strong></label>
                                        <div class="input-group">
                                            <input id="password" type="password" name="password" required class="form-control">
                                            <div class="input-group-append">
                                                <button id="genbutton" type="button" class="btn btn-default">{{trans('file.Generate')}}</button>
                                            </div>
                                            @if($errors->has('password'))
                                                <small>
                                                    <strong>{{ $errors->first('password') }}</strong>
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label><strong>{{trans('file.Confirm Password')}} *</strong></label>
                                        <div class="input-group">
                                            <input id="password_confirmation" type="password" name="password_confirmation" required class="form-control">
                                            @if($errors->has('password'))
                                                <small>
                                                    <strong>{{ $errors->first('password') }}</strong>
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label><strong>{{trans('file.Email')}} *</strong></label>
                                        <input type="email" name="email" placeholder="example@example.com" required class="form-control">
                                        @if($errors->has('email'))
                                       <small>
                                           <strong class="text-danger">{{ $errors->first('email') }}</strong>
                                        </small>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label><strong>{{trans('file.Phone Number')}} *</strong></label>
                                        <input type="text" name="phone_number" required class="form-control">
                                        @if($errors->has('phone_number'))
                                            <small>
                                               <strong>{{ $errors->first('phone_number') }}</strong>
                                            </small>
                                        @endif
                                    </div>
                                    <div class="customer-section">
                                        <div class="form-group">
                                            <label><strong>{{trans('file.Address')}} *</strong></label>
                                            <input type="text" name="address" class="form-control customer-input">
                                        </div>
                                        <div class="form-group">
                                            <label><strong>{{trans('file.State')}}</strong></label>
                                            <input type="text" name="state" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label><strong>{{trans('file.Country')}}</strong></label>
                                            <input type="text" name="country" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input class="mt-2" type="checkbox" name="is_active" value="1" checked>
                                        <label class="mt-2"><strong>{{trans('file.Active')}}</strong></label>
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>{{trans('file.Company Name')}}</strong></label>
                                        <input type="text" name="company_name" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label><strong>{{trans('file.Role')}} *</strong></label>
                                        <select name="role" id="role-id" required class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Role...">
                                          @foreach($roles as $role)
                                              <option value="{{$role->name}}">{{$role->name}}</option>
                                          @endforeach
                                        </select>
                                    </div>
                                    <div class="customer-section">
                                        <div class="form-group">
                                            <label><strong>{{trans('file.Customer Group')}} *</strong></label>
                                            <select name="customer_group_id" class="selectpicker form-control customer-input" data-live-search="true" data-live-search-style="begins" title="Select customer_group...">
                                              @foreach($customerGroups as $customer_group)
                                                  <option value="{{$customer_group->id}}">{{$customer_group->name}}</option>
                                              @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>{{trans('file.name')}} *</strong></label>
                                            <input type="text" name="customer_name" class="form-control customer-input">
                                        </div>
                                        <div class="form-group">
                                            <label><strong>{{trans('file.Tax Number')}}</strong></label>
                                            <input type="text" name="tax_number" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label><strong>{{trans('file.City')}} *</strong></label>
                                            <input type="text" name="city" class="form-control customer-input">
                                        </div>
                                        <div class="form-group">
                                            <label><strong>{{trans('file.Postal Code')}}</strong></label>
                                            <input type="text" name="postal_code" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group" id="biller-id">
                                        <label><strong>{{trans('file.Biller')}} *</strong></label>
                                        <select name="biller_id" required class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Biller...">
                                          @foreach($billers as $biller)
                                              <option value="{{$biller->id}}">{{$biller->name}}</option>
                                          @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group" id="warehouseId">
                                        <label><strong>{{trans('file.Warehouse')}} *</strong></label>
                                        <select name="warehouse_id" required class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Warehouse...">
                                          @foreach($warehouses as $warehouse)
                                              <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                          @endforeach
                                        </select>
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

    $("ul#people").siblings('a').attr('aria-expanded','true');
    $("ul#people").addClass("show");
    $("ul#people #user-create-menu").addClass("active");

    $('#warehouseId').hide();
    $('#biller-id').hide();
    $('.customer-section').hide();

    $('.selectpicker').selectpicker({
      style: 'btn-link',
    });



    // وظيفة لإنشاء كلمة سر عشوائية
    function generatePassword(length = 12) {
        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$&!";
        let password = "";
        for (let i = 0; i < length; i++) {
            const randomIndex = Math.floor(Math.random() * charset.length);
            password += charset[randomIndex];
        }
        return password;
    }

    // إضافة حدث عند الضغط على الزر
    document.getElementById('genbutton').addEventListener('click', function () {
        const generatedPassword = generatePassword(); // إنشاء كلمة السر
        document.getElementById('password').value = generatedPassword; // تعبئة حقل كلمة المرور
        document.getElementById('password_confirmation').value = generatedPassword; // تعبئة حقل تأكيد كلمة المرور
    });

    $("#role-id").on('change', function() {
        if($(this).val() == 'Customer') {
            $('#biller-id').hide(300);
            $('#warehouseId').hide(300);
            $('.customer-section').show(300);
            $('.customer-input').prop('required',true);
            $('select[name="warehouse_id"]').prop('required',false);
            $('select[name="biller_id"]').prop('required',false);
        }
        else if($(this).val() !== 'Owner' && $(this).val() !== 'Admin' && $(this).val() != 'Customer') {
            $('select[name="warehouse_id"]').prop('required',true);
            $('select[name="biller_id"]').prop('required',true);
            $('#biller-id').show(300);
            $('#warehouseId').show(300);
            $('.customer-section').hide(300);
            $('.customer-input').prop('required',false);
        }
        else {
            $('select[name="warehouse_id"]').prop('required',false);
            $('select[name="biller_id"]').prop('required',false);
            $('#biller-id').hide(300);
            $('#warehouseId').hide(300);
            $('.customer-section').hide(300);
            $('.customer-input').prop('required',false);
        }
    });
</script>
@endpush
