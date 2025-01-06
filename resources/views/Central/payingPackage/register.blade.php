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
                        {!! Form::open(['route' => 'Central.register.storeT', 'method' => 'post', 'files' => true]) !!}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>{{trans('Name')}} *</strong> </label>
                                        <input type="text" name="name" required class="form-control">
                                        @if($errors->has('name'))
                                       <small>
                                           <strong>{{ $errors->first('name') }}</strong>
                                        </small>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label><strong>{{trans('Email')}} *</strong> </label>
                                        <input type="email" name="email" required class="form-control">
                                        @if($errors->has('email'))
                                       <small>
                                           <strong>{{ $errors->first('email') }}</strong>
                                        </small>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label><strong>{{trans('Password')}} *</strong> </label>
                                        <div class="input-group">
                                            <input type="password" name="password" required class="form-control">
                                           
                                    
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label><strong>{{trans('Store Name')}} *</strong> </label>
                                        <input type="text" name="store_name" required class="form-control">
                                        @if($errors->has('store_name'))
                                       <small>
                                           <strong>{{ $errors->first('store_name') }}</strong>
                                        </small>
                                        @endif
                                    </div>
                                    
                                 
                                    <div class="form-group">
                                        <label><strong>{{trans('Domain')}} *</strong></label>
                                        <input type="text" name="domain" required class="form-control">
                                        @if($errors->has('domain'))
                                       <small>
                                           <strong class="text-danger">{{ $errors->first('domain') }}</strong>
                                        </small>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" name="package_id" value="{{$packageId}}" required class="form-control">
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


