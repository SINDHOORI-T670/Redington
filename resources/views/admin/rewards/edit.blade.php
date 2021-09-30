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
                                <h4 class="card-title" id="horz-layout-card-center">Edit Reward</h4>
                                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                            </div><br><br><br>
                            <div class="card-content collpase show">
                                <div class="card-body">
                                    <form class="form form-horizontal" action="{{url('admin/update/reward')}}/{{$reward->id}}" method="post" enctype="multipart/form-data"> 
                                        @csrf
                                        <div class="form-body">
                                            <div class="form-group row">
                                                <label class="col-md-3 label-control required" for="eventRegInput1">Title</label>
                                                <div class="col-md-9">
                                                    <input type="text" id="eventRegInput1" class="form-control" placeholder="Reward Title" name="title" value="{{$reward->heading}}">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label class="col-md-3 label-control" for="eventRegInput1">Cupon</label>
                                                <div class="col-md-9">
                                                    <input type="file" class="form-control" id="file" name="image" placeholder="files">
                                                    @if(isset($reward->image))<a href="{{$reward->image}}" target="_blank">view cupon</a>@endif
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-md-3 label-control required" for="eventRegInput4">Point</label>
                                                <div class="col-md-9">
                                                    <input type="text" id="eventRegInput4" class="form-control" placeholder="Reward Point" name="point" value="{{$reward->point}}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-actions center">
                                            <a href="{{url('admin/list/rewards')}}" class="btn btn-warning mr-1">
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js" integrity="sha512-UdIMMlVx0HEynClOIFSyOrPggomfhBKJE28LKl8yR3ghkgugPnG6iLfRfHwushZl1MOPSY6TsuBDGPK2X4zYKg==" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/bootstrap.tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.1.62/jquery.inputmask.bundle.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>

<script type="text/javascript">
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
            $('#profile').attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }


    
</script>
@endsection