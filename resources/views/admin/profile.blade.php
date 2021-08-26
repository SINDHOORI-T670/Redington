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
                                <h4 class="card-title" id="horz-layout-card-center">EDIT ADMIN PROFILE</h4>
                                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                        <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                        <li><a data-action="close"><i class="ft-x"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collpase show">
                                <div class="card-body">
                                    <form class="form form-horizontal" enctype="multipart/form-data" action="{{url('admin/update/profile')}}" method="post"> 
                                        @csrf
                                        {{-- {{ method_field('PUT') }} --}}
                                        <div class="form-body">
                                            <div class="form-group row">
                                                <label class="col-md-3 label-control" for="eventRegInput1">Name</label>
                                                <div class="col-md-9">
                                                    <input type="text" id="eventRegInput1" class="form-control" placeholder="name" name="name" value={{Auth::User()->name}}>
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
                                                    <input type="tel" id="eventRegInput5" class="form-control" name="phone" placeholder="contact number" value="{{Auth::User()->phone}}">
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
@endsection