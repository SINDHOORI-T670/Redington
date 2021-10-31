@extends('layouts.employeelay')
@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="http://bootstrap-tagsinput.github.io/bootstrap-tagsinput/dist/bootstrap-tagsinput.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" />
<style>
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
    .required:after {
        content: "*";
        color: red;
    }
    .error{
        color:red;
    }
    .dataTables_scrollBody
    {
    overflow-x:hidden !important;
    overflow-y:auto !important;
    }
</style>
<div class="app-content content">
    <div class="content-wrapper">
        <br>
        @include('alert.messages')
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                   <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <ul class="list-inline">
                                <li ><a href="{{url('employee/sales_connect/meetingRequestfrompartner')}}" class="list-group-item list-group-item-action">Partner Meeting Requests</a></li>
                                <li ><a href="{{url('employee/sales_connect/meetingRequestfromcustomer')}}" class="list-group-item active">Customer Meeting Requests</a></li>
                                <li ><a href="{{url('employee/sales_connect/serviceRequestfromcustomer')}}" class="list-group-item list-group-item-action">Customer Sales Connect Requests</a></li>
                                <li ><a href="{{url('employee/sales_connect/serviceRequestfrompartner')}}" class="list-group-item list-group-item-action">Partner Sales Connect Requests</a></li>
                            </ul>
                            <table class="table table-striped table-bordered dom-jQuery-events dataTable" id="DataTables" role="grid" aria-describedby="DataTables_Table_0_info">
                                <thead>
                                    <tr role="row">
                                        <th>#</th>
                                        <th>Request Mode</th>
                                        <th>User</th>
                                        <th>Date & Time</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($list as $index => $item)
                                    <tr role="row" class="odd">
                                        <td>{{$index+1}}</td>
                                        <td>
                                            {{($item->status==1)?"Meeting Request":"POC Connect"}}
                                        </td>
                                        <td>{{$item->from->name}}</td>
                                        <td>@if($item->status==1) {{Carbon\Carbon::parse($item->date_time)->format('j F Y h:i A')}}@else @endif</td>
                                        <td>
                                            <a class="btn btn-secondary text-white tab-order" data-toggle="modal" data-target="#viewModal{{$item->id}}"  href="#" title="View"><i class="fa fa-eye"></i></a>
                                            
                                        </td>
                                    </tr>
                                    <div class="modal" id="viewModal{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" style="padding-right: 17px;">
                                        <div class="modal-dialog" role="document">
                                          <div class="modal-content">
                                            <div class="modal-header">
                                              <h3 class="modal-title" id="myModalLabel35"> View Sales Connects Details</h3>
                                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                              </button>
                                              
                                            </div>
                                            <div class="modal-body">
                                                <p class="text-info text-center"> <strong> {{(isset($item->requestdata->status) && $item->requestdata->status==0)?"Request is in progress state":(isset($item->requestdata->status) && $item->requestdata->status==1)?"Request is granted":(isset($item->requestdata->status) && $item->requestdata->status==2)?"This request is rejected":"No Request Data."}} </strong></p>
                                                <fieldset class="input-group floating-label-form-group">
                                                    <label class="label-control col-md-4"><b>Technology</b>:</label>
                                                    <input  type="text" class="form-control col-md-8" value="{{(isset($item->technology))?$item->technology->name:""}}">
                                                </fieldset><br>
                                                <fieldset class="input-group floating-label-form-group">
                                                    <label class="label-control col-md-4"><b>Brand</b>:</label>
                                                    <input  type="text" class="form-control col-md-8" value="{{(isset($item->brand))?$item->brand->name:""}}">
                                                </fieldset><br>
                                                <fieldset class="input-group floating-label-form-group">
                                                    <label class="label-control col-md-4"><b>Request From</b>:</label>
                                                    <input  type="text" class="form-control col-md-8" value="{{(isset($item->from))?$item->from->name:""}}">
                                                </fieldset><br>
                                                <fieldset class="input-group floating-label-form-group">
                                                    <label class="label-control col-md-4"><b>Region</b>:</label>
                                                    <input  type="text" class="form-control col-md-8" value="{{(isset($item->region))?$item->region->name:""}}">
                                                </fieldset><br>
                                                @if($item->status==1)
                                                <fieldset class="input-group floating-label-form-group">
                                                    <label class="label-control col-md-4"><b>Product</b>:</label>
                                                    <input  type="text" class="form-control col-md-8" value="{{(isset($item->product))?$item->product->name:""}}">
                                                </fieldset><br>
                                                <fieldset class="form-group floating-label-form-group">
                                                    <label for="email" class="label-control required">Date</label>
                                                    <input type="date" class="form-control" id="date" name="date" min="<?php echo date("Y-m-d"); ?>" value="<?php echo date('Y-m-d',strtotime($item->date_time)) ?>">
                                                    @if ($errors->has('date'))
                                                        <span class="help-block">
                                                            <strong class="error">{{ $errors->first('date') }}</strong>
                                                        </span>
                                                    @endif
                                                </fieldset>
                                                <br>
                                                <fieldset class="form-group floating-label-form-group">
                                                    <label for="title1" class="label-control required">Time</label>
                                                    <input class="form-control" type="time" name="time" id="time"  value="{{ Carbon\Carbon::parse($item->date_time)->format('h:i') }}">
                                                    @if ($errors->has('time'))
                                                        <span class="help-block">
                                                            <strong class="error">{{ $errors->first('time') }}</strong>
                                                        </span>
                                                    @endif
                                                </fieldset>
                                                @endif
                                                
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
                                {!!$list->render() !!}
                            </div>
                            <br><br><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Import jQuery before export.js-->
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>


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
    
    $(document).ready(function() {
        $.noConflict();
        $('#DataTables').DataTable({
            "scrollY":'50vh',
            "scrollX": false,
            "paging":false,
            "searching": false,
            "info": false,
            "ordering": false
        });
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