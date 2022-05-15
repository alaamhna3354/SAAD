@extends($activeTemplate.'layouts.master')
<style>
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance: textfield;
    }
</style>
@section('content')
    <div class="row">
        <div class="col-lg-12">
            {{--@forelse($categories as $category)--}}
            {{--@continue(count($category->services) < 1)--}}
            <div class="card b-radius--10 mb-4">
                {{--<div class="card-header"><h3>@lang($category->name)</h3></div>--}}
                <div class="card-body p-0">
                    @php
                        $services = $category->services()->active()->latest('id')->paginate(getPaginate(10), ['*'], slug($category->name))
                    @endphp
                    <div class="container">
                        <div class="row">

                            @foreach ($services as $item)
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card border-0 shadow">
                                        <a href="javascript:void(0)" class="orderBtn"
                                           data-original-title="@lang('Buy')" data-toggle="tooltip"
                                           data-url="{{ route('user.order', [$category->id, $item->id])}}"
                                           data-price_per_k="{{ getAmount($item->price_per_k) }}"
                                           data-min="{{ $item->min }}" data-max="{{ $item->max }}">
                                            <img src="{{$item->image ? getImage(imagePath()['service']['path'].'/'. $item->image,imagePath()['service']['size']) : getImage(imagePath()['category']['path'].'/'. $category->image,imagePath()['category']['size'])}}"
                                                 class="card-img-top" alt="...">
                                            <div class="card-body text-center">
                                                <h5 class="card-title mb-0">{{__($item->name)}}</h5>
                                                <div class="card-text text-black-50">{{ $general->cur_sym . getAmount($item->price_per_k) }}</div>
                                                @if($item->details)
                                                    <a href="javascript:void(0)"
                                                       class="icon-btn btn--info detailsBtn"
                                                       data-original-title="@lang('Details')" data-toggle="tooltip"
                                                       data-details="{{ $item->details }}">
                                                        <i class="la la-info"></i>
                                                    </a>
                                                @endif
                                                <a href="javascript:void(0)" class="icon-btn orderBtn"
                                                   data-original-title="@lang('Buy')" data-toggle="tooltip"
                                                   data-url="{{ route('user.order', [$category->id, $item->id])}}"
                                                   data-price_per_k="{{ getAmount($item->price_per_k) }}"
                                                   data-min="{{ $item->min }}" data-max="{{ $item->max }}
                                                {{--@if(isset($category->custom_additional_field_name))--}}

                                                {{--@endif--}}
                                                        ">
                                                    <i class="fa fa-cart-plus"></i>
                                                </a>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer">
                        {{ $services->withQueryString()->links('admin.partials.paginate') }}
                    </div>
                </div><!-- card end -->
            </div>
        </div>


        {{-- Details MODAL --}}
        <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel"><i
                                    class="fa fa-share-square"></i> @lang('Details')</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div id="details">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Order MODAL --}}
        <div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel"><i
                                    class="fa fa-fw fa-share-square"></i>@lang('Place a new order')</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">×</span></button>
                    </div>
                    <form method="post">
                        @csrf
                        <div class="modal-body">

                            <div class="form-row form-group">
                                <label for="link"
                                       class="font-weight-bold">{{$category->field_name ? $category->field_name : "الرقم"}}
                                    <span
                                            class="text-danger">*</span></label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control has-error bold" id="link" name="link"
                                           required>
                                </div>
                                @if(isset($category->custom_additional_field_name))
                                    {{--<form action="{{url('user/address')}}" class="mt-5 check-out-form" method="post">--}}
                                    <label for="link"
                                           class="font-weight-bold">{{$category->custom_additional_field_name}} <span
                                                class="text-danger">*</span></label>
                                    <div class="row">
                                        <div class="col-sm-10">

                                            <input type="text" class="form-control has-error bold" id="player" name="custom"
                                                   required>
                                        </div>
                                        <div class="col-sm-2">
                                            <a href="#" id="get_player_name" class="pull-right mr-2" >
                                                <i class="fa fa-cart-plus"></i>
                                            </a>
                                        </div>
                                    </div>


                                @endif
                            </div>
                                <div class="form-row form-group">
                                    <label for="quantity" class="font-weight-bold">@lang('Quantity') <span
                                                class="text-danger">*</span></label>
                                    <div class="col-sm-12">
                                        <input type="number" class="form-control has-error bold" id="quantity"
                                               name="quantity" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">@lang('Min')</div>
                                            </div>
                                            <input type="text" name="min" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">@lang('Max')</div>
                                            </div>
                                            <input type="text" name="max" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">@lang('Price')</div>
                                            </div>
                                            <input type="text" class="form-control total_price text--success"
                                                   name="price" readonly>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                                <button type="submit" class="btn btn--primary" id="btn-save"
                                        value="add">@lang('Submit')</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    {{--<div class="container">--}}
    {{--<div class="row">--}}
    {{--<!-- products -->--}}
    {{--@forelse($categories as $category)--}}

    {{--@continue(count($category->services) < 1)--}}

    {{--<div class="col-xl-3 col-md-6 mb-4">--}}
    {{--<div class="card border-0 shadow">--}}
    {{--<a href="">--}}
    {{--<img src="https://source.unsplash.com/TMgQMXoglsM/500x350" class="card-img-top"--}}
    {{--alt="...">--}}
    {{--<div class="card-body text-center">--}}
    {{--<h5 class="card-title mb-0">@lang($category->name)</h5>--}}
    {{--<div class="card-text text-black-50">Web Developer</div>--}}
    {{--</div>--}}
    {{--</a>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--@endforeach--}}
    {{--</div>--}}
    {{--<!-- /.row -->--}}
    {{--</div>--}}
@endsection

@push('style')
    <style>
        .break_line {
            white-space: initial !important;
        }
    </style>
@endpush

@push('script')
    <script>
        {{--function test() {--}}
            {{--var link = $('#link').val()--}}
            {{--document.getElementById('player').value ='{{getPlayerName()}}';--}}

        {{--}--}}
        (function ($) {
            "use strict";

            $('.detailsBtn').on('click', function () {
                var modal = $('#detailsModal');
                var details = $(this).data('details');

                modal.find('#details').html(details);
                modal.modal('show');
            });

            $('.orderBtn').on('click', function () {
                var modal = $('#orderModal');
                var url = $(this).data('url');
                var price_per_k = $(this).data('price_per_k');
                var min = $(this).data('min');
                var max = $(this).data('max');

                //Calculate total price
                $(document).on("keyup", "#link", function () {
                    var link = $('#link').val()
                    
                    modal.find('input[name=custom]').val("{{getPlayer($category->api,12)}}");

                });

                //Calculate total price
                $(document).on("keyup", "#quantity", function () {
                    var quantity = $('#quantity').val()
                    var total_price = price_per_k * quantity;
                    modal.find('input[name=price]').val("{{ $general->cur_sym }}" + total_price.toFixed(3));
                });

                modal.find('form').attr('action', url);
                modal.find('input[name=quantity]').attr('min', min).attr('max', max);
                modal.find('input[name=min]').val(min);
                modal.find('input[name=max]').val(max);
                modal.modal('show');
            });

            //Scroll to paginate position
            var pathName = document.location.pathname;
            window.onbeforeunload = function () {
                var scrollPosition = $(document).scrollTop();
                sessionStorage.setItem("scrollPosition_" + pathName, scrollPosition.toString());
            }
            if (sessionStorage["scrollPosition_" + pathName]) {
                $(document).scrollTop(sessionStorage.getItem("scrollPosition_" + pathName));
            }
        })(jQuery);
    </script>
@endpush
