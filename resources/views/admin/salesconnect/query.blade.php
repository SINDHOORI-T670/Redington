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
    .chats .chat-left .chat-avatar {
    float: left; }
  .chats .chat-left .chat-body {
    margin-right: 0;
    margin-left: 30px; }
  .chats .chat-left .chat-content {
    text-align: left;
    float: left;
    margin: 0 0 10px 20px;
    color: #55595c;
    background-color: #c1c1c1 !important; }
    .chats .chat-left .chat-content + .chat-content:before {
      border-color: transparent; }
    .chats .chat-left .chat-content:before {
      right: auto;
      left: -10px;
      border-right-color: white;
      border-left-color: transparent; }
</style>
<div class="app-content content">
    <div class="content-wrapper">
        <br>
        @include('alert.messages')
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Queries</h4>
                        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                {{-- <li><a data-toggle="modal" data-target="#createQueryModal"  href="#" class="btn btn-success mr-1 mb-1 ladda-button" data-style="expand-left"><i class="ft-plus white"></i> <span class="ladda-label">Add Preset Question</span></a></li> --}}
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
                                        <th>Query</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($list as $index => $item)
                                    <tr role="row" class="odd">
                                        <td>{{$index+1}}</td>
                                        <td>{!!$item->question!!}</td>
                                        <td><h4 @if($item->status==1) class="danger" @else class="success" @endif>{{($item->status==1)?"Inactive":"Active"}}</h4></td>
                                        <td>
                                            
                                            {{-- <a class="btn btn-secondary requestlist" href="{{url('admin/query_request')}}/{{$item->id}}"><i class="icon-eye mr-1"></i> Request</a>  --}}
                                            <a class="btn btn-primary text-white tab-order" data-toggle="modal" data-target="#ViewRequestModal{{$item->id}}"  href="#"><i class="fa fa-eye"></i> View</a>
                                            <div class="modal fade text-left show" id="ViewRequestModal{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" style="padding-right: 17px;">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3 class="modal-title" id="myModalLabel35"> Requests From  Sales Connects</h3>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">×</span>
                                                        </button>
                                                    </div>
                                                    <form method="POST" action="{{url('admin/query/reply')}}/{{$item->id}}">
                                                        @csrf
                                                        <div class="modal-body">
                                                        {{-- <fieldset class="form-group floating-label-form-group"> --}}
                                                            {{-- <label for="email" class="label-control">Request</label>
                                                            <textarea class="form-control Query" id="query{{$item->id}}" name="query" rows="10" columns="10" placeholder="Preset Question"></textarea>
                                                                @if ($errors->has('query'))
                                                                    <span class="help-block">
                                                                        <strong class="error">{{ $errors->first('query') }}</strong>
                                                                    </span>
                                                                @endif --}}
                                                                {{-- <div class="chat-app-window"> --}}
                                                                    <div class="chats">
                                                                        <div class="chat">
                                                                            <div class="chat-avatar">
                                                                            <a class="avatar" data-toggle="tooltip" href="#" data-placement="right" title="" data-original-title="">
                                                                                <img src="@if(isset(Auth::user()->image)){{url(Auth::user()->image)}} @else {{asset('admin/app-assets/images/portrait/small/avatar-s-1.png')}} @endif" alt="avatar">
                                                                            </a>
                                                                            </div>
                                                                            <div class="chat-body">
                                                                            <div class="chat-content" style="text-align: left">
                                                                                <p>{!!$item->question!!}</p>
                                                                            </div>
                                                                            </div>
                                                                        </div>
                                                                        @foreach($item->request as $reply)
                                                                            <div class="chat @if($reply->from_id!=Auth::User()->id) chat-left @endif">
                                                                                <div class="chat-avatar">
                                                                                <a class="avatar" data-toggle="tooltip" href="#" data-placement="right" title="" data-original-title="">
                                                                                    <img src="@if(isset($reply->user->image)){{url($reply->user->image)}} @else {{asset('admin/app-assets/images/portrait/small/avatar-s-1.png')}} @endif" alt="avatar">
                                                                                </a>
                                                                                </div>
                                                                                <div class="chat-body">
                                                                                <div class="chat-content">
                                                                                    <p>{!!$reply->reply!!}</p>
                                                                                </div>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                {{-- </div> --}}
                                                        {{-- </fieldset> --}}
                                                        
                                                        <fieldset class="form-group floating-label-form-group">
                                                            <label for="title1" class="label-control required">Reply</label>
                                                            <textarea class="form-control detail2" id="reply{{$item->id}}" name="reply"></textarea>
                                                                @if ($errors->has('reply'))
                                                                    <span class="help-block">
                                                                        <strong class="error">{{ $errors->first('reply') }}</strong>
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
                                               
                                        </td>
                                    </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="pull-right">
                                {!!$list->render() !!}
                            </div>
                            {{-- <div class="modal fade text-left show" id="createQueryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" style="padding-right: 17px;">
                                <div class="modal-dialog" role="document">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h3 class="modal-title" id="myModalLabel35"> New Preset Question</h3>
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                      </button>
                                    </div>
                                    <form method="POST" action="{{url('admin/add/new/query')}}/{{$sale}}">
                                        @csrf
                                      <div class="modal-body">
                                          <fieldset class="form-group floating-label-form-group">
                                              <label for="email" class="label-control required">Preset Question</label>
                                              <textarea class="form-control" id="query" name="query" placeholder="Preset Question"></textarea>
                                                @if ($errors->has('query'))
                                                    <span class="help-block">
                                                        <strong class="error">{{ $errors->first('query') }}</strong>
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
                            </div> --}}
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
    CKEDITOR.replace( 'query' );
    // CKEDITOR.replace( 'detail2' );
    $('.Query').each(function () {
        CKEDITOR.replace($(this).prop('id'));
    });
    $('.detail2').each(function () {
        CKEDITOR.replace($(this).prop('id'));
    });
    
</script>

@endsection