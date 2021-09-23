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
                        <h4 class="card-title">Latest Events</h4>
                        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-toggle="modal" data-target="#createEventModal"  href="#" class="btn btn-success mr-1 mb-1 ladda-button" data-style="expand-left"><i class="ft-plus white"></i> <span class="ladda-label">Add Event</span></a></li>
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <ul class="list-inline">
                                <li class="pr-1"><a href="{{url('admin/list/new/events')}}" class="list-group-item active">New Events</a></li>
                                <li class="pr-1"><a href="{{url('admin/list/past/events')}}" class="list-group-item list-group-item-action">Past Events</a></li>
                            </ul>
                            <table class="table table-striped table-bordered dom-jQuery-events dataTable" id="DataTables" role="grid" aria-describedby="DataTables_Table_0_info">
                                <thead>
                                    <tr role="row">
                                        <th>Title</th>
                                        <th>Added By</th>
                                        <th>Image</th>
                                        <th>Date Time</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($events as $event)
                                    <tr role="row" class="odd">
                                        <td>{{$event->title}}</td>
                                        <td>{{$event->user->name}}</td>
                                        <td><img height="60" src="{{url($event->image)}}"></td>
                                        <td>{{Carbon\Carbon::parse($event->date_time)->format('j F Y h:i A')}}</td>
                                        <td><h4 @if($event->status==1) class="danger text-center" @else class="success text-center" @endif>{{($event->status==1)?"Inactive":"Active"}}</h4></td>
                                        <td>
                                            <a class="btn btn-primary text-white tab-order" data-toggle="modal" data-target="#editEventModal{{$event->id}}"  href="#"><i class="icon-pencil"></i> Edit</a>
                                            <button @if($event->status==0) class="btn btn-danger text-white tab-order" @else class="btn btn-success text-white tab-order" @endif onclick="confirmDelete('service-active-{{ $event->id }}','{{ $event->name }}','{{ $event->status }}');"> @if($event->status==0) <i class="fa fa-thumbs-o-down"></i> Inactive @else <i class="fa fa-thumbs-o-up"></i> Active @endif</button>
                                            <form id="service-active-{{ $event->id }}" action="{{url('admin/active/service/')}}/{{$event->id}}" method="get">
                                                {{ csrf_field() }}
                                            </form>
                                        </td>
                                    </tr>
                                    <div class="modal fade text-left show" id="editEventModal{{$event->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" style="padding-right: 17px;">
                                        <div class="modal-dialog" role="document">
                                          <div class="modal-content">
                                            <div class="modal-header">
                                              <h3 class="modal-title" id="myModalLabel35"> Edit Event</h3>
                                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                              </button>
                                            </div>
                                            <form method="POST" action="{{url('admin/update/event')}}/{{$event->id}}">
                                                @csrf
                                                <div class="modal-body">
                                                    <fieldset class="form-group floating-label-form-group">
                                                        <label for="eventname" class="label-control required">Event Title</label>
                                                        <input type="text" class="form-control" id="eventname" name="eventname" placeholder="Event Title" value="{{$event->title}}">
                                                          @if ($errors->has('eventname'))
                                                              <span class="help-block">
                                                                  <strong class="error">{{ $errors->first('eventname') }}</strong>
                                                              </span>
                                                          @endif
                                                    </fieldset>
                                                    <br>
                                                    <fieldset class="form-group floating-label-form-group">
                                                        <label for="title1" class="label-control required">Event Description</label>
                                                        <textarea class="form-control" id="eventdescription" name="eventdescription" placeholder="Event Description">{!! $event->description !!}</textarea>
                                                          @if ($errors->has('eventdescription'))
                                                              <span class="help-block">
                                                                  <strong class="error">{{ $errors->first('eventdescription') }}</strong>
                                                              </span>
                                                          @endif
                                                    </fieldset>
                                                      <fieldset class="form-group floating-label-form-group">
                                                        <img class="card-img-top img-fluid" src="{{url($event->image)}}" alt="{{$event->title}}">
                                                          <label for="file" class="label-control required">Image</label>
                                                          <input type="file" class="form-control" id="file" name="image" placeholder="files">
                                                          @if ($errors->has('image'))
                                                              <span class="help-block">
                                                                  <strong class="error">{{ $errors->first('image') }}</strong>
                                                              </span>
                                                          @endif
                                                      </fieldset>
                                                      <fieldset class="form-group floating-label-form-group">
                                                          <label for="file1" class="label-control">Doument Upload</label>
                                                          <input type="file" class="form-control" id="file1" name="doc[]" placeholder="Document upload" accept="application/pdf,application/msword,
                                                          application/vnd.openxmlformats-officedocument.wordprocessingml.document" multiple>
                                                          @if ($errors->has('doc'))
                                                              <span class="help-block">
                                                                  <strong class="error">{{ $errors->first('doc') }}</strong>
                                                              </span>
                                                          @endif
                                                          <br>
                                                            <ul class="list-inline mb-0 text-white">
                                                                @if(isset($event->document))
                                                                    @forelse(explode(',',$event->document) as $index => $file)
                                                                        @php 
                                                                            $type = pathinfo($file, PATHINFO_EXTENSION);
                                                                            $split[] = explode('.',$file);
                                                                        @endphp
                                                                        <li>
                                                                            <a  class="text-primary" href="{{url('uploads/event/documents')}}/{{$file}}" title="File{{$index+1}}" download>File{{$index+1}} <i class="fa fa-file-{{$icons[$type]}}-o fa-1x text-center"/></i>&nbsp;&nbsp;</a> 
                                                                        </li>
                                                                    @empty 
                                                                    @endforelse
                                                                @endif
                                                            </ul>
                                                            <br>
                                                      </fieldset>
                                                      <fieldset class="form-group floating-label-form-group">
                                                          <label for="email" class="label-control required">Date</label>
                                                          <input type="date" class="form-control" id="date" name="date" value="<?php echo date('Y-m-d',strtotime($event->date_time)) ?>">
                                                            @if ($errors->has('date'))
                                                                <span class="help-block">
                                                                    <strong class="error">{{ $errors->first('date') }}</strong>
                                                                </span>
                                                            @endif
                                                        </fieldset>
                                                        <br>
                                                      <fieldset class="form-group floating-label-form-group">
                                                          <label for="title1" class="label-control required">Time</label>
                                                          <input class="form-control" type="time" name="time" id="time" value="{{ Carbon\Carbon::parse($event->date_time)->format('h:i') }}">
                                                            @if ($errors->has('time'))
                                                                <span class="help-block">
                                                                    <strong class="error">{{ $errors->first('time') }}</strong>
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
                                {!! $events->render() !!}
                            </div>
                        </div>
                    </div>
                    <div class="modal fade text-left show" id="createEventModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" style="padding-right: 17px;">
                        <div class="modal-dialog" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h3 class="modal-title" id="myModalLabel35"> Create Event</h3>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                              </button>
                            </div>
                            <form method="POST" action="{{url('admin/add/new/event')}}" enctype="multipart/form-data">
                                @csrf
                              <div class="modal-body">
                                  <fieldset class="form-group floating-label-form-group">
                                      <label for="eventname" class="label-control required">Event Title</label>
                                      <input type="text" class="form-control" id="eventname" name="eventname" placeholder="Event Title">
                                        @if ($errors->has('eventname'))
                                            <span class="help-block">
                                                <strong class="error">{{ $errors->first('eventname') }}</strong>
                                            </span>
                                        @endif
                                  </fieldset>
                                  <br>
                                  <fieldset class="form-group floating-label-form-group">
                                      <label for="title1" class="label-control required">Event Description</label>
                                      <textarea class="form-control" id="eventdescription" name="eventdescription" placeholder="Event Description"></textarea>
                                        @if ($errors->has('eventdescription'))
                                            <span class="help-block">
                                                <strong class="error">{{ $errors->first('eventdescription') }}</strong>
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
                                        <label for="file1" class="label-control">Doument Upload</label>
                                        <input type="file" class="form-control" id="file1" name="doc[]" placeholder="Document upload" accept="application/pdf,application/msword,
                                        application/vnd.openxmlformats-officedocument.wordprocessingml.document" multiple>
                                        @if ($errors->has('doc'))
                                            <span class="help-block">
                                                <strong class="error">{{ $errors->first('doc') }}</strong>
                                            </span>
                                        @endif
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
    CKEDITOR.replace( 'eventdescription' );
    // CKEDITOR.replace( 'detail2' );

    $('.detail2').each(function () {
        CKEDITOR.replace($(this).prop('id'));
    });
    var today = new Date().toISOString().split('T')[0];
    document.getElementsByName("date")[0].setAttribute('min', today);
    function confirmDelete(id,name) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to remove this service '+name+'?',
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