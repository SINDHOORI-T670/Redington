@extends('layouts.adminlay')

@section('content')

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css">

<link rel="stylesheet" href="http://bootstrap-tagsinput.github.io/bootstrap-tagsinput/dist/bootstrap-tagsinput.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" />

<style>
    
    .required:after {

        content: "*";

        color: red;

    }

    .error{

        color:red;

    }

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

    .remove{

        display:block;

        float:right;

        width:30px;

        height:29px;

        background:url(https://web.archive.org/web/20110126035650/http://digitalsbykobke.com/images/close.png) no-repeat center center;

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

                        <h4 class="card-title"><a href="{{url('admin/resource/list')}}"><i class="fa fa-arrow-left"></i></a> &nbsp;&nbsp;Sub Resources</h4>

                        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                        <div class="heading-elements">

                            <ul class="list-inline mb-0">

                                <li><a data-toggle="modal" data-target="#createSubResourceModal" class="btn btn-success mr-1 mb-1 ladda-button" data-style="expand-left"><i class="ft-plus white"></i> <span class="ladda-label">Add Sub Resource</span></a></li>

                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>

                            </ul>

                        </div>

                    </div>

                    <div class="card-content collapse show">

                        <div class="card-body card-dashboard">

                            <section id="minimal-statistics-bg">

                                <div class="row">

                                    @forelse($list as $item)

                                    <div class="col-xl-3 col-lg-6 col-12">

                                        <div class="card {{$item->id % 2 == 0 ? 'bg-info':'bg-warning'}}">

                                            <div class="card-content">

                                                <div class="card-body">

                                                    <div class="media d-flex">

                                                        <div class="media-body text-white text-left">

                                                            <h3 class="text-white">{{ucfirst(trans($item->heading))}}</h3>

                                                            <span>{!! \Illuminate\Support\Str::limit($item->details, 50) !!}</span>

                                                            <br>

                                                            <ul class="list-inline mb-0 text-white">

                                                                @if(isset($item->subfiles))
                                                                    {{-- @forelse(explode(',',$item->file) as $index => $file)--}}
                                                                     @forelse($item->subfiles as $index => $file)
                                                                        @php 
                                                                            $type = pathinfo($file->file, PATHINFO_EXTENSION);
                                                                            $split[] = explode('.',$file->file);
                                                                        @endphp 
                                                                        <li>
                                                                            <a  class="text-white" href="{{$file->file}}" title="{{$file->filename}}" target="_blank">{{$file->filename}} <i class="fa fa-file-{{$icons[$type]}}-o fa-1x text-center"/></i>&nbsp;&nbsp;</a> 
                                                                        </li>
                                                                    @empty 
                                                                    @endforelse
                                                                @endif

                                                            </ul>

                                                            <br>

                                                            <ul class="list-inline mb-0 text-white">

                                                                <li>
                                                                    <a href="{{url('admin/edit/subresource')}}/{{$item->id}}" class="btn btn-secondary">Edit</a>
                                                                    {{-- <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#editsubResourceModal{{$item->id}}">

                                                                        Edit

                                                                    </button> --}}

                                                                </li>

                                                                <li>

                                                                    <button @if($item->status==0) class="btn btn-success text-white tab-order" @else class="btn btn-danger text-white tab-order" @endif onclick="confirmDelete('subresource-active-{{ $item->id }}','{{ $item->name }}','{{ $item->status }}');"> @if($item->status==0) <i class="fa fa-thumbs-o-up"></i> Active @else <i class="fa fa-thumbs-o-down"></i> Inactive @endif</button>

                                                                    <form id="subresource-active-{{ $item->id }}" action="{{url('admin/active/subresource/')}}/{{$item->id}}" method="get">

                                                                        {{ csrf_field() }}

                                                                    </form>

                                                                </li>

                                                            </ul>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="modal fade text-left show" id="editsubResourceModal{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" style="padding-right: 17px;">

                                        <div class="modal-dialog" role="document">

                                          <div class="modal-content">

                                            <div class="modal-header">

                                              <h3 class="modal-title" id="myModalLabel35"> Edit Sub Resource</h3>

                                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                                                <span aria-hidden="true">??</span>

                                              </button>

                                            </div>

                                            <form method="POST" action="{{url('admin/edit/subresource')}}/{{$item->id}}" enctype="multipart/form-data">

                                                @csrf

                                              <div class="modal-body">

                                                <fieldset class="form-group floating-label-form-group">

                                                    <label for="email" class="label-control required">Sub Resource Name</label>

                                                    <input type="text" class="form-control" id="subresourcename1" name="name" placeholder="Sub Resource Name" value="{{$item->heading}}">

                                                      @if ($errors->has('subresourcename'))

                                                          <span class="help-block">

                                                              <strong class="error">{{ $errors->first('subresourcename') }}</strong>

                                                          </span>

                                                      @endif

                                                </fieldset>

                                                <fieldset class="form-group floating-label-form-group">

                                                  <label for="email" class="label-control">Sub Resource Detail</label>

                                                  <textarea cols="30" rows="15" class="form-control detail2" id="detail2{{$item->id}}" name="detail2" placeholder="Details about subresource">{!! $item->details !!}</textarea>

                                                    @if ($errors->has('detail2'))

                                                        <span class="help-block">

                                                            <strong class="error">{{ $errors->first('detail2') }}</strong>

                                                        </span>

                                                    @endif

                                                </fieldset>

                                                <fieldset class="form-group floating-label-form-group">

                                                  <label for="email" class="label-control">Upload Files</label>

                                                  <input type="file" class="form-control" id="file" name="file[]" placeholder="Subresource files" accept="application/pdf,application/msword,

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
                                                                    <a  class="text-info" href="{{url('admin/downloadfile')}}/{{$file->file}}" title="File{{$index+1}}" download>File{{$index+1}} <i class="fa fa-file-{{$icons[$type]}}-o fa-1x text-center"/></i>&nbsp;&nbsp;</a> 
                                                                </li>
                                                            @empty 
                                                            @endforelse
                                                        @endif

                                                  </ul>

                                                    @if ($errors->has('fie'))

                                                        <span class="help-block">

                                                            <strong class="error">{{ $errors->first('file') }}</strong>

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

                                    

                                </div>

                                <div class="pull-right">

                                    {!! $list->render() !!}

                                </div>

                            </section>

                        </div>

                    </div>

                    <div class="modal fade text-left show" id="createSubResourceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel35" style="padding-right: 17px;">

                        <div class="modal-dialog" role="document">

                          <div class="modal-content">

                            <div class="modal-header">

                              <h3 class="modal-title" id="myModalLabel35"> Create Sub Resource</h3>

                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                                <span aria-hidden="true">??</span>

                              </button>

                            </div>

                            <form method="POST" action="{{url('admin/add/subresource')}}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="resource_id" value="{{$resource}}">
                              <div class="modal-body">
                                  <fieldset class="form-group floating-label-form-group">
                                      <label for="email" class="label-control required">Sub Resource Name</label>
                                      <input type="text" class="form-control" id="subresourcename" name="name" placeholder="Sub Resource Name">
                                        @if ($errors->has('subresourcename'))
                                            <span class="help-block">
                                                <strong class="error">{{ $errors->first('subresourcename') }}</strong>
                                            </span>
                                        @endif
                                  </fieldset>
                                  <fieldset class="form-group floating-label-form-group">
                                    <label for="email" class="label-control">Sub Resource Detail</label>
                                    <textarea cols="30" rows="15" class="form-control" id="detail1" name="detail1" placeholder="Details about subresource"></textarea>
                                      @if ($errors->has('detail1'))
                                          <span class="help-block">
                                              <strong class="error">{{ $errors->first('detail1') }}</strong>
                                          </span>
                                      @endif
                                  </fieldset>
                                  <fieldset class="form-group floating-label-form-group" id="upload-form">
                                    <label for="email" class="label-control">Upload Files</label>
                                    <input type="file" class="form-control uploadFile11" id="uploadFile11" name="file[]" placeholder="Subresource files" accept="application/pdf,application/msword,
                                    application/vnd.openxmlformats-officedocument.wordprocessingml.document" multiple>
                                      @if ($errors->has('fie'))
                                          <span class="help-block">
                                              <strong class="error">{{ $errors->first('file') }}</strong>
                                          </span>
                                      @endif
                                </fieldset>
                                <div id="image_preview11"></div>
                                <fieldset class="form-group floating-label-form-group" id="FileName11">
                                    

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

    CKEDITOR.replace( 'detail1' ,{
        filebrowserUploadUrl: "{{route('ckeditor.image-upload', ['_token' => csrf_token() ])}}",
        filebrowserUploadMethod: 'form',
    });

    // CKEDITOR.replace( 'detail2' );

    $('.detail2').each(function () {

        CKEDITOR.replace($(this).prop('id'),{
        filebrowserUploadUrl: "{{route('ckeditor.image-upload', ['_token' => csrf_token() ])}}",
        filebrowserUploadMethod: 'form',
    });

    });

    $('.select2-multi').select2();
    $(".uploadFile11").change(function(){
        
        var $this = $(this);
        $this.hide();
        var total_file=document.getElementById("uploadFile11").files.length;
        for(var i=0;i<total_file;i++)

        {
            var fileName = this.files[i].name;
            var divId = Math.floor((Math.random() * 100) + 1);
            $('#image_preview11').append("<ul class=\"list-inline mb-0 \"><li class=\"pip\">" +
                "<span id='"+divId+"' class=\"remove11\"></span>"+
                "<lable class=\"label-control\">" + fileName +"<lable>"+
                "</li></ul>");
            var selectbox = '<div class='+divId+'><label class="label-control required">File Name for ' + fileName + '</label> <br><input type="text" class="form-control" id="subresourcefilename" name="filenamearray11[]" placeholder="File Name" required><br>';
                $('#FileName11').append(selectbox);

        }

        $("#upload-form").append("<input type='file' class='file-input uploadMoreFile' id='uploadMoreFile' name='file[]' multiple='multiple'>");

        $(".remove11").click(function(){
            file = $(this).attr('id');
            $(this).parent(".pip").remove();
            $("."+file).remove();
            document.getElementById("uploadFile11").value=null;
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
            $('#image_preview11').append("<ul class=\"list-inline mb-0 \"><li class=\"pip\">" +
                "<span id='"+divId+"' class=\"remove\"></span>"+
                "<lable class=\"label-control\">" + fileName +"<lable>"+
                "</li></ul>");
            var selectbox = '<div class='+divId+'><label class="label-control required">File Name for ' + fileName + '</label> <br><input type="text" class="form-control" id="subresourcefilename" name="filenamearray11[]" placeholder="File Name" required><br>';
                $('#FileName11').append(selectbox);

        }
        $(".remove").click(function(){
        file = $(this).attr('id');
        $(this).parent(".pip").remove();
        $("."+file).remove();
        });
        $("#upload-form").append("<input type='file' class='form-control uploadMoreFile' id='uploadMoreFile' name='file[]' accept='application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document' multiple='multiple'>");
   
    });

    $(".remove1").click(function(){

        file = $(this).attr('id');

        Swal.fire({

        title: 'Are you sure ?',

        text: 'This task cannot be revert',

        showCancelButton: true,

        confirmButtonText: `Ok Delete`,

        cancelButtonText: `Cancel`,

        }).then((result) => {

            if (!result.isConfirmed) return;

                $.ajax({

                    url:"#",

                    method:"get",

                    data: {

                        id: file

                    },

                    success: function (data) {

                    if(data=="deleted"){

                        Swal.fire({

                        title: "Done!", 

                        text: "It was succesfully deleted!",

                        type: "success"

                        }).then((result) => {

                        // Reload the Page

                        location.reload();

                        });

                    }

                    },

                    error: function (xhr, ajaxOptions, thrownError) {

                        Swal.fire({

                        title: "Error deleting!", 

                        text: "Please try again",

                        type: "error"

                        }).then((result) => {

                        // Reload the Page

                        location.reload();

                        });

                    }

                });

        })

    });

    function confirmDelete(id,name,status) {

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

    $(document).ready(function() {

        $.noConflict();

        $('#DataTables').DataTable({
            "scrollY":'50vh',
            "scrollX": false,
            "paging":false,
            "searching": false,
            "info": false,
            "ordering": false
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