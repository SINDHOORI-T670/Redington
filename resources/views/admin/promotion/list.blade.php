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
.required:after {
        content: "*";
        color: red;
    }
    .error{
        color:red;
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
                        <h4 class="card-title">Promotions</h4>
                        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-toggle="modal" data-target="#createPromotionModal"  href="#" class="btn btn-success mr-1 mb-1 ladda-button" data-style="expand-left"><i class="ft-plus white"></i> <span class="ladda-label">Add Promotion</span></a></li>
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            
                            <table class="table table-striped table-bordered dom-jQuery-events dataTable" id="DataTables" role="grid" aria-describedby="DataTables_Table_0_info">
                                <thead>
                                    <tr role="row">
                                        <th>Title</th>
                                        <th>Image</th>
                                        <th>From date</th>
                                        <th>To date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($list as $promotion)
                                    <tr role="row" class="odd">
                                        <td>{{$promotion->name}}</td>
                                        <td><img height="60" src="{{url($promotion->image)}}"></td>
                                        <td>{{Carbon\Carbon::parse($promotion->from_date)->format('j F Y h:i A')}}</td>
                                        <td>{{Carbon\Carbon::parse($promotion->to_date)->format('j F Y h:i A')}}</td>
                                        <td><h4 @if($promotion->status==1) class="danger text-center" @else class="success text-center" @endif>{{($promotion->status==1)?"Inactive":"Active"}}</h4></td>
                                        <td>
                                            <ul class="list-inline mb-0">
                                                <li><a class="btn btn-primary text-white tab-order" data-toggle="modal" data-target="#editPromotionModal{{$promotion->id}}"  href="#" title="edit"><i class="icon-pencil"></i> Edit</a></li>
                                                <li>
                                                    <button @if($promotion->status==0) class="btn btn-danger text-white tab-order" @else class="btn btn-success text-white tab-order" @endif onclick="confirmDelete('event-active-{{ $promotion->id }}','{{ $promotion->name }}','{{ $promotion->status }}');"> @if($promotion->status==0) <i class="fa fa-thumbs-o-down"></i> Inactive @else <i class="fa fa-thumbs-o-up"></i> Active @endif</button>
                                                    <form id="event-active-{{ $promotion->id }}" action="{{url('admin/active/promotion/')}}/{{$promotion->id}}" method="get">
                                                        {{ csrf_field() }}
                                                    </form>
                                                </li>
                                                <li><a href="{{url('admin/request_for')}}/{{'Promotion'}}/{{$promotion->id}}" class="btn btn-info text-white tab-order" title="Requests"><i class="icon-list"></i> Requests</a></li>
                                            </ul>
                                            
                                        </td>
                                        
                                    </tr>
                                    <div class="modal fade text-left show" id="editPromotionModal{{$promotion->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" style="padding-right: 17px;">
                                        <div class="modal-dialog" role="document">
                                          <div class="modal-content">
                                            <div class="modal-header">
                                              <h3 class="modal-title" id="myModalLabel35"> Edit Promotion</h3>
                                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                              </button>
                                            </div>
                                            <form method="POST" action="{{url('admin/update/promotion')}}/{{$promotion->id}}" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-body">
                                                    <fieldset class="form-group floating-label-form-group">
                                                        <label for="promotionname" class="label-control required">Promotion Title</label>
                                                        <input type="text" class="form-control" id="promotionname" name="promotionname" placeholder="Promotion Title" value="{{$promotion->name}}">
                                                          @if ($errors->has('promotionname'))
                                                              <span class="help-block">
                                                                  <strong class="error">{{ $errors->first('promotionname') }}</strong>
                                                              </span>
                                                          @endif
                                                    </fieldset>
                                                    <br>
                                                    <fieldset class="form-group floating-label-form-group">
                                                        <label for="title1" class="label-control required"> Description</label>
                                                        <textarea class="form-control detail1" id="promotiondescription{{$promotion->id}}" name="promotiondescription" placeholder="Promotion Description">{!! $promotion->description !!}</textarea>
                                                        @if ($errors->has('promotiondescription'))
                                                            <span class="help-block">
                                                                <strong class="error">{{ $errors->first('promotiondescription') }}</strong>
                                                            </span>
                                                        @endif
                                                    </fieldset>
                                                      <fieldset class="form-group floating-label-form-group">
                                                        <img class="card-img-top img-fluid" src="{{url($promotion->image)}}" alt="{{$promotion->title}}">
                                                          <label for="file" class="label-control required">Image</label>
                                                          <input type="file" class="form-control" id="file" name="image" placeholder="files">
                                                          @if ($errors->has('image'))
                                                              <span class="help-block">
                                                                  <strong class="error">{{ $errors->first('image') }}</strong>
                                                              </span>
                                                          @endif
                                                      </fieldset>
                                                        <fieldset class="form-group floating-label-form-group">
                                                          <label for="email" class="label-control required">From Date</label>
                                                          <input type="date" class="form-control" id="date" name="date1" min="<?php echo date("Y-m-d"); ?>" value="<?php echo date('Y-m-d',strtotime($promotion->from_date)) ?>">
                                                            @if ($errors->has('date1'))
                                                                <span class="help-block">
                                                                    <strong class="error">{{ $errors->first('date1') }}</strong>
                                                                </span>
                                                            @endif
                                                        </fieldset>
                                                        <fieldset class="form-group floating-label-form-group">
                                                            <label for="email" class="label-control required">To Date</label>
                                                            <input type="date" class="form-control" id="date" name="date2" min="<?php echo date("Y-m-d"); ?>" value="<?php echo date('Y-m-d',strtotime($promotion->to_date)) ?>">
                                                              @if ($errors->has('date2'))
                                                                  <span class="help-block">
                                                                      <strong class="error">{{ $errors->first('date2') }}</strong>
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
                                {!! $list->render() !!}
                            </div>
                        </div>
                    </div>
                    <div class="modal fade text-left show" id="createPromotionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" style="padding-right: 17px;">
                        <div class="modal-dialog" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h3 class="modal-title" id="myModalLabel35"> Create Promotion</h3>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                              </button>
                            </div>
                            <form method="POST" action="{{url('admin/add/new/promotion')}}" enctype="multipart/form-data">
                                @csrf
                              <div class="modal-body">
                                  <fieldset class="form-group floating-label-form-group">
                                      <label for="promotionname" class="label-control required">Promotion Title</label>
                                      <input type="text" class="form-control" id="promotionname" name="promotionname" placeholder="Promotion Title">
                                        @if ($errors->has('promotionname'))
                                            <span class="help-block">
                                                <strong class="error">{{ $errors->first('promotionname') }}</strong>
                                            </span>
                                        @endif
                                  </fieldset>
                                  <br>
                                  <fieldset class="form-group floating-label-form-group">
                                        <label for="title1" class="label-control required">Description</label>
                                        <textarea class="form-control" id="promotiondescription1" name="promotiondescription1" placeholder="Promotion Description"></textarea>
                                        @if ($errors->has('promotiondescription1'))
                                            <span class="help-block">
                                                <strong class="error">{{ $errors->first('promotiondescription1') }}</strong>
                                            </span>
                                        @endif
                                    </fieldset>
                                  
                                    <fieldset class="form-group floating-label-form-group">
                                        <label for="file" class="label-control required">Image</label>
                                        <input type="file" class="form-control" id="file" name="image" placeholder="files">
                                        @if ($errors->has('image'))
                                            <span class="help-block">
                                                <strong class="error">{{ $errors->first('image') }}</strong>
                                            </span>
                                        @endif
                                    </fieldset>
                                    <fieldset class="form-group floating-label-form-group">
                                        <label for="email" class="label-control required">From Date</label>
                                        <input type="date" class="form-control" id="date" name="date1" min="<?php echo date("Y-m-d"); ?>" >
                                          @if ($errors->has('date1'))
                                              <span class="help-block">
                                                  <strong class="error">{{ $errors->first('date1') }}</strong>
                                              </span>
                                          @endif
                                      </fieldset>
                                      <fieldset class="form-group floating-label-form-group">
                                          <label for="email" class="label-control required">To Date</label>
                                          <input type="date" class="form-control" id="date" name="date2" min="<?php echo date("Y-m-d"); ?>" >
                                            @if ($errors->has('date2'))
                                                <span class="help-block">
                                                    <strong class="error">{{ $errors->first('date2') }}</strong>
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
<script src="https://cdn.jsdelivr.net/bootstrap.tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js"></script>
<script>
    CKEDITOR.replace( 'promotiondescription1' );

    $('.detail1').each(function () {
        CKEDITOR.replace($(this).prop('id'));
    });
</script>
<script>
    function confirmDelete(id,name) {
        if(status==0){
            var text="inactive";
        }else{
            var text="active";
        }
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to '+text+' the '+name+'?',
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