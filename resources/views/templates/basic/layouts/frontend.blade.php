<!doctype html>
<html lang="en" itemscope itemtype="http://schema.org/WebPage">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @include('partials.seo')


    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- fontawesome css link -->
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/line-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/fontawesome-all.min.css')}}">

    <!-- flaticon css -->
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'font/flaticon.css')}}">
    <!-- magnific popup -->
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/magnific-popup.css')}}">
    <!-- nice-select css -->
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/nice-select.css')}}">
    <!-- bootstrap css link -->
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/bootstrap.min.css')}}">
    <!-- swipper css link -->
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/swiper.min.css')}}">
    <!-- odometer css -->
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/odometer.css')}}">
    <!-- icon css -->
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/themify.css')}}">
    <!-- animate.css -->
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/animate.css')}}">
    <!--headline.css -->
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/jquery.animatedheadline.css')}}">
    <!-- main style css link -->
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'css/style.css')}}">
    <link rel="stylesheet" href="{{asset($activeTemplateTrue.'/css/bootstrap-fileinput.css')}}">

    <link rel="stylesheet"
          href="{{asset($activeTemplateTrue.'css/color.php?color='.$general->base_color.'&secondColor='.$general->secondary_color)}}">

    @stack('style-lib')
    @stack('style')

</head>
<body>

{{--@php echo loadFbComment() @endphp--}}

<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        Start Preloader
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<div id="overlayer">
    <div class="loader">
        <div class="loader-inner"></div>
    </div>
</div>

<!-- header-section start -->
<header class="header-section">
    <div class="header">
        <div class="header-bottom-area">
            <div class="container">
                <div class="header-menu-content">
                    <nav class="navbar navbar-expand-lg p-0" >
                        <a class="site-logo site-title" href="{{route('home')}}"><img src="{{ getImage(imagePath()['logoIcon']['path'] .'/logo.png') }}" height="34px" alt="site-logo"></a>
                        <button class="navbar-toggler ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="fas fa-bars"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav main-menu ml-auto">
                                @auth
                                    <li><a href="{{ route('user.home') }}">@lang('Dashboard')</a></li>
                                    <li><a href="{{ route('user.logout') }}">@lang('Logout')</a></li>
                                @else
                                    <li><a href="{{ route('user.login') }}">@lang('Login')</a></li>
                                    <li><a href="{{ route('user.register') }}">@lang('Register')</a></li>
                                @endauth
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- header-section end -->

<a href="#" class="scrollToTop"><i class="fa fa-angle-up"></i></a>

<!--breadcrumb area-->
{{--@if(request()->route()->getName() != 'home')--}}
    {{--@include($activeTemplate.'partials.breadcrumb')--}}
{{--@endif--}}
<!--/breadcrumb area-->


@yield('content')

@php
    $footer_content = getContent('footer.content', true);
    $footer_elements = getContent('footer.element');
    $address_content = getContent('address.content', true);
    $extra_pages = getContent('extra.element');
@endphp
{{--<!-- footer-section start -->--}}
{{--<footer class="footer-section ptb-80">--}}
    {{--<div class="container">--}}
        {{--<div class="footer-area mrt-100">--}}
            {{--<div class="row ml-b-30">--}}
                {{--<div class="col-lg-4 col-sm-8 mrb-30">--}}
                    {{--<div class="footer-widget widget-menu">--}}
                        {{--<div class="footer-logo">--}}
                            {{--<h3 class="widget-title">@lang('About Us')</h3>--}}
                            {{--<p>{{ __(@$footer_content->data_values->content) }}</p>--}}
                            {{--<div class="social-area">--}}
                                {{--<ul class="footer-social">--}}
                                    {{--@forelse($footer_elements as $item)--}}
                                        {{--<li><a href="{{ @$item->data_values->social_url }}">@php echo @$item->data_values->social_icon @endphp</a></li>--}}
                                    {{--@empty--}}
                                    {{--@endforelse--}}
                                {{--</ul>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="col-lg-2 col-sm-6 mrb-30">--}}
                    {{--<div class="footer-widget">--}}
                        {{--<h3 class="widget-title">@lang('Quick Link')</h3>--}}
                        {{--<ul>--}}
                            {{--@foreach($pages as $k => $data)--}}
                                {{--<li><a href="{{route('pages',[$data->slug])}}">{{__($data->name)}}</a></li>--}}
                            {{--@endforeach--}}

                            {{--<li><a href="{{ route('contact') }}">@lang('Contact')</a></li>--}}
                        {{--</ul>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="col-lg-3 col-sm-6 mrb-30">--}}
                    {{--<div class="footer-widget">--}}
                        {{--<h3 class="widget-title">@lang('Privacy and Terms')</h3>--}}
                        {{--<ul>--}}
                            {{--@forelse($extra_pages as $item)--}}
                                {{--<li><a href="{{ route('extra.details', [$item->id, slug($item->data_values->title)]) }}">{{ __(@$item->data_values->title) }}</a></li>--}}
                            {{--@empty--}}
                            {{--@endforelse--}}

                            {{--<li><a href="{{ route('api.documentation') }}">@lang('API Documentation')</a></li>--}}
                        {{--</ul>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="col-lg-3 col-sm-6 mrb-30">--}}
                    {{--<div class="footer-widget widget-menu">--}}
                        {{--<h3 class="widget-title">@lang('contact info')</h3>--}}
                        {{--<ul class="footer-contact-list">--}}
                            {{--<li>{{ __(@$address_content->data_values->address) }}</li>--}}
                            {{--<li>@lang('Call Us Now') {{ __(@$address_content->data_values->phone) }}</li>--}}
                            {{--<li>{{ __(@$address_content->data_values->email) }}</li>--}}
                        {{--</ul>--}}
                    {{--</div>--}}
                {{--</div>--}}

            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</footer>--}}
<section class="w3l-footers-20">
<div class="footers20">
		<div class="container">
			<div class="footers20-content">
				<div class="row grid-col-4 grids-content">
					<div class="column col-lg-3 col-md-6 col-sm-6">
						<h3><a class="footer-logo" style="color:#fff"
                        href="index.html"><span class="fa fa-gamepad" aria-hidden="true"></span>Sema.Store</a></h3>
						<p class="contact-para3 para">Adipisicing elit.exercit ationem accu samus lauda ntium porro sunt
							repudt ationem accusamus exercit ationem accus amus lauda ntium porro elit exerc sunt repud.
						</p>
					</div>
					<div class="column col-lg-3 col-md-6 col-sm-6">
						<h4>@lang('Latest News')</h4>
						<div class="img-text">
							<a href=""><img src="https://mrreiha.keybase.pub/codepen/hover-fx/news1.jpg" alt="product" class="img-responsive "></a>
							<div class="post-details"> <a href="">
									<p class="contact-mail para"> Black Hunt</p>
								</a>
								<p class="para">june 28</p>

							</div>
						</div>
						<div class="img-text mt-3">
							<a href=""><img src="https://mrreiha.keybase.pub/codepen/hover-fx/news1.jpg" alt="product" class="img-responsive "></a>
							<div class="post-details"> <a href="">
									<p class="contact-mail para">Blood Moon</p>
								</a>
								<p class="para">june 28</p>

							</div>
						</div>
					</div>
					<div class="column col-lg-3 col-md-6 col-sm-6">
						<h4>@lang('Categories')</h4>
						<div class="img-instagram">
							<div class="img-inst">
								<a href=""><img src="https://mrreiha.keybase.pub/codepen/hover-fx/news1.jpg" alt="product" class="img-responsive pb-1"> </a>
								<a href=""><img src="https://mrreiha.keybase.pub/codepen/hover-fx/news1.jpg" alt="product" class="img-responsive mt-2"></a>
							</div>
							<div class="img-inst">
								<a href=""><img src="https://mrreiha.keybase.pub/codepen/hover-fx/news1.jpg" alt="product" class="img-responsive pb-1"></a>
								<a href=""><img src="https://mrreiha.keybase.pub/codepen/hover-fx/news1.jpg" alt="product" class="img-responsive mt-2"> </a>
							</div>
							<div class="img-inst">
								<a href=""><img src="https://mrreiha.keybase.pub/codepen/hover-fx/news1.jpg" alt="product" class="img-responsive pb-1"></a>
								<a href=""><img src="https://mrreiha.keybase.pub/codepen/hover-fx/news1.jpg" alt="product" class="img-responsive mt-2 "></a>
							</div>
						</div>
					</div>
					<div class="column col-lg-3 col-md-6 col-sm-6">
						<h4>@lang('Newsletter')</h4>
						<p class="contact-para3 para">
                            @lang('Adipisicing elit exerc tationem accusamus exercit ationem accus
							amus lauda ntium porro elit exerc sunt repud.')
                        </p>
						<form action="#" method="post" class="rightside-form">
							<input type="email" name="email" placeholder="Subscribe" required="">
							<button class="btn button-icon" type="submit"><span class="fa fa-paper-plane-o" aria-hidden="true"></span></button>
						</form>
					</div>
				</div>
				<div class="row grid-col-3 grids-content1 bottom-border">
					<div class="columns text-md-left links-grid col-md-3 ">
						<ul class="links">
							<li><a href="#links">@lang('Privacy policy')</a></li>
						</ul>
					</div>
					<div class="columns text-center copyright-grid col-md-6 ">
                    <p>@lang('Copyright') SYRIAN CARDS © {{ date('Y') }} @lang('All Rights reserved')</p>
					</div>
					<div class="columns text-md-right social-grid col-md-3 ">
						<ul class="social">
							<li><a href="#url"><span class="fab fa-facebook" aria-hidden="true"></span></a></li>
							<li><a href="#url"><span class="fab fa-instagram" aria-hidden="true"></span></a></li>
							<li><a href="#url"><span class="fab fa-twitter" aria-hidden="true"></span></a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
    </section>
<!-- <div class="privacy-area privacy-area--style">
    <div class="container">
        <div class="copyright-area d-flex flex-wrap align-items-center justify-content-center">
            <div class="copyright">
                <p>@lang('Copyright') SYRIAN CARDS © {{ date('Y') }} @lang('All Rights reserved')</p>
            </div>
        </div>
    </div>
</div> -->

<!-- footer-section end -->


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<!-- jquery -->
<script src="{{asset($activeTemplateTrue.'js/jquery-3.6.0.min.js')}}"></script>
<!-- migarate-jquery -->
<script src="{{asset($activeTemplateTrue.'js/jquery-migrate-3.0.0.js')}}"></script>
<!-- bootstrap js -->
<script src="{{asset($activeTemplateTrue.'js/bootstrap.min.js')}}"></script>
<!-- magnific-popup js -->
<script src="{{asset($activeTemplateTrue.'js/jquery.magnific-popup.js')}}"></script>
<!-- nice-select js-->
<script src="{{asset($activeTemplateTrue.'js/jquery.nice-select.js')}}"></script>
<!-- swipper js -->
<script src="{{asset($activeTemplateTrue.'js/swiper.min.js')}}"></script>
<!--plugin js-->
<script src="{{asset($activeTemplateTrue.'js/plugin.js')}}"></script>
<!--chart js-->
<script src="{{asset($activeTemplateTrue.'js/chart.js')}}"></script>
<!-- viewport js -->
<script src="{{asset($activeTemplateTrue.'js/viewport.jquery.js')}}"></script>
<!-- odometer js -->
<script src="{{asset($activeTemplateTrue.'js/odometer.min.js')}}"></script>
<!-- wow js file -->
<script src="{{asset($activeTemplateTrue.'js/wow.min.js')}}"></script>
<!-- main -->
<script src="{{asset($activeTemplateTrue.'js/main.js')}}"></script>


@stack('script-lib')

@stack('script')

@include('partials.plugins')

@include('admin.partials.notify')


<script>
    (function ($) {
        "use strict";
        $(document).on("change", ".langSel", function() {
            window.location.href = "{{url('/')}}/change/"+$(this).val() ;
        });
    })(jQuery);
</script>

</body>
</html>
