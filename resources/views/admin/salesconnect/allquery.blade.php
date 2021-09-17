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
                        <h4 class="card-title">Queries</h4>
                        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-toggle="modal" data-target="#createQueryModal"  href="#" class="btn btn-success mr-1 mb-1 ladda-button" data-style="expand-left"><i class="ft-plus white"></i> <span class="ladda-label">Add Preset Question</span></a></li>
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
                                        <th>Technology</th>
                                        <th>Brand</th>
                                        <th>Query</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($list as $index => $item)
                                        @php 
                                            $count = App\Models\QueryRequest::where('query_id',$item->id)->where('read_status',0)->count();
                                            
                                        @endphp
                                    <tr role="row" class="odd">
                                        <td>{{$index+1}}</td>
                                        <td>{{isset($item->technology)?$item->technology->name:"--"}}</td>
                                        <td>{{isset($item->brand)?$item->brand->name:"--"}}</td>
                                        <td>{!!$item->question!!}</td>
                                        <td><h4 @if($item->status==1) class="danger" @else class="success" @endif>{{($item->status==1)?"Inactive":"Active"}}</h4></td>
                                        <td>
                                            <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#QueryEditModal{{$item->id}}"><i class="icon-pencil mr-1"></i>Edit</a>
                                        </td>
                                    </tr>
                                    <div class="modal fade text-left show" id="QueryEditModal{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" style="padding-right: 17px;">
                                        <div class="modal-dialog" role="document">
                                          <div class="modal-content">
                                            <div class="modal-header">
                                              <h3 class="modal-title" id="myModalLabel35"> Edit Preset Question</h3>
                                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                              </button>
                                            </div>
                                            <form method="POST" action="{{url('admin/edit/query')}}/{{$item->id}}">
                                                @csrf
                                              <div class="modal-body">
                                                <fieldset class="form-group floating-label-form-group">
                                                    <label for="email" class="label-control required">Technology</label>
                                                    <select class="form-control" name="tech">
                                                        @forelse ($techs as $tech)
                                                            <option value="{{$tech->id}}" @if($tech->id==$item->tech_id) selected @else @endif>{{$tech->name}}</option>
                                                        @empty
                                                            
                                                        @endforelse
                                                    </select>
                                                </fieldset>
                                                <fieldset class="form-group floating-label-form-group">
                                                    <label for="email" class="label-control required">Brand</label>
                                                    <select class="form-control" name="brand">
                                                        @forelse ($brands as $brand)
                                                            <option value="{{$brand->id}}" @if($brand->id==$item->brand_id) selected @else @endif>{{$brand->name}}</option>
                                                        @empty
                                                            
                                                        @endforelse
                                                    </select>
                                                </fieldset>
                                                <fieldset class="form-group floating-label-form-group">
                                                    <label for="email" class="label-control required">Preset Question</label>
                                                    <textarea class="form-control Query" id="query{{$item->id}}" name="query" placeholder="Preset Question">{!!$item->question!!}</textarea>
                                                      @if ($errors->has('query'))
                                                          <span class="help-block">
                                                              <strong class="error">{{ $errors->first('query') }}</strong>
                                                          </span>
                                                      @endif
                                                </fieldset>
                                                <fieldset class="form-group floating-label-form-group">
                                                    <label for="email" class="label-control">Status</label>
                                                    <select class="form-control" name="status">
                                                            <option value="0" @if($item->status==0) selected @endif>Active</option>
                                                            <option value="1" @if($item->status==1) selected @endif>Inactive</option>
                                                    </select>
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
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="pull-right">
                                {!!$list->render() !!}
                            </div>
                            <div class="modal fade text-left show" id="createQueryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" style="padding-right: 17px;">
                                <div class="modal-dialog" role="document">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h3 class="modal-title" id="myModalLabel35"> New Preset Question</h3>
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                      </button>
                                    </div>
                                    <form method="POST" action="{{url('admin/add/new/query')}}">
                                        @csrf
                                      <div class="modal-body">
                                        <fieldset class="form-group floating-label-form-group">
                                            <label for="email" class="label-control required">Technology</label>
                                            <select class="form-control" name="tech">
                                                @forelse ($techs as $tech)
                                                    <option value="{{$tech->id}}" >{{$tech->name}}</option>
                                                @empty
                                                    
                                                @endforelse
                                            </select>
                                        </fieldset>
                                        <fieldset class="form-group floating-label-form-group">
                                            <label for="email" class="label-control required">Brand</label>
                                            <select class="form-control" name="brand">
                                                @forelse ($brands as $brand)
                                                    <option value="{{$brand->id}}" >{{$brand->name}}</option>
                                                @empty
                                                    
                                                @endforelse
                                            </select>
                                        </fieldset>
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
    CKEDITOR.replace( 'query' );
    // CKEDITOR.replace( 'detail2' );
    $('.Query').each(function () {
        CKEDITOR.replace($(this).prop('id'));
    });
</script>

@endsection