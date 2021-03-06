@extends('layouts.customerlay')
@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="http://bootstrap-tagsinput.github.io/bootstrap-tagsinput/dist/bootstrap-tagsinput.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" />
<style>
    .required:after {
        content: "*";
        color: red;
    }
    .error{
        color:red;
    }
table.dataTable tbody td {
    word-break: break-word;
    vertical-align: top;
}
.bootstrap-tagsinput .tag {
        background: #3bafda;
        border: 1px solid #3bafda;
        padding: 0 6px;
        margin-right: 2px;
        color: white;
        border-radius: 4px;
    }
    .bootstrap-tagsinput {
        width: 100% !important;
        height: calc(2.75rem + 2px) !important;
    }
    .select2-container{
        display: inline !important;
    }
</style>
<div class="app-content content">
    <div class="content-wrapper">
        <br>
        @include('alert.messages')
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Value Journals</h4>
                        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered dom-jQuery-events dataTable" id="DataTables" role="grid" aria-describedby="DataTables_Table_0_info">
                                <thead>
                                    <tr role="row">
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($list as $item)
                                    <tr role="row" class="odd">
                                        <td>{{$item->journal}}</td>
                                        <td>
                                            {{\Carbon\Carbon::parse($item->journal_date)->format('d/m/Y')}}
                                        </td>
                                        <td><h4 @if($item->status==1) class="danger text-center" @else class="success text-center" @endif>{{($item->status==1)?"Inactive":"Active"}}</h4></td>
                                        <td>
                                            <a class="btn btn-primary text-white tab-order" data-toggle="modal" data-target="#editJournalModal{{$item->id}}"  href="#"><i class="icon-pencil"></i> View</a>
                                            <a class="btn btn-warning text-white tab-order" href="{{url('customer/sub/journals')}}/{{$item->id}}"><i class="icon-list"></i> Sub List</a>
                                            
                                        </td>
                                    </tr>
                                    <div class="modal fade text-left show" id="editJournalModal{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" style="padding-right: 17px;">
                                        <div class="modal-dialog" role="document">
                                          <div class="modal-content">
                                            <div class="modal-header">
                                              <h3 class="modal-title" id="myModalLabel35"> View Details of {{$item->journal}}</h3>
                                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">??</span>
                                              </button>
                                            </div>
                                              <div class="modal-body">
                                                    <fieldset class="form-group floating-label-form-group">
                                                      <label for="email" class="label-control required">Name</label>
                                                      <input type="text" class="form-control" id="editname" name="editname" value="{{$item->journal}}" >
                                                        @if ($errors->has('editname'))
                                                            <span class="help-block">
                                                                <strong class="error">{{ $errors->first('editname') }}</strong>
                                                            </span>
                                                        @endif
                                                    </fieldset>
                                                    <fieldset class="form-group floating-label-form-group">
                                                        <label for="email" class="label-control">Description</label>
                                                        <textarea cols="30" rows="15" class="form-control detail2" id="detail1_{{$item->id}}" name="detail2" placeholder="Details about journal">{!! $item->description !!}</textarea>
                                                          @if ($errors->has('detail1'))
                                                              <span class="help-block">
                                                                  <strong class="error">{{ $errors->first('detail1') }}</strong>
                                                              </span>
                                                          @endif
                                                    </fieldset>
                                                    <fieldset class="form-group floating-label-form-group">
                                                        <label for="email" class="label-control">Image</label>
                                                        <img class="card-img-top img-fluid" src="@if($item->image){{url($item->image)}} @else {{asset('admin/app-assets/images/portrait/small/avatar-s-1.png')}} @endif" alt="{{$item->journal}}">
                                                    </fieldset>
                                                    <fieldset class="form-group floating-label-form-group">
                                                        <label class="label-control required">Date </label>
                                                        <input type="date" class="form-control" id="date" name="date" placeholder="Date" min="<?php echo date("Y-m-d"); ?>" value="<?php echo date('Y-m-d',strtotime($item->journal_date)) ?>">
                                                            @if ($errors->has('date'))
                                                                <span class="help-block">
                                                                    <strong class="error">{{ $errors->first('date') }}</strong>
                                                                </span>
                                                            @endif
                                                      </fieldset>
                                              </div>
                                              <div class="modal-footer">
                                                  <input type="reset" class="btn btn-outline-secondary btn-lg" data-dismiss="modal" value="close">
                                              </div>
                                          </div>
                                        </div>
                                    </div>
                                    @empty
                                    @endforelse
                                    
                                </tbody>
                            </table>
                            <div class="pull-right">
                                {!! $list->render() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Import jQuery before export.js-->
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script src="https://cdn.ckeditor.com/4.14.0/standard-all/ckeditor.js"></script>

<!--Data Table-->
<script type="text/javascript"  src=" https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
<script type="text/javascript"  src=" https://cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js"></script>

<!--Export table buttons-->
<script type="text/javascript"  src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script type="text/javascript" src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.24/build/pdfmake.min.js" ></script>
<script type="text/javascript"  src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.24/build/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.1/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/bootstrap.tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js"></script>
<script>
    CKEDITOR.replace( 'detail1' );
    // CKEDITOR.replace( 'detail2' );
    $('.detail2').each(function () {
        CKEDITOR.replace($(this).prop('id'));
    });
    $('.select2-multi').select2();
    function confirmDelete(id,name,status) {
        if(status==0){
            var text="inactive";
        }else{
            var text="active";
        }
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to '+text+' the '+name+'?',
            showDenyButton: false,
            showCancelButton: true,
            confirmButtonText: `Ok`,
            cancelButtonText: `Cancel`,
        }).then((result) => {
            if (result.isConfirmed) {
                $('#'+id).submit();
            } 
        })
    }
    $(document).ready(function() {
        $.noConflict();
        
        // $('#DataTables').DataTable({
        //     "ordering": false,
        //     "info": true,
        //     "autoWidth": false,
        //     "bInfo": false,
        //     "paging": true,
        //     "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
        //     "dom": "Bfrtip",
        //     "buttons": [
        //         {
        //         extend: "copy",
        //         exportOptions: { columns: [":visible :not(:last-child)"] },
        //         },
        //         {
        //         extend: "csv",
        //         exportOptions: { columns: [":visible :not(:last-child)"] },
        //         },
        //         {
        //         extend: "excel",
        //         exportOptions: { columns: [":visible :not(:last-child)"] },
        //         },
        //         {
        //         extend: "print",
        //         exportOptions: { columns: [":visible :not(:last-child)"] },
        //         },
        //         {
        //         extend: "pdf",
        //         exportOptions: { columns: [":visible :not(:last-child)"] },
        //         },
        //     ],
        // }),
        // $(
        // ".buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel"
        // ).removeClass("dt-button buttons-html5").addClass("btn btn-info square  mr-1 mb-1");
    });
</script>

@endsection