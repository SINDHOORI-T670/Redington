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
                        <h4 class="card-title"><a href="{{url('customer/journals')}}"><i class="fa fa-arrow-left"></i></a> &nbsp;&nbsp; Value Journals For <b class="text-primary">{{ucfirst($journal->journal)}}</b></h4>
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
                                        <th>Title</th>
                                        <th>Short Description</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($list as $item)
                                    <tr role="row" class="odd">
                                        <td>{{$item->title}}</td>
                                        <td>{!! $item->short !!}</td>
                                        {{-- <td><img  height="80" width="140" src="@if($item->image){{url($item->image)}} @else {{asset('admin/app-assets/images/portrait/small/avatar-s-1.png')}} @endif" alt="image"></td> --}}
                                        
                                        <td><h4 @if($item->status==1) class="danger text-center" @else class="success text-center" @endif>{{($item->status==1)?"Inactive":"Active"}}</h4></td>
                                        <td>
                                            <a class="btn btn-primary text-white tab-order" data-toggle="modal" data-target="#editvaluejournalModal{{$item->id}}"  href="#"><i class="icon-pencil"></i> View</a>
                                            
                                        </td>
                                    </tr>
                                    <div class="modal fade text-left show" id="editvaluejournalModal{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" style="padding-right: 17px;">
                                        <div class="modal-dialog" role="document">
                                          <div class="modal-content">
                                            <div class="modal-header">
                                              <h3 class="modal-title" id="myModalLabel35"> View Details of  {{$item->title}}</h3>
                                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">??</span>
                                              </button>
                                            </div>
                                              <div class="modal-body">
                                                <fieldset class="form-group floating-label-form-group">
                                                    <label class="label-control required">Title</label>
                                                    <input type="text" class="form-control" id="title" name="title" placeholder="Title" value="{{$item->title}}">
                                                      @if ($errors->has('title'))
                                                          <span class="help-block">
                                                              <strong class="error">{{ $errors->first('title') }}</strong>
                                                          </span>
                                                      @endif
                                                </fieldset>
                                                
                                                <fieldset class="form-group floating-label-form-group">
                                                    <label class="label-control required">Image</label>
                                                    <img class="card-img-top img-fluid" src="@if($item->image){{url($item->image)}} @else {{asset('admin/app-assets/images/portrait/small/avatar-s-1.png')}} @endif" alt="{{$item->title}}">
                                                </fieldset>
              
                                                <fieldset class="form-group floating-label-form-group">
                                                  <label class="label-control required">Short Description</label>
                                                  <textarea cols="20" rows="5" class="form-control detail3" id="detail3_{{$item->id}}" name="detail3" placeholder="Short description about value journal">{!! $item->short !!}</textarea>
                                                      @if ($errors->has('detail3'))
                                                          <span class="help-block">
                                                              <strong class="error">{{ $errors->first('detail3') }}</strong>
                                                          </span>
                                                      @endif
                                                </fieldset>
              
                                                <fieldset class="form-group floating-label-form-group">
                                                  <label class="label-control required">Details </label>
                                                  <textarea cols="30" rows="15" class="form-control detail4" id="detail4_{{$item->id}}" name="detail4" placeholder="Details about value journal">{!! $item->detail !!}</textarea>
                                                      @if ($errors->has('detail4'))
                                                          <span class="help-block">
                                                              <strong class="error">{{ $errors->first('detail4') }}</strong>
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
    CKEDITOR.replace( 'detail2' );
    $('.detail3').each(function () {
        CKEDITOR.replace($(this).prop('id'));
    });
    $('.detail4').each(function () {
        CKEDITOR.replace($(this).prop('id'));
    });
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