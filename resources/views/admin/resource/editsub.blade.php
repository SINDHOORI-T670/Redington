@extends('layouts.adminlay')
@section('content')
<style>
    .required:after {
        content: "*";
        color: red;
    }
    .error{
        color:red;
    }
    .remove{
        display:block;
        float:right;
        width:30px;
        height:29px;
        background:url(https://web.archive.org/web/20110126035650/http://digitalsbykobke.com/images/close.png) no-repeat center center;
    }
</style>
<div class="app-content content">
    <div class="content-wrapper">
        <br>
        @include('alert.messages')
        <div class="content-body">
            <section id="horizontal-form-layouts">
                <div class="row justify-content-md-center">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title" id="horz-layout-card-center">Edit SubResource</h4>
                                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                            </div><br><br><br>
                            <div class="card-content collpase show">
                                <div class="card-body">
                                    <form class="form form-horizontal"  action="{{url('admin/edit/subresource')}}/{{$item->id}}" method="post" enctype="multipart/form-data"> 
                                        @csrf
                                        <div class="form-body">
                                            <div class="form-group row">
                                                <label class="col-md-3 label-control required" for="eventRegInput1">Name</label>
                                                <div class="col-md-9">
                                                    <input type="text" class="form-control" id="subresourcename1" name="name" placeholder="Sub Resource Name" value="{{$item->heading}}">
                                                      @if ($errors->has('subresourcename'))
                                                          <span class="help-block">
                                                              <strong class="error">{{ $errors->first('subresourcename') }}</strong>
                                                          </span>
                                                      @endif
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-md-3 label-control" for="eventRegInput4">Detail</label>
                                                <div class="col-md-9">
                                                    <textarea cols="30" rows="15" class="form-control detail2" id="detail2" name="detail2" placeholder="Details about subresource">{!! $item->details !!}</textarea>
                                                    @if ($errors->has('detail2'))
                                                        <span class="help-block">
                                                            <strong class="error">{{ $errors->first('detail2') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-md-3 label-control" for="eventRegInput4">Upload Files</label>
                                                <div class="col-md-9" id="upload-form">
                                                    <input type="file" class="form-control uploadFile" id="uploadFile12" name="file12[]" placeholder="Subresource files" accept="application/pdf,application/msword,
                                                    application/vnd.openxmlformats-officedocument.wordprocessingml.document" multiple>
                                                        <ul class="list-inline mb-0">  
                                                            @if(isset($item->subfiles))
                                                                {{-- @forelse(explode(',',$item->file) as $index => $file)--}}
                                                                    @forelse($item->subfiles as $index => $file)
                                                                    @php 
                                                                        $type = pathinfo($file->file, PATHINFO_EXTENSION);
                                                                        $split[] = explode('.',$file->file);
                                                                    @endphp 
                                                                    <li>
                                                                        <a  class="text-info" href="{{url('admin/downloadfile')}}/{{$file->file}}" title="File{{$index+1}}" download>{{$file->filename}} <i class="fa fa-file-{{$icons[$type]}}-o fa-1x text-center"/></i>&nbsp;&nbsp;</a> 
                                                                    </li>
                                                                @empty 
                                                                @endforelse
                                                            @endif
                                                        </ul>
                                                      @if ($errors->has('file12'))
                                                          <span class="help-block">
                                                              <strong class="error">{{ $errors->first('file12') }}</strong>
                                                          </span>
                                                      @endif
                                                </div>
                                            </div>
                                            <div id="image_preview"></div>
                                            <div class="form-group row" id="FileName">
                                               
                                            </div>
                                        </div>

                                        <div class="form-actions center">
                                            <a href="{{url('admin/subresource/list')}}/{{$item->resource_id}}" class="btn btn-warning mr-1">
                                                <i class="ft-x"></i> Cancel
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-check-square-o"></i> Save
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
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

<script type="text/javascript">
   CKEDITOR.replace( 'detail2' );
   $(".uploadFile").change(function(){
        var $this = $(this);
        $this.hide();
        var total_file=document.getElementById("uploadFile12").files.length;
        for(var i=0;i<total_file;i++)

        {
            
            var fileName = this.files[i].name;
            var divId = Math.floor((Math.random() * 100) + 1);
            $('#image_preview').append("<ul class=\"list-inline mb-0 \"><li class=\"pip\">" +
                "<span id='"+divId+"' class=\"remove\"></span>"+
                "<lable class=\"label-control\">" + fileName +"<lable>"+
                "</li></ul>");
            var selectbox = '<div class='+divId+'><label class="label-control required">File Name for ' + fileName + '</label> <br><input type="text" class="form-control" id="subresourcefilename" name="filenamearray[]" placeholder="File Name" required><br>';
                $('#FileName').append(selectbox);

        }

        $("#upload-form").append("<input type='file' class='form-control uploadFile' id='uploadMoreFile' name='file12[]' accept='application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document' multiple='multiple'>");

        $(".remove").click(function(){
            file = $(this).attr('id');
            $(this).parent(".pip").remove();
            $("."+file).remove();
            document.getElementById("uploadFile").value=null;
        });

    });
    $(document).on('change','#uploadMoreFile',function(){
        var $this = $(this);
        $this.hide();
        var total_file=document.getElementById("uploadMoreFile").files.length;
        for(var i=0;i<total_file;i++)

        {
            
            var fileName = this.files[i].name;
            var divId = Math.floor((Math.random() * 100) + 1);
            $('#image_preview').append("<ul class=\"list-inline mb-0 \"><li class=\"pip\">" +
                "<span id='"+divId+"' class=\"remove\"></span>"+
                "<lable class=\"label-control\">" + fileName +"<lable>"+
                "</li></ul>");
            var selectbox = '<div class='+divId+'><label class="label-control required">File Name for ' + fileName + '</label> <br><input type="text" class="form-control" id="subresourcefilename" name="filenamearray[]" placeholder="File Name" required><br>';
                $('#FileName').append(selectbox);

        }
        $(".remove").click(function(){
        file = $(this).attr('id');
        $(this).parent(".pip").remove();
        $("."+file).remove();
        });
        $("#upload-form").append("<input type='file' class='form-control uploadFile' id='uploadMoreFile' name='file12[]' accept='application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document' multiple='multiple'>");
   
    });

    
</script>
@endsection