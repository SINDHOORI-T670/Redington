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
    .rotate-icon.down{
        -ms-transform: rotate(180deg);
        -moz-transform: rotate(180deg);
        -webkit-transform: rotate(180deg);
        transform: rotate(180deg);
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

                                        <th>Action</th>

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
                                        <td>
                                        <a class="btn btn-info text-white tab-order" data-toggle="modal" data-target="#modalQuickView{{$user->id}}"  href="#" ><i class="icon-eye"></i></a>
                                        </td>

                                    </tr>
                                        <div class="modal fade" id="modalQuickView{{$user->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                <div class="modal-body">
                                                    <div class="row">
                                                    <div class="col-lg-5">
                                                        <!--Carousel Wrapper-->
                                                        <div id="carousel-thumb" class="carousel slide carousel-fade carousel-thumbnails"
                                                        data-ride="carousel">
                                                        <!--Slides-->
                                                        <div class="carousel-inner" role="listbox">
                                                            <div class="carousel-item active">
                                                            <img class="d-block w-100"
                                                                src="@if($user->image!=null) {{url($user->image)}} @else {{asset('admin/app-assets/images/portrait/small/avatar-s-1.png')}} @endif"
                                                                alt="First slide">
                                                            </div>
                                                        </div>
                                                        <!--/.Slides-->
                                                        </div>
                                                        <!--/.Carousel Wrapper-->
                                                    </div>
                                                    <div class="col-lg-7">
                                                        <h2 class="h2-responsive product-name">
                                                        <strong>{{$user->name}}</strong>
                                                        </h2>
                                                        <h4 class="h4-responsive">
                                                        <span class="text-success">
                                                            <strong>{{$user->email}}</strong>
                                                        </span>
                                                        <span class="text-success">
                                                            &nbsp;<strong>{{$user->phone}}</strong>
                                                        </span>
                                                        </h4>

                                                        <!--Accordion wrapper-->
                                                        <div class="accordion md-accordion" id="accordionEx" role="tablist" aria-multiselectable="true">

                                                            <!-- Accordion card -->
                                                            <div class="card">

                                                                <!-- Card header -->
                                                                <div class="card-header" role="tab" id="headingOne1">
                                                                    <a data-toggle="collapse" data-parent="#accordionEx" href="#collapseOne1" aria-expanded="true"
                                                                        aria-controls="collapseOne1">
                                                                        <h5 class="mb-0">
                                                                        Company #1 <i class="fa fa-chevron-up rotate-icon"></i>
                                                                        </h5>
                                                                    </a>
                                                                </div>

                                                                <!-- Card body -->
                                                                <div id="collapseOne1" class="collapse" role="tabpanel" aria-labelledby="headingOne1"
                                                                data-parent="#accordionEx">
                                                                    <div class="card-body">
                                                                        {{$user->company}}
                                                                    </div>
                                                                </div>


                                                                <!-- Card header -->
                                                                <div class="card-header" role="tab" id="headingOne2">
                                                                    <a data-toggle="collapse" data-parent="#accordionEx" href="#collapseOne2" aria-expanded="true"
                                                                        aria-controls="collapseOne2">
                                                                        <h5 class="mb-0">
                                                                        Position #2 <i class="fa fa-chevron-up rotate-icon"></i>
                                                                        </h5>
                                                                    </a>
                                                                </div>

                                                                <!-- Card body -->
                                                                <div id="collapseOne2" class="collapse" role="tabpanel" aria-labelledby="headingOne2"
                                                                data-parent="#accordionEx">
                                                                    <div class="card-body">
                                                                        {{$user->post}}
                                                                    </div>
                                                                </div>
                                                                <!-- Card header -->
                                                                <div class="card-header" role="tab" id="headingOne3">
                                                                    <a data-toggle="collapse" data-parent="#accordionEx" href="#collapseOne3" aria-expanded="true"
                                                                        aria-controls="collapseOne3">
                                                                        <h5 class="mb-0">
                                                                          Linkedin URL #3 <i class="fa fa-chevron-up rotate-icon"></i>
                                                                        </h5>
                                                                    </a>
                                                                </div>

                                                                <!-- Card body -->
                                                                <div id="collapseOne3" class="collapse" role="tabpanel" aria-labelledby="headingOne3"
                                                                    data-parent="#accordionEx">
                                                                    <div class="card-body">
                                                                        {{$user->linkedin}}
                                                                    </div>
                                                                </div>

                                                                @if($user->type==2)
                                                                    <!-- Card header -->
                                                                    <div class="card-header" role="tab" id="headingOne4">
                                                                        <a data-toggle="collapse" data-parent="#accordionEx" href="#collapseOne4" aria-expanded="true"
                                                                            aria-controls="collapseOne4">
                                                                            <h5 class="mb-0">
                                                                            Services #4 <i class="fa fa-chevron-up rotate-icon"></i>
                                                                            </h5>
                                                                        </a>
                                                                    </div>

                                                                    <!-- Card body -->
                                                                    <div id="collapseOne4" class="collapse" role="tabpanel" aria-labelledby="headingOne4"
                                                                        data-parent="#accordionEx">
                                                                        <div class="card-body">
                                                                        <ul class="list-inline mb-0 text-white">
                                                                        @forelse ($services as $item)

                                                                            <li class="text-success"> @if(isset($user->userSpec->service_id))@foreach(explode(",",$user->userSpec->service_id) as $spec) @if($spec==$item->id) {{$item->name}} @else @endif @endforeach @endif</li>

                                                                            @empty

                                                                        @endforelse
                                                                        </ul>
                                                                        </div>
                                                                    </div>

                                                                @else  
                                                                        <!-- Card header -->
                                                                    <div class="card-header" role="tab" id="headingOne4">
                                                                        <a data-toggle="collapse" data-parent="#accordionEx" href="#collapseOne4" aria-expanded="true"
                                                                            aria-controls="collapseOne4">
                                                                            <h5 class="mb-0">
                                                                            Products #4 <i class="fa fa-chevron-up rotate-icon"></i>
                                                                            </h5>
                                                                        </a>
                                                                    </div>

                                                                    <!-- Card body -->
                                                                    <div id="collapseOne4" class="collapse" role="tabpanel" aria-labelledby="headingOne4"
                                                                        data-parent="#accordionEx">
                                                                        <div class="card-body">
                                                                        <ul class="list-inline mb-0 text-success">
                                                                        @forelse ($products as $item)
                                                                        <li class="text-success">
                                                                            @if(isset($user->userSpec->product_id))@foreach(explode(",",$user->userSpec->product_id) as $spec) @if($spec==$item->id) {{$item->name}} @else @endif @endforeach @endif</label>
                                                                        </li>
                                                                            @empty

                                                                        @endforelse
                                                                        </ul>
                                                                        </div>
                                                                    </div>

                                                                @endif

                                                                <!-- Card header -->
                                                                <div class="card-header" role="tab" id="headingOne5">
                                                                        <a data-toggle="collapse" data-parent="#accordionEx" href="#collapseOne5" aria-expanded="true"
                                                                            aria-controls="collapseOne5">
                                                                            <h5 class="mb-0">
                                                                            Technology #5 <i class="fa fa-chevron-up rotate-icon"></i>
                                                                            </h5>
                                                                        </a>
                                                                    </div>

                                                                    <!-- Card body -->
                                                                    <div id="collapseOne5" class="collapse" role="tabpanel" aria-labelledby="headingOne5"
                                                                        data-parent="#accordionEx">
                                                                        <div class="card-body">
                                                                        <ul class="list-inline mb-0 text-white">
                                                                        @forelse ($technologies as $item)

                                                                            <li class="text-success"> @if(isset($user->userSpec->technology_id))@foreach(explode(",",$user->userSpec->technology_id) as $spec) @if($spec==$item->id) {{$item->name}} @else @endif @endforeach @endif</li>

                                                                            @empty

                                                                        @endforelse
                                                                        </ul>
                                                                        </div>
                                                                    </div>

                                                            </div>
                                                            <!-- Accordion card -->

                                                            </div>

                                                        </div>
                                                        <!-- Accordion wrapper -->


                                                        <!-- Add to Cart -->
                                                        <div class="card-body">
                                                        
                                                        <div class="pull-right">

                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        
                                                        </div>
                                                        </div>
                                                        <!-- /.Add to Cart -->
                                                    </div>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
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
    $(".rotate-icon").click(function(){
        $(this).toggleClass("down"); 
    });
    
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