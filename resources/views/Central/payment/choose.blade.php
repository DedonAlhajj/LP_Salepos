@extends('Central.layout.main_guest1') @section('content')

@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('Register')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        {!! Form::open(['route' => '', 'method' => 'post', 'files' => true]) !!}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>{{trans('Name')}}</strong> </label>
                                        <input type="text" name="name" value="{{$registrationData->name}}" required class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label><strong>{{trans('Email')}}</strong></label>
                                        <input type="email" name="email" value="{{$registrationData->email}}" required class="form-control">
                                       
                                    </div>
                                   
                                    <div class="form-group">
                                        <label><strong>{{trans('Store Name')}}</strong> </label>
                                        <input type="text" name="store_name" value="{{$registrationData->store_name}}" required class="form-control">
                                    </div>
                                    
                                 
                                    <div class="form-group">
                                        <label><strong>{{trans('Domain')}}</strong></label>
                                        <input type="text" name="domain" value="{{$registrationData->domain}}" required class="form-control">
                                        
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" name="package_id" value="{{$registrationData->package_id}}" required class="form-control">
                                    </div>
                    
                                    <div class="form-group">
                                        <input type="number" name="price" value="50" required class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <input type="text" name="currency" value="dollar" required class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label><strong>{{trans('Payment Method')}} *</strong></label>
                                        <select name="Payment" required class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Payment Method...">
                                              <option value="">may fatura</option>
                                        </select>
                                    </div>



                                    <div class="form-group">
                                        <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
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


