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
                        <h4 class="card-title">Technology List</h4>
                        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-toggle="modal" data-target="#createTechnologyModal"  href="#" class="btn btn-success mr-1 mb-1 ladda-button" data-style="expand-left"><i class="ft-plus white"></i> <span class="ladda-label">Add Technology</span></a></li>
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered dom-jQuery-events dataTable" id="DataTables" role="grid" aria-describedby="DataTables_Table_0_info">
                                <thead>
                                    <tr role="row">
                                        <th>Technology</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($technologys as $technology)
                                    <tr role="row" data-toggle="modal" data-target="#technologyDetailModal" class="odd" data-id="{{$technology->id}}">
                                        <td>{{$technology->name}}</td>
                                        <td><h4 @if($technology->status==1) class="danger text-center" @else class="success text-center" @endif>{{($technology->status==1)?"Inactive":"Active"}}</h4></td>
                                        <td>
                                            <a class="btn btn-primary text-white tab-order" data-toggle="modal" data-target="#editTechnologyModal{{$technology->id}}"  href="#"><i class="icon-pencil"></i> Edit</a>
                                            <button @if($technology->status==0) class="btn btn-danger text-white tab-order" @else class="btn btn-success text-white tab-order" @endif onclick="confirmDelete('technology-active-{{ $technology->id }}','{{ $technology->name }}','{{ $technology->status }}');"> @if($technology->status==0) <i class="fa fa-thumbs-o-down"></i> Inactive @else <i class="fa fa-thumbs-o-up"></i> Active @endif</button>
                                            <form id="technology-active-{{ $technology->id }}" action="{{url('admin/active/technology/')}}/{{$technology->id}}" method="get">
                                                {{ csrf_field() }}
                                            </form>
                                        </td>
                                    </tr>
                                    <div class="modal fade text-left show" id="editTechnologyModal{{$technology->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" style="padding-right: 17px;">
                                        <div class="modal-dialog" role="document">
                                          <div class="modal-content">
                                            <div class="modal-header">
                                              <h3 class="modal-title" id="myModalLabel35"> Edit Technology</h3>
                                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">??</span>
                                              </button>
                                            </div>
                                            <form method="POST" action="{{url('admin/edit/Redington/technology')}}/{{$technology->id}}">
                                                @csrf
                                              <div class="modal-body">
                                                    <fieldset class="form-group floating-label-form-group">
                                                      <label for="email" class="label-control required">Technology</label>
                                                      <input type="text" class="form-control" id="editname" name="editname" value="{{$technology->name}}">
                                                        @if ($errors->has('editname'))
                                                            <span class="help-block">
                                                                <strong class="error">{{ $errors->first('editname') }}</strong>
                                                            </span>
                                                        @endif
                                                    </fieldset>
                                                    <br>
                                                  <fieldset class="form-group floating-label-form-group">
                                                      <label for="title1" class="label-control required">Description</label>
                                                      <textarea class="form-control detail2" id="editdescription{{$technology->id}}" name="editdescription">{!!$technology->description!!}</textarea>
                                                        @if ($errors->has('editdescription'))
                                                            <span class="help-block">
                                                                <strong class="error">{{ $errors->first('editdescription') }}</strong>
                                                            </span>
                                                        @endif
                                                  </fieldset>
                                              </div>
                                              <div class="modal-footer">
                                                  <input type="reset" class="btn btn-outline-secondary btn-lg" data-dismiss="modal" value="close">
                                                  <input type="submit" class="btn btn-outline-primary btn-lg" value="Update">
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
                                {!! $technologys->render() !!}
                            </div>
                        </div>
                    </div>

                    <div class="modal fade text-left show" id="createTechnologyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" style="padding-right: 17px;">
                        <div class="modal-dialog" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h3 class="modal-title" id="myModalLabel35"> Create Technology</h3>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">??</span>
                              </button>
                            </div>
                            <form method="POST" action="{{url('admin/add/new/Redington/technology')}}">
                                @csrf
                              <div class="modal-body">
                                  <fieldset class="form-group floating-label-form-group">
                                      <label for="email" class="label-control required">Technology</label>
                                      <input type="text" class="form-control" id="techname" name="techname" placeholder="Technology Name">
                                        @if ($errors->has('techname'))
                                            <span class="help-block">
                                                <strong class="error">{{ $errors->first('techname') }}</strong>
                                            </span>
                                        @endif
                                  </fieldset>
                                  <br>
                                  <fieldset class="form-group floating-label-form-group">
                                      <label for="title1" class="label-control required">Description</label>
                                      <textarea class="form-control" id="techdescription" name="techdescription" placeholder="Technology Description"></textarea>
                                        @if ($errors->has('techdescription'))
                                            <span class="help-block">
                                                <strong class="error">{{ $errors->first('techdescription') }}</strong>
                                            </span>
                                        @endif
                                  </fieldset>
                              </div>
                              <div class="modal-footer">
                                  <input type="reset" class="btn btn-outline-secondary btn-lg" data-dismiss="modal" value="close">
                                  <input type="submit" class="btn btn-outline-primary btn-lg" value="Save">
                              </div>
                            </form>
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
    CKEDITOR.replace( 'techdescription' );
    // CKEDITOR.replace( 'detail2' );
    $('.detail2').each(function () {
        CKEDITOR.replace($(this).prop('id'));
    });
    function confirmDelete(id,name) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to remove this user '+name+'?',
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