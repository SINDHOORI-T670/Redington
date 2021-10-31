@extends('layouts.partnerlay')
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
                        <h4 class="card-title">Resources</h4>
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
                                        <th>Resource</th>
                                        <th>User Type</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($list as $item)
                                    @php 
                                        $array = explode(',',$item->type);
                                    @endphp
                                        @if(in_array(3,$array))
                                            <tr role="row" class="odd">
                                                <td>{{$item->name}}</td>
                                                <td>
                                                    Partner
                                                </td>
                                                <td>
                                                    <a class="btn btn-primary text-white tab-order" data-toggle="modal" data-target="#editResourceModal{{$item->id}}"  href="#"><i class="icon-pencil"></i> View</a>
                                                    <a class="btn btn-warning text-white tab-order" href="{{url('partner/subresource/list')}}/{{$item->id}}"><i class="icon-list"></i> Sub Resources</a>
                                                    
                                                </td>
                                            </tr>
                                        @endif
                                        <div class="modal fade text-left show" id="editResourceModal{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" style="padding-right: 17px;">
                                            <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                <h3 class="modal-title" id="myModalLabel35"> Edit Resource</h3>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                                </div>
                                                <div class="modal-body">
                                                        <fieldset class="form-group floating-label-form-group">
                                                        <label for="email" class="label-control required">Resource Name</label>
                                                        <input type="text" class="form-control" id="editname" name="editname" value="{{$item->name}}">
                                                            @if ($errors->has('editname'))
                                                                <span class="help-block">
                                                                    <strong class="error">{{ $errors->first('editname') }}</strong>
                                                                </span>
                                                            @endif
                                                        </fieldset>
                                                        <fieldset class="form-group floating-label-form-group">
                                                            <label for="type" class="label-control required">User Type</label>
                                                            <select name="type[]" id="type" class="form-control select2-multi" multiple>
                                                                <option value="">Select any type</option>
                                                                <option value="2" @if(in_array(2,explode(',',$item->type))) selected @else @endif>Customer</option>
                                                                <option value="3" @if(in_array(3,explode(',',$item->type))) selected @else @endif>Partner</option>
                                                                {{-- <option value="4" @if(in_array(4,explode(',',$item->type))) selected @else @endif>Employee</option> --}}
                                                            </select>
                                                            @if ($errors->has('type'))
                                                                <span class="help-block">
                                                                    <strong class="error">{{ $errors->first('type') }}</strong>
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