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
                                @if($type==3)<li><a href="#" class="btn btn-warning mr-1 mb-1 ladda-button" data-style="expand-left" data-toggle="modal" data-target="#ApplyRewardModal"><i class="ft-plus white"></i> <span class="ladda-label">Apply Reward</span></a></li>@endif
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            </ul>
                        </div>
                        <div class="modal fade text-left show" id="ApplyRewardModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" style="padding-right: 17px;">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h3 class="modal-title" id="myModalLabel35"> Apply Rewards</h3>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                  </button>
                                </div>
                                <form method="POST" action="{{url('admin/Redington/Apply/Rewards')}}">
                                    @csrf
                                  <div class="modal-body">
                                      <fieldset class="form-group floating-label-form-group">
                                          <label for="partner" class="label-control required">Partners</label>
                                          <br><select class="form-control select2-multi" name="partner[]" multiple="multiple">
                                                @foreach($users as $user)
                                                    <option value="{{$user->id}}">{{$user->name}}</option>
                                                @endforeach
                                          </select>
                                            @if ($errors->has('partner'))
                                                <span class="help-block">
                                                    <strong class="error">{{ $errors->first('partner') }}</strong>
                                                </span>
                                            @endif
                                      </fieldset>
                                      <br>
                                      <fieldset class="form-group floating-label-form-group">
                                        <label for="reward" class="label-control required">Reward</label>
                                        <select name="reward" id="rewards" class="form-control">
                                              <option value="">Select any reward</option>
                                              @foreach($rewards as $reward)
                                                  <option value="{{$reward->id}}">{{$reward->heading}}</option>
                                              @endforeach
                                        </select>
                                          @if ($errors->has('reward'))
                                              <span class="help-block">
                                                  <strong class="error">{{ $errors->first('reward') }}</strong>
                                              </span>
                                          @endif
                                      </fieldset>
                                      <fieldset class="form-group floating-label-form-group">
                                        <label for="rewardpoint" class="label-control required">Point</label>
                                        <input type="text" class="form-control" name="rewardpoint" id="rewardpoint" value="" readonly>
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
                                    @forelse($users as $user)
                                    <tr role="row" class="odd">
                                        <td>{{$user->name}}</td>
                                        <td>{{$user->email}}</td>
                                        <td>{{$user->phone}}</td>
                                        <td><h4 @if($user->status==0) class="success text-center" @else class="danger text-center" @endif>{{($user->status==0)?"Active":"Inactive"}}</h4></td>
                                        <td>
                                            <a class="btn btn-primary text-white tab-order" href="{{url('admin/edit/user/')}}/{{$user->id}}"><i class="icon-pencil"></i> Edit</a>
                                            @if($user->type==3)
                                            {{-- <a class="btn btn-secondary text-white tab-order " href="{{url('admin/redeem/history/')}}/{{$user->id}}"><i class="fa fa-money"></i> Redeem History</a> --}}
                                            <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="icon-settings mr-1"></i>History</button>
                                                <div class="dropdown-menu arrow" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                    <a class="dropdown-item" href="{{url('admin/reward/history/')}}/{{$user->id}}"><i class="icon-trophy mr-1"></i> Rewards</a> 
                                                    <a class="dropdown-item" href="{{url('admin/redeem/history/')}}/{{$user->id}}"><i class="fa fa-money mr-1"></i> Redeems</a>
                                                </div>
                                            @endif
                                            {{-- <button class="btn btn-danger text-white tab-order" onclick="confirmDelete('resource-delete-{{ $user->id }}','{{ $user->name }}');"><i class="icon-trash"></i> Delee</button>
                                            <form id="resource-delete-{{ $user->id }}" action="{{url('admin/delete/user/')}}/{{$user->id}}" method="get">
                                                {{ csrf_field() }}
                                            </form> --}}

                                            <button @if($user->status==0) class="btn btn-danger text-white tab-order" @else class="btn btn-success text-white tab-order" @endif onclick="confirmDelete('resource-active-{{ $user->id }}','{{ $user->name }}','{{ $user->status }}');"> @if($user->status==0) <i class="fa fa-thumbs-o-down"></i> Inctive @else <i class="fa fa-thumbs-o-up"></i> Active @endif</button>
                                            <form id="resource-active-{{ $user->id }}" action="{{url('admin/active/user/')}}/{{$user->id}}" method="get">
                                                {{ csrf_field() }}
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    @endforelse
                                    {!! $users->render() !!}
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
    
    $(document).ready(function() {
        $.noConflict();
        $(document).on('change', '#rewards', function() {
                var reward_id =  $('#rewards').val();     // get id the value from the select
                $.ajax({
                    url:"{{route('getRewardPoint')}}",
                    method:"get",
                    data:{reward_id:reward_id},
                    success:function(data)
                    {
                        console.log(data);
                        $('#rewardpoint').val(data);
                    } 
                }) 
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