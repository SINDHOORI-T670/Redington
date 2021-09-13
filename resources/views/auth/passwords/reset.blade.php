<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
  
<!-- Mirrored from pixinvent.com/bootstrap-admin-template/robust/html/ltr/vertical-menu-template/register-simple.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 23 Aug 2021 07:51:24 GMT -->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Robust admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template.">
    <meta name="keywords" content="admin template, robust admin template, dashboard template, flat admin template, responsive admin template, web app, crypto dashboard, bitcoin dashboard">
    <meta name="author" content="PIXINVENT">
    <title>{{ config('app.name', '') }} - Reset Password </title>
    <link rel="apple-touch-icon" href="{{asset('admin/app-assets/images/ico/apple-icon-120.png')}}">
    <link rel="shortcut icon" type="image/x-icon" href="https://pixinvent.com/bootstrap-admin-template/robust/app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i%7CMuli:300,400,500,700" rel="stylesheet">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css/vendors.min.css')}}">
    <!-- END VENDOR CSS-->
    <!-- BEGIN ROBUST CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css/app.min.css')}}">
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css/core/menu/menu-types/vertical-menu.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css/core/colors/palette-gradient.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css/pages/login-register.min.css')}}">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('admin/assets/css/style.css')}}">
    <!-- END Custom CSS-->
  </head>
  <body class="vertical-layout vertical-menu 1-column   menu-expanded blank-page blank-page" data-open="click" data-menu="vertical-menu" data-col="1-column">
    <!-- ////////////////////////////////////////////////////////////////////////////-->
    <div class="app-content content">
      <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body"><section class="flexbox-container">
    <div class="col-12 d-flex align-items-center justify-content-center">
        <div class="col-md-4 col-10 box-shadow-2 p-0">
			<div class="card border-grey border-lighten-3 px-2 py-2 m-0">
				<div class="card-header border-0">
					<div class="card-title text-center">
						<img src="{{asset('admin/app-assets/images/logo/logo-dark.png')}}" alt="branding logo">
					</div>
					<h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2"><span>{{ __('Reset Password') }}</span></h6>
				</div>
				<div class="card-content">	
					<div class="card-body">
						<form class="form-horizontal form-simple" method="POST" action="{{ route('password.update') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">
							
							<fieldset class="form-group position-relative has-icon-left mb-1">
								<input type="email" class="form-control form-control-lg input-lg @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus  id="email" type="email" placeholder="Your Email Address">
								<div class="form-control-position">
								    <i class="ft-mail"></i>
								</div>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
							</fieldset>
							<fieldset class="form-group position-relative has-icon-left">
								<input type="password" id="password" class="form-control form-control-lg input-lg @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Enter Password" required>
								<div class="form-control-position">
								    <i class="fa fa-key"></i>
								</div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
							</fieldset>

                            <fieldset class="form-group position-relative has-icon-left">
								<input type="password" id="password-confirm" class="form-control form-control-lg input-lg @error('password') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password">
								<div class="form-control-position">
								    <i class="fa fa-key"></i>
								</div>
							</fieldset>
							<button type="submit" class="btn btn-info btn-lg btn-block"><i class="ft-unlock"></i> {{ __('Reset Password') }}</button>
						</form>
					</div>
					<p class="text-center">Already have an account ? <a href="{{url('admin/login')}}" class="card-link">Login</a></p>
				</div>
			</div>
		</div>
	</div>
</section>
        </div>
      </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->

    <!-- BEGIN VENDOR JS-->
    <script src="{{asset('admin/app-assets/vendors/js/vendors.min.js')}}"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
    <script src="{{asset('admin/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"></script>
    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN ROBUST JS-->
    <script src="{{asset('admin/app-assets/js/core/app-menu.min.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/core/app.min.js')}}"></script>
    <!-- END ROBUST JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    <script src="{{asset('admin/app-assets/js/scripts/forms/form-login-register.min.js')}}"></script>
    <!-- END PAGE LEVEL JS-->
  </body>

<!-- Mirrored from pixinvent.com/bootstrap-admin-template/robust/html/ltr/vertical-menu-template/register-simple.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 23 Aug 2021 07:51:24 GMT -->
</html>