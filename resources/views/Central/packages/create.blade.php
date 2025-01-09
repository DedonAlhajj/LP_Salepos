@extends('Central.layout.main_guest1') @section('content')

@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                  
                    <div class="card-body">
                    <div class="card-header d-flex align-items-center" style="width: 50%;">
                        <h4>{{trans('Add Package')}}</h4>
                    </div>

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
                                        <textarea type="text" name="description" required class="form-control"></textarea>
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
                                        <label><strong>{{trans('Features')}} *</strong></label>
                                        <select name="features[]" multiple="multiple" required class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Features...">
                                          @foreach($features as $f)
                                              <option value="{{$f->id}}">{{$f->description}}</option>
                                          @endforeach
                                        </select>
                                    </div>
                                  
                                    <div class="form-group">
                                        <input class="mt-2" type="checkbox" name="is_active" value="1" checked>
                                        <label class="mt-2"><strong>{{trans('file.Active')}}</strong></label>
                                    </div>

                                    
                                    <div class="form-group">
                                        <input class="mt-2" type="checkbox" name="is_trial" value="1" checked>
                                        <label class="mt-2"><strong>{{trans('Trial')}}</strong></label>
                                    </div>

                                    <div class="form-group">
                                        <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                                    </div>
                                </div>
                                {!! Form::close() !!}


                        <div class="col-md-6">
                         <div class="card">
                            <div class="card-header d-flex align-items-center" style="margin-top: -100px;">
                               <h4>{{trans('Add Feature')}}</h4>
                              </div>
                             <div class="card-body">
                                <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                                {!! Form::open(['route' => 'Central.features.store', 'method' => 'post', 'files' => true]) !!}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>{{trans('Description')}} *</strong></label>

                                        <textarea type="text" name="description" class="form-control"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                                    </div>

                                </div>

                                </div>
                                </div>
                                </div>
                            </div>
                    </div>
                    {!! Form::close() !!}

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

    @if(config('database.connections.saleprosaas_landlord'))
        numberOfUserAccount = <?php echo json_encode($numberOfUserAccount)?>;
        $.ajax({
            type: 'GET',
            async: false,
            url: '{{route("package.fetchData", $general_setting->package_id)}}',
            success: function(data) {
                if(data['number_of_user_account'] > 0 && data['number_of_user_account'] <= numberOfUserAccount) {
                    localStorage.setItem("message", "You don't have permission to create another user account as you already exceed the limit! Subscribe to another package if you wants more!");
                    location.href = "{{route('user.index')}}";
                }
            }
        });
    @endif

    $('#genbutton').on("click", function(){
      $.get('genpass', function(data){
        $("input[name='password']").val(data);
      });
    });

    $('select[name="role_id"]').on('change', function() {
        if($(this).val() == 5) {
            $('#biller-id').hide(300);
            $('#warehouseId').hide(300);
            $('.customer-section').show(300);
            $('.customer-input').prop('required',true);
            $('select[name="warehouse_id"]').prop('required',false);
            $('select[name="biller_id"]').prop('required',false);
        }
        else if($(this).val() > 2 && $(this).val() != 5) {
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
