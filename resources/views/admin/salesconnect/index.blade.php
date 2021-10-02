@extends('layouts.adminlay')
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
</style>
<div class="app-content content">
    <div class="content-wrapper">
        <br>
        @include('alert.messages')
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Sales Connects</h4>
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
                                        <th>#</th>
                                        <th>User</th>
                                        <th>Request Mode</th>
                                        {{-- <th>Technology</th>
                                        <th>Brand</th>
                                        <th>Region</th>
                                        <th>POC</th> --}}
                                        <th>Date & Time</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($list as $index => $item)
                                    <tr role="row" class="odd">
                                        <td>{{$index+1}}</td>
                                        <td>{{$item->from->name}} (@if($item->from->type==2) Customer @elseif($item->from->type==3) Partner @elseif($item->from->type==4) Employee @endif)</td>
                                        <td>
                                            {{($item->status==1)?"Meeting Request":"POC Connect"}}
                                        </td>
                                        
                                        <td>@if($item->status==1) {{Carbon\Carbon::parse($item->date_time)->format('j F Y h:i A')}}@else @endif</td>
                                        <td>
                                            <a class="btn btn-secondary text-white tab-order" data-toggle="modal" data-target="#viewModal{{$item->id}}"  href="#" title="View"><i class="fa fa-eye"></i></a>
                                            @if($item->status==1)
                                            <a class="btn btn-primary text-white tab-order" data-toggle="modal" data-target="#ReschduleModal{{$item->id}}"  href="#" title="Reschedule"><i class="fa fa-calendar"></i></a>
                                            @else 
                                            <a class="btn btn-warning text-white tab-order" href="{{url('admin/preset_questions')}}/{{$item->tech_id}}/{{$item->brand_id}}/{{$item->from_id}}" title="Preset Questions"><i class="fa fa-question"></i></a>
                                            @endif
                                            {{-- <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="icon-settings mr-1"></i></button>
                                                <div class="dropdown-menu arrow" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                    <a class="dropdown-item" href="{{url('admin/preset_questions')}}/{{$item->id}}"><i class="fa fa-question mr-1"></i> Preset Questions</a> 
                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#ReschduleModal{{$item->id}}"><i class="fa fa-calendar mr-1"></i> Reschedule</a>
                                                </div> --}}
                                        </td>
                                    </tr>
                                    <div class="modal fade text-left show" id="ReschduleModal{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" style="padding-right: 17px;">
                                        <div class="modal-dialog" role="document">
                                          <div class="modal-content">
                                            <div class="modal-header">
                                              <h3 class="modal-title" id="myModalLabel35"> Reschedule Sales Connects</h3>
                                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                              </button>
                                            </div>
                                            <form method="POST" action="{{url('admin/Reschedule')}}/{{$item->id}}">
                                                @csrf
                                              <div class="modal-body">
                                                    <fieldset class="form-group floating-label-form-group">
                                                        <label for="email" class="label-control required">Technology</label>
                                                        <select class="form-control" name="tech">
                                                            @forelse ($techs as $tech)
                                                                <option value="{{$tech->id}}" @if($tech->id==$item->tech_id) selected @else @endif>{{$tech->name}}</option>
                                                            @empty
                                                                
                                                            @endforelse
                                                        </select>
                                                    </fieldset>
                                                    <fieldset class="form-group floating-label-form-group">
                                                        <label for="email" class="label-control required">Product</label>
                                                        <select class="form-control" name="product">
                                                            @forelse ($products as $product)
                                                                <option value="{{$product->id}}" @if($product->id==$item->product_id) selected @else @endif>{{$product->name}}</option>
                                                            @empty
                                                                
                                                            @endforelse
                                                        </select>
                                                    </fieldset>
                                                    <fieldset class="form-group floating-label-form-group">
                                                      <label for="email" class="label-control required">Date</label>
                                                      <input type="date" class="form-control" id="date" name="date" >
                                                        @if ($errors->has('date'))
                                                            <span class="help-block">
                                                                <strong class="error">{{ $errors->first('date') }}</strong>
                                                            </span>
                                                        @endif
                                                    </fieldset>
                                                    <br>
                                                  <fieldset class="form-group floating-label-form-group">
                                                      <label for="title1" class="label-control required">Time</label>
                                                      <input class="form-control" type="time" name="time" id="time" value="08:00">
                                                        @if ($errors->has('time'))
                                                            <span class="help-block">
                                                                <strong class="error">{{ $errors->first('time') }}</strong>
                                                            </span>
                                                        @endif
                                                  </fieldset>
                                              </div>
                                              <div class="modal-footer">
                                                  <input type="reset" class="btn btn-outline-secondary btn-lg" data-dismiss="modal" value="close">
                                                  <input type="submit" class="btn btn-outline-primary btn-lg" value="Reschedule Meeting">
                                              </div>
                                            </form>
                                          </div>
                                        </div>
                                    </div>
                                    <div class="modal" id="viewModal{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" style="padding-right: 17px;">
                                        <div class="modal-dialog" role="document">
                                          <div class="modal-content">
                                            <div class="modal-header">
                                              <h3 class="modal-title" id="myModalLabel35"> View Sales Connects Details</h3>
                                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                              </button>
                                              
                                            </div>
                                            <form method="post" action="{{url('admin/sales_connect/schedule/accept')}}/{{$item->id}}">
                                                @csrf
                                                <div class="modal-body">
                                                    <p class="text-info text-center"><strong> @if(isset($item->requestdata->notifi)) {{$item->requestdata->notifi->message}} @else @endif</strong></p>
                                                    <fieldset class="input-group floating-label-form-group">
                                                        <label class="label-control col-md-4"><b>Technology</b>:</label>
                                                        <input  type="text" class="form-control col-md-8" value="{{(isset($item->technology))?$item->technology->name:""}}">
                                                    </fieldset><br>
                                                    <fieldset class="input-group floating-label-form-group">
                                                        <label class="label-control col-md-4"><b>Brand</b>:</label>
                                                        <input  type="text" class="form-control col-md-8" value="{{(isset($item->brand))?$item->brand->name:""}}">
                                                    </fieldset><br>
                                                    <fieldset class="input-group floating-label-form-group">
                                                        <label class="label-control col-md-4"><b>POC</b>:</label>
                                                        <input  type="text" class="form-control col-md-8" value="{{(isset($item->user))?$item->user->name:""}}">
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
                                                    @if($item->meeting_status!=1 && empty($item->reschedule))
                                                        <input type="submit" class="btn btn-outline-primary btn-lg" value="Schedule Meeting">
                                                    @endif
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
                                {!!$list->render() !!}
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
    $('.select2-multi').select2();
    function confirmDelete(id,name,status) {
        if(status==0){
            var text="inactive";
        }else{
            var text="active";
        }
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to '+text+' this user '+name+'?',
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
    
    $('document').ready(function() {
        var modal = "#"+'{{$modalid}}';
            if(modal){
            
            $(modal).modal('show');
        }
    });
    
    var today = new Date().toISOString().split('T')[0];
    document.getElementsByName("date")[0].setAttribute('min', today);
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