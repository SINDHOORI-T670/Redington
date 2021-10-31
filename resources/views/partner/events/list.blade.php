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

                                        <th>Name</th>

                                        <th>E-mail</th>

                                        <th>Phone</th>


                                        <th>Status</th>


                                    </tr>

                                </thead>

                                <tbody>
                                    @if(isset($users))
                                    @forelse($users as $index => $user)

                                    <tr role="row" class="odd">

                                        <td>{{ $index+ $users->firstItem() }}</td>

                                        <td>{{$user->name}}</td>

                                        <td>{{$user->email}}</td>

                                        <td>{{$user->phone}}</td>

                                        @if($user->type==4)

                                        <td>{{isset($user->poc)?$user->poc->name:"--"}}</td>

                                        <td>{{isset($user->regionConnect)?$user->regionConnect->region->name:"--"}}</td>

                                        @endif

                                        <td><h4 @if($user->status==0) class="success text-center" @else class="danger text-center" @endif>{{($user->status==0)?"Active":"Inactive"}}</h4></td>


                                    </tr>

                                    @empty

                                    @endforelse
                                    @endif

                                </tbody>
                                <div class="pull-right">

                                {!!$users->render() !!}

                                </div>
                                <br><br>
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

<script src="https://cdn.jsdelivr.net/bootstrap.tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js"></script>

<script>

    
    if('{{$type}}'==2){
        var user = "Customer";
    }else if('{{$type}}'==3){
        var user = "Partner";
    }else{
        var user = "Employee";
    }
    
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


    $(document).ready(function() {

        $.noConflict();
        $('#DataTables').DataTable({
            "scrollY":        '50vh',
            "scrollX": false,
            "paging":false,
            "searching": false,
            "info": false,
            "ordering": false
        });
    });

</script>



@endsection