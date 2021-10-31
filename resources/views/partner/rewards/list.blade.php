@extends('layouts.partnerlay')
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
                        <h4 class="card-title">Reward History</h4>
                        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                {{-- <li><a href="{{url('admin/create/partner/reward')}}" class="btn btn-success mr-1 mb-1 ladda-button" data-style="expand-left"><i class="ft-plus white"></i> <span class="ladda-label">Add reward</span></a></li> --}}
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <div class="row">
                                <div class="col-lg-3 col-sm-12 border-right-blue-grey border-right-lighten-5">
                                    <div class="pb-1">
                                        <div class="clearfix mb-1">
                                            <i class="icon-star font-large-1 success float-left mt-1"></i>
                                            <span class="font-large-2 text-bold-300 success float-right">{{isset($total)?$total->total_reward:0}} AED</span>
                                        </div>
                                        <div class="clearfix">
                                            <span class="text-muted">Rewards</span>
                                            {{-- <span class="success float-right"><i class="ft-arrow-up success"></i> 16.89%</span> --}}
                                        </div>
                                    </div>
                                    <div class="progress mb-0" style="height: 7px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                &nbsp;&nbsp;&nbsp;
                                <div class="col-lg-3 col-sm-12">
                                    <div class="pb-1">
                                        <div class="clearfix mb-1">
                                            <i class="icon-wallet font-large-1 blue-grey float-left mt-1"></i>
                                            <span class="font-large-2 text-bold-300 warning float-right">{{isset($total)?$total->total_redeem:0}} AED</span>
                                        </div>
                                        <div class="clearfix">
                                            <span class="text-muted">Redeem</span>
                                            {{-- <span class="warning float-right"><i class="ft-arrow-up warning"></i> 43.84%</span> --}}
                                        </div>
                                    </div>
                                    <div class="progress mb-0" style="height: 7px;">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 100%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                            <br><br>
                            <table class="table table-striped table-bordered dom-jQuery-events dataTable" id="DataTables" role="grid" aria-describedby="DataTables_Table_0_info">
                                <thead>
                                    <tr role="row">
                                        <th>reward</th>
                                        <th>Point</th>
                                        <th>Cupon</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rewards as $reward)
                                    <tr role="row" class="odd">
                                        <td>{{$reward->rewarddetail->heading}}</td>
                                        <td>{{$reward->rewarddetail->point}} <b>AED</b></td>
                                        <td>@if(isset($reward->rewarddetail->image))<a href="{{$reward->rewarddetail->image}}" target="_blank">View Cupon</a>@else No cupon available @endif</td>
                                    </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="pull-right">
                                {!! $rewards->render() !!}
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