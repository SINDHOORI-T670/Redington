@extends('layouts.adminlay')
@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css">
<style>
table.dataTable tbody td {
    word-break: break-word;
    vertical-align: top;
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
                                <li><a data-toggle="modal" data-target="#addTechnologyModal"  href="#" class="btn btn-success mr-1 mb-1 ladda-button" data-style="expand-left"><i class="ft-plus white"></i> <span class="ladda-label">Add Technology</span></a></li>
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
                                       
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($technologys as $technology)
                                    <tr role="row" data-toggle="modal" data-target="#technologyDetailModal" class="odd" data-id="{{$technology->id}}">
                                        <td>{{$technology->name}}</td>
                                        <td>
                                            <a class="btn btn-primary text-white tab-order" data-toggle="modal" data-target="#editTechnologyModal{{$technology->id}}"  href="#"><i class="icon-pencil"></i> Edit</a>
                                            <button class="btn btn-danger text-white tab-order" onclick="confirmDelete('resource-delete-{{ $technology->id }}','{{ $technology->name }}');"><i class="icon-trash"></i> Delee</button>
                                            <form id="resource-delete-{{ $technology->id }}" action="{{url('admin/delete/user/')}}/{{$technology->id}}" method="get">
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
                                                <span aria-hidden="true">×</span>
                                              </button>
                                            </div>
                                            <form>
                                              <div class="modal-body">
                                                  <fieldset class="form-group floating-label-form-group">
                                                      <label for="email">Technology</label>
                                                      <input type="text" class="form-control" id="editname" name="editname" value="{{$technology->name}}">
                                                  </fieldset>
                                                  <br>
                                                  <fieldset class="form-group floating-label-form-group">
                                                      <label for="title1">Description</label>
                                                      <textarea class="form-control" id="editdescription" name="editdescription">{{$technology->description}}</textarea>
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
<script>
    function confirmDelete(id,name) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to remove this user '+name+'?',
            showDenyButton: false,
            showCancelButton: true,
            confirmButtonText: `Ok Delete`,
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
        
        $('#DataTables').DataTable({
            "ordering": false,
            "info": true,
            "autoWidth": false,
            "bInfo": false,
            "paging": true,
            "lengthMenu": [[25, 50, -1], [25, 50, "All"]],
            "dom": "Bfrtip",
            "buttons": [
                {
                extend: "copy",
                exportOptions: { columns: [":visible :not(:last-child)"] },
                },
                {
                extend: "csv",
                exportOptions: { columns: [":visible :not(:last-child)"] },
                },
                {
                extend: "excel",
                exportOptions: { columns: [":visible :not(:last-child)"] },
                },
                {
                extend: "print",
                exportOptions: { columns: [":visible :not(:last-child)"] },
                },
                {
                extend: "pdf",
                exportOptions: { columns: [":visible :not(:last-child)"] },
                },
            ],
        }),
        $(
        ".buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel"
        ).removeClass("dt-button buttons-html5").addClass("btn btn-info square  mr-1 mb-1");
    });
</script>

@endsection