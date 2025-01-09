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
                        {!! Form::open(['route' => 'Central.packages.store', 'method' => 'post', 'files' => true]) !!}
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
                                        <label><strong>{{trans('Duration')}} *</strong> </label>
                                        <input type="text" name="duration" required class="form-control">
                                        @if($errors->has('duration'))
                                       <small>
                                           <strong>{{ $errors->first('duration') }}</strong>
                                        </small>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label><strong>{{trans('Duration Unit')}} *</strong> </label>
                                        <select name="unit" required class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Unit...">
                                              <option value="">Days</option>
                                              <option value="">Monthes</option>
                                              <option value="">Years</option>

                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label><strong>{{trans('Price')}} *</strong> </label>
                                        <input type="number" name="price" required class="form-control">
                                        @if($errors->has('price'))
                                       <small>
                                           <strong>{{ $errors->first('price') }}</strong>
                                        </small>
                                        @endif
                                    </div>
                                    
                                    
                                    <div class="form-group">
                                        <label><strong>{{trans('Description')}} *</strong></label>
                                        <input type="text" name="description" required class="form-control">
                                        @if($errors->has('description'))
                                       <small>
                                           <strong class="text-danger">{{ $errors->first('description') }}</strong>
                                        </small>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label><strong>{{trans('Max_Users')}} *</strong></label>
                                        <input type="number" name="max_users" required class="form-control">
                                        @if($errors->has('max_users'))
                                       <small>
                                           <strong class="text-danger">{{ $errors->first('max_users') }}</strong>
                                        </small>
                                        @endif
                                    </div>
                                    
                                    <div class="form-group">
                                        <label><strong>{{trans('Max_Storage')}} *</strong></label>
                                        <input type="text" name="max_storage" required class="form-control">
                                        @if($errors->has('max_storage'))
                                       <small>
                                           <strong class="text-danger">{{ $errors->first('max_storage') }}</strong>
                                        </small>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label><strong>{{trans('Ttial')}} *</strong></label>
                                        <label>
                                        <input type="radio" name="trial" value="1" required>
                                        yes
                                        </label>
                                        <br>
                                        <label>
                                        <input type="radio" name="trial" value="0">
                                        No
                                        </label>
                                        <br>
                                    </div>


                                    <div class="form-group">
                                        <label><strong>{{trans('Active')}} *</strong></label>
                                        <label>
                                        <input type="radio" name="active" value="1" required>
                                        yes
                                        </label>
                                        <br>
                                        <label>
                                        <input type="radio" name="active" value="0">
                                        No
                                        </label>
                                        <br>
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


