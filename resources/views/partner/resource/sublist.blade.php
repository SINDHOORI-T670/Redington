@extends('layouts.partnerlay')
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
    
    .remove11{
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
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><a href="{{url('partner/resource/list')}}"><i class="fa fa-arrow-left"></i></a> &nbsp;&nbsp; Sub Resources</h4>
                        <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
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
                                                                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#editsubResourceModal{{$item->id}}">
                                                                        View
                                                                    </button>
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

                                                    <h3 class="modal-title" id="myModalLabel35"> Create Sub Resource</h3>

                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                                                        <span aria-hidden="true">×</span>

                                                    </button>

                                                </div>
                                                <div class="modal-body">
                                                    <fieldset class="form-group floating-label-form-group">
                                                        <label for="email" class="label-control required">Sub Resource Name</label>
                                                        <input type="text" class="form-control" id="subresourcename" name="name" placeholder="Sub Resource Name" value="{{$item->heading}}">
                                                            @if ($errors->has('subresourcename'))
                                                                <span class="help-block">
                                                                    <strong class="error">{{ $errors->first('subresourcename') }}</strong>
                                                                </span>
                                                            @endif
                                                    </fieldset>
                                                    <fieldset class="form-group floating-label-form-group">
                                                        <label for="email" class="label-control">Sub Resource Detail</label>
                                                        <textarea cols="30" rows="15" class="form-control detail2" id="detail1_{{$item->id}}" name="detail1" placeholder="Details about subresource">{!! $item->details !!}</textarea>
                                                        @if ($errors->has('detail1'))
                                                            <span class="help-block">
                                                                <strong class="error">{{ $errors->first('detail1') }}</strong>
                                                            </span>
                                                        @endif
                                                    </fieldset>
                                                    <fieldset class="form-group floating-label-form-group" id="upload-form">
                                                        <label for="email" class="label-control">Files</label>
                                                        <ul class="list-inline mb-0">  
                                                            @if(isset($item->subfiles))
                                                                {{-- @forelse(explode(',',$item->file) as $index => $file)--}}
                                                                    @forelse($item->subfiles as $index => $file)
                                                                    @php 
                                                                        $type = pathinfo($file->file, PATHINFO_EXTENSION);
                                                                        $split[] = explode('.',$file->file);
                                                                    @endphp 
                                                                    <li>
                                                                        <a  class="text-info" href="#" title="File{{$index+1}}">{{$file->filename}} &nbsp;</a> 
                                                                    </li>
                                                                @empty 
                                                                @endforelse
                                                            @endif
                                                        </ul>
                                                    </fieldset>
                                                    <div id="image_preview11"></div>
                                                    <fieldset class="form-group floating-label-form-group" id="FileName11">
                                                        

                                                    </fieldset>
                                                    
                                                </div>
                                                <div class="modal-footer">
                                                    <input type="reset" class="btn btn-outline-secondary btn-lg" data-dismiss="modal" value="close">
                                                </div>

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

    // CKEDITOR.replace( 'detail2' );
    $('.detail2').each(function () {
        CKEDITOR.replace($(this).prop('id'));
    });
    $('.select2-multi').select2();
    


    
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