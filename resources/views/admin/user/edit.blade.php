@extends('layouts.adminlay')
@section('content')
<link rel="stylesheet" href="http://bootstrap-tagsinput.github.io/bootstrap-tagsinput/dist/bootstrap-tagsinput.css">
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
</style>
<div class="app-content content">
    <div class="content-wrapper">
        <br>
        @include('alert.messages')
        <div class="content-body"><!-- Basic form layout section start -->
            <section id="horizontal-form-layouts">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                @php 
                                    $usertype = (
                                        ($user->type == 2) ? "Customer" :
                                        (($user->type == 3) ? "Partner" :
                                        (($user->type == 4) ? "Employee" : "No List"))
                                        );
                                @endphp
                                <h4 class="card-title" id="horz-layout-basic">Edit {{$usertype}} Info</h4>
                                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li>
                                            <div class="user-profile-images">
                                                <img src="{{asset('admin/app-assets/images/gallery/noimage.jpg')}}" id="profile" class="user-profile-image rounded" alt="user profile image" height="100" width="132">
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collpase show">
                                <div class="card-body">
                                    <form class="form form-horizontal" enctype="multipart/form-data" action="{{url('admin/update/user')}}" method="post"> 
                                        @csrf
                                        <input type="hidden" name="id" value="{{$user->id}}">
                                        <div class="form-body">
                                            <h4 class="form-section"><i class="ft-user"></i> Personal Info</h4>
                                            
                                            <div class="form-group row">
                                                <label class="col-md-3 label-control required" for="projectinput1">Name</label>
                                                <div class="col-md-9">
                                                    <input type="text" id="name" class="form-control" placeholder="Name" name="name" value="{{ $user->name }}" autofocus>
                                                    @if ($errors->has('name'))
                                                        <span class="help-block">
                                                            <strong class="error">{{ $errors->first('name') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                            </div>
        
                                            <div class="form-group row">
                                                <label class="col-md-3 label-control required" for="projectinput3">E-mail</label>
                                                <div class="col-md-9">
                                                    <input type="email" id="email" class="form-control" placeholder="E-mail" name="email" value="{{ $user->email }}">
                                                    @if ($errors->has('email'))
                                                        <span class="help-block">
                                                            <strong class="error">{{ $errors->first('email') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 label-control required" for="projectinput4">Job Position</label>
                                                <div class="col-md-9">
                                                    <input type="text" id="post" class="form-control" placeholder="Job Position" name="post" value="{{ old('post') }}">
                                                    @if ($errors->has('post'))
                                                        <span class="help-block">
                                                            <strong class="error">{{ $errors->first('post') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 label-control required" for="projectinput4">Contact Number</label>
                                                <div class="col-md-9">
                                                    <input type="text" id="phone" class="form-control" placeholder="Contact Number" name="phone" value="{{ $user->phone }}">
                                                    @if ($errors->has('phone'))
                                                        <span class="help-block">
                                                            <strong class="error">{{ $errors->first('phone') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- <div class="form-group row">
                                                <label class="col-md-3 label-control required" for="projectinput5">Password</label>
                                                <div class="col-md-9">
                                                    <input type="password" id="password" class="form-control" placeholder="Password" name="password">
                                                </div>
                                                @if ($errors->has('password'))
                                                    <span class="help-block">
                                                        <strong class="error">{{ $errors->first('password') }}</strong>
                                                    </span>
                                                @endif
                                            </div> --}}

                                            {{-- <div class="form-group row">
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
                                            </div> --}}
                                                
                                            <h4 class="form-section"><i class="ft-clipboard"></i>  Services</h4>
            
            
                                            <div class="form-group row">
                                                <label class="col-md-3 label-control" for="projectinput8">Tags</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="serv_tags[]" value="{{ old('tags') }}" data-role="tagsinput" class="form-control" />
                                                </div>
                                            </div>
                                            <h4 class="form-section"><i class="ft-clipboard"></i> Technologies</h4>
                                            
                                            <div class="form-group row">
                                                <label class="col-md-3 label-control" for="projectinput8">Tags</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="tec_tags[]" value="{{ old('tags') }}" data-role="tagsinput" class="form-control" />
                                                </div>
                                            </div>
                                        </div>
            
                                        <div class="form-actions right">
                                            <a href="{{url('admin/list/user')}}/{{$user->type}}" class="btn btn-warning mr-1">
                                                <i class="ft-x"></i> Cancel
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ft-check"></i> Save
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
    var phones = [{ "mask": "+(###) ########"},{ "mask": "+(###) ########"}];
            $('#phone').inputmask({ 
                mask: phones, 
                greedy: false, 
                definitions: { '#': { validator: "[0-9]", cardinality: 1}} ,
                
            });
    console.clear();


    
</script>
@endsection