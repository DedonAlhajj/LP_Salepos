@extends('Central.layout.main_guest1') @section('content')

    @if(session()->has('not_permitted'))
        <div class="alert alert-danger alert-dismissible text-center">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
    @endif
    <section class="forms">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h4>{{trans('Register')}}</h4>
                        </div>
                    @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="card-body">
                            <p class="italic">
                                <small>{{trans('file.The field labels marked with * are required input fields')}}
                                    .</small></p>
                            {!! Form::open(['route' => 'Central.register.storeT', 'method' => 'post', 'files' => true]) !!}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>{{trans('Name')}} *</strong> </label>
                                        <input type="text" name="name" class="form-control">
                                        @error('name')
                                        <small>
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label><strong>{{trans('Email')}} *</strong> </label>
                                        <input type="email" name="email" required class="form-control">
                                        @error('email')
                                        <small>
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label><strong>{{trans('Password')}} *</strong> </label>

                                        <input type="password" name="password" required class="form-control">
                                        @error('password')
                                        <small>
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </small>
                                        @enderror

                                    </div>
                                    <div class="form-group">
                                        <label><strong>Password Confirmation *</strong> </label>

                                        <input type="password" name="password_confirmation" required
                                               class="form-control">
                                        @error('password_confirmation')
                                        <small>
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </small>
                                        @enderror

                                    </div>
                                    <div class="form-group">
                                        <label><strong>{{trans('Store Name')}} *</strong> </label>
                                        <input type="text" name="store_name" required class="form-control">
                                        @error('store_name')
                                        <small>
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </small>
                                        @enderror
                                    </div>


                                    <div class="form-group">
                                        <label><strong>{{trans('Domain')}} *</strong></label>
                                        <input type="text" name="domain" required class="form-control">
                                        @error('domain')
                                        <small>
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" name="package_id" value="{{$packageId}}" required
                                               class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" name="OperationType" value="purchase" required
                                               class="form-control">
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


