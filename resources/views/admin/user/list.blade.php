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
                        @php 
                            $usertype = (
                                ($type == 2) ? "Customer" :
                                (($type == 3) ? "Partner" :
                                (($type == 4) ? "Employee" : "No List"))
                                );
                        @endphp
                        <h4 class="card-title">{{$usertype}} List</h4>
                        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a href="{{url('admin/create/user')}}/{{$type}}" class="btn btn-success mr-1 mb-1 ladda-button" data-style="expand-left"><i class="ft-plus white"></i> <span class="ladda-label">Add {{$usertype}}</span></a></li>
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
                                        <th>E-mail</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr role="row" class="odd">
                                        <td>{{$user->name}}</td>
                                        <td>{{$user->email}}</td>
                                        <td>{{$user->phone}}</td>
                                        <td>{{($user->status==1)?"Active":"Inactive"}}</td>
                                        <td>
                                            <a class="btn btn-primary text-white tab-order" href="{{url('admin/edit/user/')}}/{{$user->id}}"><i class="icon-pencil"></i> Edit</a>
                                            <button class="btn btn-danger text-white tab-order" onclick="confirmDelete('resource-delete-{{ $user->id }}','{{ $user->name }}');"><i class="icon-trash"></i> Delee</button>
                                            <form id="resource-delete-{{ $user->id }}" action="{{url('admin/delete/user/')}}/{{$user->id}}" method="get">
                                                {{ csrf_field() }}
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
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