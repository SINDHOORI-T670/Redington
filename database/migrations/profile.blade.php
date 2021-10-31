@extends('layouts.customerlay')

@section('content')
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
        #scrollingDiv{
            height:400px;overflow-y:auto
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

                                <h4 class="card-title" id="horz-layout-card-center">EDIT CUSTOMER PROFILE</h4>

                                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>

                                <div class="heading-elements">

                                    <ul class="list-inline mb-0">

                                        <li>

                                            <div class="user-profile-images">

                                                <img src="@if(Auth::User()->image!=""){{url(Auth::User()->image)}} @else {{asset('admin/app-assets/images/gallery/noimage.jpg')}}@endif"  id="profile" class="user-profile-image rounded" alt="user profile image" height="100" width="132">

                                            </div>

                                        </li>

                                    </ul>

                                </div>

                                

                            </div><br><br><br>

                            <div class="card-content collpase show" id="scrollingDiv">

                                <div class="card-body">

                                    <form class="form form-horizontal" enctype="multipart/form-data" action="{{url('customer/update/profile')}}" method="post"> 

                                        @csrf

                                        {{-- {{ method_field('PUT') }} --}}

                                        <div class="form-body">

                                            <div class="form-group row">

                                                <label class="col-md-3 label-control" for="eventRegInput1">Name</label>

                                                <div class="col-md-9">

                                                    <input type="text" id="eventRegInput1" class="form-control" placeholder="name" name="name" value="{{Auth::User()->name}}">

                                                </div>

                                            </div>



                                            <div class="form-group row">

                                                <label class="col-md-3 label-control" for="eventRegInput4">Email</label>

                                                <div class="col-md-9">

                                                    <input type="email" id="eventRegInput4" class="form-control" placeholder="email" name="email" value="{{Auth::User()->email}}">

                                                </div>

                                            </div>



                                            <div class="form-group row">

                                                <label class="col-md-3 label-control" for="eventRegInput5">Contact Number</label>

                                                <div class="col-md-9">

                                                    <input type="text" id="phone" class="form-control" name="phone" placeholder="contact number" value="{{Auth::User()->phone}}">

                                                </div>

                                            </div>



                                            <div class="form-group row">

                                                <label class="col-md-3 label-control" for="password">Password</label>

                                                <div class="col-md-9">

                                                    <input type="password" id="password" class="form-control" name="password"  placeholder="Enter Password">

                                                </div>

                                            </div>



                                            <div class="form-group row">

                                                <label class="col-md-3 label-control required">Profile Image</label>

                                                <div class="col-md-9">

                                                    <label id="projectinput6" class="file center-block">

                                                        <input type="file"  name="image" id="image" accept=".jpg,.png,.jpeg" onchange="readURL(this);">

                                                        <span class="file-custom"></span>

                                                    </label>

                                                </div>

                                                @if ($errors->has('image'))

                                                    <span class="help-block">

                                                        <strong class="error">{{ $errors->first('image') }}</strong>

                                                    </span>

                                                @endif

                                            </div>

                                            <div class="form-group row">

                                                <label class="col-md-3 label-control">Services</label>

                                                <div class="col-md-9">

                                                    {{-- <label id="projectinput6" class="file center-block"> --}}

                                                        <select class="form-control select2-multi" name="services[]" multiple="multiple">

                                                            @forelse ($services as $item)
    
                                                                
    
                                                                <option value={{$item->id}} @if(isset($userspecs->service_id))@foreach(explode(",",$userspecs->service_id) as $spec) @if($spec==$item->id) selected @else @endif @endforeach @endif>{{$item->name}}</option>
    
                                                            @empty
    
                                                            @endforelse
    
                                                        </select>

                                                    {{-- </label> --}}

                                                </div>

                                            </div>

                                            <div class="form-group row">

                                                <label class="col-md-3 label-control">Technologies</label>

                                                <div class="col-md-9">

                                                    {{-- <label id="projectinput6" class="file center-block"> --}}

                                                        <select class="form-control select2-multi" name="technologies[]" multiple="multiple">

                                                            @forelse ($technologies as $item)
    
                                                                
    
                                                                <option value={{$item->id}} @if(isset($userspecs->technology_id))@foreach(explode(",",$userspecs->technology_id) as $spec) @if($spec==$item->id) selected @else @endif @endforeach @endif>{{$item->name}}</option>
    
                                                            @empty
    
                                                            @endforelse
    
                                                        </select>

                                                    {{-- </label> --}}

                                                </div>

                                            </div>

                                        </div>



                                        <div class="form-actions center">

                                            <button type="button" class="btn btn-warning mr-1">

                                                <i class="ft-x"></i> Cancel

                                            </button>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js"></script>

<script type="text/javascript">

    $('.select2-multi').select2();
    function readURL(input) {

        if (input.files && input.files[0]) {

            var reader = new FileReader();



            reader.onload = function(e) {

            $('#profile').attr('src', e.target.result);

            };



            reader.readAsDataURL(input.files[0]);

        }

    }

    var phones = [{ "mask": "+(###) #######"},{ "mask": "+(###) #######"}];

            $('#phone').inputmask({ 

                mask: phones, 

                greedy: false, 

                definitions: { '#': { validator: "[0-9]", cardinality: 1}} ,

                

            });

    console.clear();





    

</script>

@endsection