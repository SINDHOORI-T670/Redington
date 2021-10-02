@extends('layouts.adminlay')
@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css">
<style>
    table.dataTable tbody td {
        word-break: break-word;
        vertical-align: top;
    }
.required:after {
        content: "*";
        color: red;
    }
    .error{
        color:red;
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
                        <h4 class="card-title">Requests</h4>
                        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                {{-- <li><a data-toggle="modal" data-target="#createBusinessModal"  href="#" class="btn btn-success mr-1 mb-1 ladda-button" data-style="expand-left"><i class="ft-plus white"></i> <span class="ladda-label">Add Business Solution</span></a></li> --}}
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered dom-jQuery-events dataTable" id="DataTables" role="grid" aria-describedby="DataTables_Table_0_info">
                                <thead>
                                    <tr role="row">
                                        <th>From</th>
                                        <th>User Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($list as $request)
                                    <tr role="row" class="odd">
                                        <td>{{$request->from->name}}</td>
                                        <td>{{$request->from->type==2?"Customer":$request->from->type==3?"Partner":"Employee"}}</td>
                                        <td><h4 @if($request->status==0) class="warning" @elseif($request->status==1) class="success" @else class="danger"  @endif>@if($request->status==0) Waiting @elseif($request->status==1)  Accepted @else Rejected  @endif</h4></td>
                                        <td>
                                            <a class="btn btn-primary text-white tab-order" data-toggle="modal" data-target="#viewModal{{$request->id}}"  href="#"><i class="icon-eye"></i> View</a>
                                            {{-- <button @if($request->status==0) class="btn btn-danger text-white tab-order" @else class="btn btn-success text-white tab-order" @endif onclick="confirmDelete('service-active-{{ $request->id }}','{{ $request->name }}','{{ $request->status }}');"> @if($request->status==0) <i class="fa fa-thumbs-o-down"></i> Inactive @else <i class="fa fa-thumbs-o-up"></i> Active @endif</button>
                                            <form id="service-active-{{ $request->id }}" action="{{url('admin/active/service/')}}/{{$request->id}}" method="get">
                                                {{ csrf_field() }}
                                            </form> --}}
                                        </td>
                                    </tr>
                                    <div class="modal" id="viewModal{{$request->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" style="padding-right: 17px;">
                                        <div class="modal-dialog" role="document">
                                          <div class="modal-content">
                                            <div class="modal-header">
                                              <h3 class="modal-title" id="myModalLabel35"> View Request Details</h3>
                                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                              </button>
                                            </div>
                                            <form method="post" action="{{url('admin/respond/request')}}/{{$request->id}}">
                                                @csrf
                                                <input type="hidden" name="type" value="Business_Solution">
                                                <div class="modal-body">
                                                    <p class="text-success text-center" id="showme" style="display:block;"><strong>@if($request->status==1) Request Granted @elseif($request->status==2) Request Rejected!! @else Your request in progress @endif</strong></p>
                                                    <p class="text-info text-center" id="notif_message" style="display:none;"><strong>@if(isset($request->notifi)){{$request->notifi->message}} @else @endif</strong></p>
                                                    <fieldset class="input-group floating-label-form-group">
                                                        <label class="label-control col-md-4"><b>Business Solution Name</b>:</label>
                                                        <input  type="text" class="form-control col-md-8" value="{{(isset($request->business))?$request->business->name:""}}">
                                                    </fieldset><br>
                                                    <fieldset class="input-group floating-label-form-group">
                                                        <label class="label-control col-md-4"><b>Request From</b>:</label>
                                                        <input  type="text" class="form-control col-md-8" value="{{(isset($request->from))?$request->from->name:""}}">
                                                    </fieldset><br>
                                                    <fieldset class="input-group floating-label-form-group">
                                                        <label class="label-control col-md-4"><b>Requested Date</b>:</label>
                                                        <input  type="text" class="form-control col-md-8" value="{{ Carbon\Carbon::parse($request->date_time)->format('j F Y h:i A')}}">
                                                    </fieldset><br>
                                                    <fieldset class="input-group floating-label-form-group">
                                                        <label class="label-control col-md-4"><b>Status</b>:</label>
                                                        <select class="form-control col-md-8" name="status">
                                                                <option value="1" @if($request->status==1) selected @endif>Accept</option>
                                                                <option value="2" @if($request->status==2) selected @endif>Reject</option>
                                                        </select>
                                                    </fieldset><br>
                                                    
                                                </div>
                                                <div class="modal-footer">
                                                    <input type="reset" class="btn btn-outline-secondary btn-lg" data-dismiss="modal" value="close">
                                                    <input type="submit" class="btn btn-outline-primary btn-lg" value="Save">
                                                </div>
                                            </form>
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
<script>
    
    $('document').ready(function() {
        console.log('{{$modalid}}');
        if('{{$modalid}}'!=""){
            var modal = "#"+'{{$modalid}}';
            if(modal){
                $('#notif_message').css("display","block");
                $('#showme').css("display","none");
                $(modal).modal('show');
                modal="";
            }else{
                $('#notif_message').css("display","none");
                $('#showme').css("display","block");
                $(modal).modal('show');
            }
        }
        
    });
    function confirmDelete(id,name) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to remove this service '+name+'?',
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
</script>
<script type="text/javascript">
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