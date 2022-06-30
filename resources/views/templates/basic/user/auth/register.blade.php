@extends($activeTemplate.'layouts.auth')
@section('content')
    <!-- register-section start -->
    <section class="register-section" style="background-image: url('{{asset($activeTemplateTrue.'images/bg.jpg')}}')">
        <div class="container">
            <div class="">
                <div class="">
                    
                            <div class="sign-card signup-card">
                               
                            <form class="create-account-form register-form" action="{{ route('user.register') }}"
                                      method="POST" onsubmit="return submitUserForm();">
                                    @csrf
                                    @if(session()->get('reference') != null)
                                            <div class="col-lg-6 form-group">
                                                <label for="firstname"
                                                       class="col-md-4 col-form-label text-md-right">@lang('Reference By')</label>
                                                <input type="text" name="referBy" id="referenceBy" class="form-control"
                                                       value="{{session()->get('reference')}}" readonly>
                                            </div>
                                        @endif
                                    <div class="row ">
                                   
                                    <div class="col-6 right-form">
                                        <h4 class="title mt-4 mr-2 m-2">@lang('Create your account')</h4>
                                        <div class=" cont-50 mr-2 m-2">
                                        <input id="firstname" type="text" name="firstname"
                                                   value="{{ old('firstname') }}" placeholder="@lang('First Name')"
                                                   required>
                                        </div>
                                        <div class="cont-50 mr-2 m-2">
                                        <input id="lastname" type="text" name="lastname"
                                                   value="{{ old('lastname') }}" placeholder="@lang('Last Name')"
                                                   required>
                                        </div>
                                        <div class="cont-50 mr-2 m-2">
                                        <input id="username" type="text" name="username"
                                                   value="{{ old('username') }}" placeholder="@lang('Username')"
                                                   required>
                                        </div>
                                         <!--   <div class="  mr-2 m-2">
                                            <input type="text" id="country" name="country" value="Syria" hidden>
                                        </div> -->
                                        <div class="cont-50 mr-2 m-2">
                                        <input id="email" type="email" placeholder="@lang('Email')" name="email" value="{{ old('email') }}"
                                                   required>
                                        </div>
                                        <div class="cont-100  mr-2 m-2 d-flex align-items-center">
                                      
                                                    <div class="">
                                                            <input type="hidden" name="country_code" value="963">
                                                            <select  name="country">
                                                                {{--<option  value="963" data-country="Syrian Arab Republic" data-code="SY"> +963</option>--}}
                                                                @include('partials.country_code')
                                                            </select>
                                                    </div>
                                                    <input type="text" name="mobile" placeholder="@lang('Your Phone Number')" class="w-auto flex-fill" value="{{ old('mobile') }}">
                                                
                                        </div>
                                        <div class="cont-50 mr-2 m-2">
                                        <input id="password" type="password" name="password" required  placeholder="@lang('Password')">
                                            @if($general->secure_password)
                                                <div class="input-popup">
                                                    <p class="error lower">@lang('1 small letter minimum')</p>
                                                    <p class="error capital">@lang('1 capital letter minimum')</p>
                                                    <p class="error number">@lang('1 number minimum')</p>
                                                    <p class="error special">@lang('1 special character minimum')</p>
                                                    <p class="error minimum">@lang('6 character password')</p>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="cont-50 mr-2 m-2">
                                        <input id="password-confirm" type="password" name="password_confirmation" placeholder="@lang('Confirm Password')" required autocomplete="new-password">
                                        </div>

                                        <div class="mr-2 m-2">
                                        @php echo loadReCaptcha() @endphp
                                        </div>
                                        @include($activeTemplate.'partials.custom-captcha')
                                           

                                        <div class="mr-2 m-2 wid-100">
                                        <button type="submit">@lang('Signup Now')</button>
                                        </div>
                                        <div class="mr-2 m-2 wid-100">
                                <h4 class="text-white text-anim">
                                @lang('Already have an account?')
                                </h4>
                                <a href="{{ route('user.login') }}"
                                 class="text-white text-anim">@lang("Sign In Here")
                                </a>
                            </div>
                                        </div>
                                        <div class="col-6 left-wall">
                                        <img src="{{asset($activeTemplateTrue.'images/logincard.png')}}" alt="">
                                        </div>
                                    </div>
                                </form>
                            </div>
                </div>
            </div>
        </div>
    </section>
    <!-- register-section end -->
@endsection


@push('script-lib')
    <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
@endpush

@push('script')
    <script>
        "use strict";
        @if($country_code)
        $(`option[data-code={{ $country_code }}]`).attr('selected', '');
        @endif
        $('select[name=country_code]').change(function () {
            $('input[name=country]').val($('select[name=country_code] :selected').data('country'));
        }).change();

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

        @if($general->secure_password)
        $('input[name=password]').on('input', function () {
            secure_password($(this));
        });
        @endif
    </script>
@endpush
