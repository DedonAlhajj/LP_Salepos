@extends('backend.layout.main') 
@section('content')

<section>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header mt-2">
                <h3 class="text-center">{{trans('file.SMS Template List')}}</h3>
            </div>
        </div>
        <button class="btn btn-info" data-toggle="modal" data-target="#smstemplates-modal"><i class="dripicons-plus"></i> {{trans('file.Add Template')}}</button>
    </div>

    <div class="table-responsive">
        <table id="template-table" class="table" style="width: 100%">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.name')}}</th>
                    <th>{{trans('file.Content')}}</th>
                    <th>{{trans('file.Default')}}</th>
                    <th>{{trans('file.Default Online')}}</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $template)
                    <tr>
                        <td></td>
                        <td>{{ $template->name }}</td>
                        <td>{{ $template->content }}</td>
                        <td>
                            @if ($template->is_default)
                                <span class="badge badge-success">Default</span>
                            @endif
                        </td>
                        <td>
                            @if ($template->is_default_ecommerce)
                                <span class="badge badge-success">Default</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{trans('file.action')}}
                                  <span class="caret"></span>
                                  <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                    <li>
                                        <button type="button" data-id="{{$template->id}}" data-name="{{$template->name}}" data-content="{{$template->content}}" data-is_default="{{$template->is_default}}" data-is_default_ecommerce="{{$template->is_default_ecommerce}}"class="edit-btn btn btn-link" data-toggle="modal" data-target="#editModal" ><i class="dripicons-document-edit"></i>  {{trans('file.edit')}}</button>
                                    </li>
                                    <li class="divider"></li>
                                    {{ Form::open(['route' => ['smstemplates.destroy', $template->id], 'method' => 'DELETE'] ) }}
                                    <li>
                                      <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> {{trans('file.delete')}}</button>
                                    </li>
                                    {{ Form::close() }}
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<!-- create modal -->
<div id="smstemplates-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Add Template')}}</h5>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
          <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
          {!! Form::open(['route' => 'smstemplates.store', 'method' => 'post']) !!}  
          <form action="{{route('smstemplates.store')}}" method="POST">
          <div class="row">
            <div class="col-md-12 form-group">
              <label>{{trans('file.name')}}*</label>
              <input type="text" name="name" class="form-control " placeholder="Template Name" />
            </div>
          </div>
          <div class="form-group">
            <label>{{trans('file.Content')}}*</label>
            <textarea type="text" name="content" rows="7" placeholder="You can set following dynamic tags for a template:
[reference], [customer], [sale_status], [payment_status] 
Example: 
Hi [customer],
Thanks for the order. Order reference: [reference]. Order status: [sale_status]. Payment status: [payment_status].
" class="form-control"></textarea>
          </div>
          <div class="form-group">
            <input class="mt-2" type="checkbox" name="is_default" value="1">
            <label class="mt-2"><strong>{{trans('file.Default SMS Sale')}}</strong></label>
          </div>
          <div class="form-group">
            <input class="mt-2" type="checkbox" name="is_default_ecommerce" value="1">
            <label class="mt-2"><strong>{{trans('file.Default SMS E-Commerce')}}</strong></label>
          </div>
          <div class="form-group">
            <button type="submit" class="btn btn-primary">{{trans('file.submit')}}</button>
          </div>
          {{ Form::close() }}
        </div>
      </div>
    </div>
</div>
<!-- edit modal -->
<div id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Update Template')}}</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
              <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                {!! Form::open(['route' => ['smstemplates.update', 1], 'method' => 'put']) !!}
                    <div class="row">
                        <div class="col-md-12 form-group">
	      			        <input type="hidden" name="smstemplate_id">
                            <label>{{trans('file.name')}}</label>
                            <input type="text" name="name" value="" class="form-control " placeholder="Template Name"/>
                        </div>
                    </div>
                  <div class="form-group">
                      <label>{{trans('file.Content')}}</label>
                      <textarea name="content" rows="3" class="form-control"></textarea>
                  </div>
                  <div class="form-group">
                    <input class="mt-2" type="checkbox" name="is_default" value="1">
                    <label class="mt-2"><strong>{{trans('file.Default SMS Sale')}}</strong></label>
                  </div>
                  <div class="form-group">
                    <input class="mt-2" type="checkbox" class="is_default_ecommerce" name="is_default_ecommerce" value="1">
                    <label class="mt-2"><strong>{{trans('file.Default SMS E-Commerce')}}</strong></label>
                  </div>
                  <div class="form-group">
                      <button type="submit" class="btn btn-primary">{{trans('file.submit')}}</button>
                  </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        $(document).on('click', '.edit-btn', function(){
            $("#editModal input[name='smstemplate_id']").val($(this).data('id'));
            $("#editModal input[name='name']").val($(this).data('name'));
            $("#editModal textarea[name='content']").val($(this).data('content'));
            $(this).data('is_default') == true ? $("#editModal input[name='is_default']").prop("checked",true) : $("#editModal input[name='is_default']").prop("checked", false);
            $(this).data('is_default_ecommerce') == true ? $("#editModal input[name='is_default_ecommerce']").prop("checked",true) : $("#editModal input[name='is_default_ecommerce']").prop("checked", false);
        });
    });

    $('#template-table').DataTable( {
        "order": [],
        'language': {
            'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
             "info":      '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search":  '{{trans("file.Search")}}',
            'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 3]
            },
            {
                'render': function(data, type, row, meta){
                    if(type === 'display'){
                        data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
                    }

                   return data;
                },
                'checkboxes': {
                   'selectRow': true,
                   'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
                },
                'targets': [0]
            }
        ],
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'pdf',
                text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                }
            },
            {
                extend: 'excel',
                text: '<i title="export to excel" class="dripicons-document-new"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                }
            },
            {
                extend: 'csv',
                text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                }
            },
            {
                extend: 'print',
                text: '<i title="print" class="fa fa-print"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                }
            },
            {
                extend: 'colvis',
                text: '<i title="column visibility" class="fa fa-eye"></i>',
                columns: ':gt(0)'
            },
        ],
    } );
</script>
@endpush