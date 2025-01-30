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
                        <h4>{{trans('file.Update User')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        {!! Form::open(['route' => ['user.update', $user->id], 'method' => 'put', 'files' => true]) !!}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>{{trans('file.UserName')}} *</strong> </label>
                                        <input type="text" name="name" required class="form-control" value="{{$user->name}}">
                                        @if($errors->has('name'))
                                       <span>
                                           <strong>{{ $errors->first('name') }}</strong>
                                        </span>
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
                                    <div class="form-group mt-3">
                                        <label><strong>{{trans('file.Email')}} *</strong></label>
                                        <input type="email" name="email" placeholder="example@example.com" required class="form-control" value="{{$user->email}}">
                                        @if($errors->has('email'))
                                       <span>
                                           <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group mt-3">
                                        <label><strong>{{trans('file.Phone Number')}} *</strong></label>
                                        <input type="text" name="phone" required class="form-control" value="{{$user->phone}}">
                                        @if($errors->has('phone'))
                                            <span>
                                           <strong>{{ $errors->first('phone') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        @if($user->is_active)
                                        <input class="mt-2" type="checkbox" name="is_active" value="1" checked>
                                        @else
                                        <input class="mt-2" type="checkbox" name="is_active" value="1">
                                        @endif
                                        <label class="mt-2"><strong>{{trans('file.Active')}}</strong></label>
                                            @if($errors->has('is_active'))
                                                <span>
                                           <strong>{{ $errors->first('is_active') }}</strong>
                                        </span>
                                            @endif
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>{{trans('file.Company Name')}}</strong></label>
                                        <input type="text" name="company_name" class="form-control" value="{{$user->company_name}}">
                                        @if($errors->has('company_name'))
                                            <span>
                                           <strong>{{ $errors->first('company_name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label><strong>{{trans('file.Role')}} *</strong></label>
                                        <input type="hidden" name="role_id_hidden" value="{{$user->roles->first()->name ?? ''}}">
                                        <select name="role" id="role-id" required class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Role...">
                                            @foreach($roles as $role)
                                                <option value="{{$role->name}}">{{$role->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group" id="biller-id">
                                        <label><strong>{{trans('file.Biller')}} *</strong></label>
                                        <input type="hidden" name="biller_id_hidden" value="{{$user->biller_id}}">
                                        <select name="biller_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Biller...">
                                            @foreach($billers as $biller)
                                                <option value="{{$biller->id}}">{{$biller->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group" id="warehouseId">
                                        <label><strong>{{trans('file.Warehouse')}} *</strong></label>
                                        <input type="hidden" name="warehouse_id_hidden" value="{{$user->warehouse_id}}">
                                        <select name="warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Warehouse...">
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
    $(document).ready(function () {
        // تعيين الدور الافتراضي عند تحميل الصفحة
        let roleIdHidden = $("input[name='role_id_hidden']").val();
        $("#role-id").val(roleIdHidden);

        // تعيين الـ Biller الافتراضي
        let billerIdHidden = $("input[name='biller_id_hidden']").val();
        $('select[name="biller_id"]').val(billerIdHidden);

        // تعيين الـ Warehouse الافتراضي
        let warehouseIdHidden = $("input[name='warehouse_id_hidden']").val();
        $('select[name="warehouse_id"]').val(warehouseIdHidden);

        // تحديث واجهة المستخدم
        $('.selectpicker').selectpicker('refresh');

        // التحكم في الحقول بناءً على الدور المختار
        $("#role-id").on('change', function () {
            let selectedRole = $(this).val();

            if (selectedRole !== 'Owner' && selectedRole !== 'Admin') {
                $('#biller-id').show();
                $('#warehouseId').show();
                $('select[name="biller_id"]').prop('required', true);
                $('select[name="warehouse_id"]').prop('required', true);
            } else {
                $('#biller-id').hide();
                $('#warehouseId').hide();
                $('select[name="biller_id"]').prop('required', false).val('');
                $('select[name="warehouse_id"]').prop('required', false).val('');
            }

            // تحديث واجهة selectpicker
            $('.selectpicker').selectpicker('refresh');
        });

        // تفعيل المنطق عند تحميل الصفحة
        $("#role-id").trigger('change');
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


</script>
@endpush
