@extends($activeTemplate.'layouts.auth')

@section('content')
    <!-- register-section start -->
    <section class="register-section" style="background-image: url('{{asset($activeTemplateTrue.'images/bg.jpg')}}')">
        <div class="container">
            <div class="">
                <div class="">
                    
                            <div class="sign-card">
                               
                                <form class="create-account-form register-form" method="POST" action="{{ route('user.login')}}"
                                      onsubmit="return submitUserForm();">
                                    @csrf
                                   
                                    <div class="row ">
                                   
                                        <div class="col-6 right-form">
                                        <h4 class="title mt-4 mr-2 m-2">@lang('Login your account')</h4>
                                        <div class="  mr-2 m-2">
                                            <input type="text" name="username" value="{{ old('username') }}" placeholder="@lang('Username or Email')" required>
                                        </div>
                                        <div class="mr-2 m-2">
                                            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="@lang('Password')">
                                        </div>

                                        <div class="mr-2 m-2">
                                            @php echo loadReCaptcha() @endphp
                                        </div>
                                        @include($activeTemplate.'partials.custom-captcha')
                                            <div class="d-flex align-items-center text-white flex-wrap mr-2 m-2 rem">
                                                <div class="checkbox-item d-flex ">
                                                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                    <label for="remember">@lang('Remember Me')</label>
                                                </div>

                                                <a class="text-anim" href="{{route('user.password.request')}}">@lang('Forgot Password?')</a>
                                            </div>

                                        <div class="mr-2 m-2">
                                            <button type="submit" class="">@lang('Signin Now')</button>
                                        </div>
                                        <div class="mr-2 m-2">
                                <h4 class="title text-anim">
                                    @lang('New here?')
                                </h4>
                                <a  class="text-anim" href="{{ route('user.register') }}" class="">@lang('Create Account')</a>
                            </div>
                                        </div>
                                        <div class="col-6 left-wall">
                                        <img src="{{asset($activeTemplateTrue.'images/loginbg.png')}}" alt="">
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                       
                        <div class="col-lg-3 p-0"></div>
                        {{--<div class="col-lg-6 p-0">--}}
                            {{--<div class="change-catagory-area">--}}
                                {{--<h4 class="title">--}}
                                    {{--@lang('New here?')--}}
                                {{--</h4>--}}
                                {{--<a href="{{ route('user.register') }}" class="cmn-btn-active account-control-button">@lang('Create Account')</a>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                </div>
            </div>
        </div>
    </section>
    <!-- register-section end -->
@endsection

@push('script')
    <script>
        "use strict";
        function submitUserForm() {
            var response = grecaptcha.getResponse();
            if (response.length == 0) {
                document.getElementById('g-recaptcha-error').innerHTML = '<span style="color:red;">@lang("Captcha field is required.")</span>';
                return false;
            }
            return true;
        }
        function verifyCaptcha() {
            document.getElementById('g-recaptcha-error').innerHTML = '';
        }
    </script>
@endpush
