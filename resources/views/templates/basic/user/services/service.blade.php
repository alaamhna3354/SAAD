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
            <div class="card b-radius--10 mb-4" style="background: transparent;">
                {{--<div class="card-header"><h3>@lang($category->name)</h3></div>--}}
                <div class="card-body p-0">
                    @php
                        $services = $category->services()->active()->latest('id')->paginate(getPaginate(10), ['*'], slug($category->name))
                    @endphp
                    <div class="container">
                        <div class="row">

                            @foreach ($services as $item)
                                <div class="col-3 mt-4 mb-4 order-items">
                                    <div class="card border-0 shadow">
                                        <a href="javascript:void(0)" class="orderBtn"
                                           data-original-title="@lang('Buy')" data-toggle="tooltip"
                                           data-url="{{ route('user.order', [$category->id, $item->id])}}"
                                           data-price_per_k="{{Auth::user()->is_special ? ( $item->special_price ? getAmount($item->special_price) : getAmount($item->price_per_k)) : getAmount($item->price_per_k)}}"
                                           data-min="{{ $item->min }}" data-max="{{ $item->max }}"
                                           data-category="{{$category->id}}">
                                            <img src="{{$item->image ? getImage(imagePath()['service']['path'].'/'. $item->image,imagePath()['service']['size']) : getImage(imagePath()['category']['path'].'/'. $category->image,imagePath()['category']['size'])}}"
                                                 class="card-img-top" alt="...">
                                            <div class="card-body text-center">
                                                <h5 class="card-title mb-0">{{__($item->name)}}</h5>
                                                <div class="card-text text-black-50 mb-2">{{ $general->cur_sym . (Auth::user()->is_special ? ( $item->special_price ? getAmount($item->special_price) : getAmount($item->price_per_k)) : getAmount($item->price_per_k) )}}</div>
                                                @if($item->details)
                                                    <a href="javascript:void(0)"
                                                       class="icon-btn btn--info detailsBtn S m-2"
                                                       data-original-title="@lang('Details')" data-toggle="tooltip"
                                                       data-details="{{ $item->details }}">
                                                        <i class="la la-info "></i>
                                                    </a>
                                                @endif
                                                <a href="javascript:void(0)" class="icon-btn orderBtn"
                                                   data-original-title="@lang('Buy')" data-toggle="tooltip"
                                                   data-url="{{ route('user.order', [$category->id, $item->id])}}"
                                                   data-price_per_k="{{Auth::user()->is_special ? ( $item->special_price ? getAmount($item->special_price) : getAmount($item->price_per_k)) : getAmount($item->price_per_k)}}"
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
                    <div class="card-footer" style="background: transparent;border:none">
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
                                @if($category->type=="GAME")
                                    <div class=" col-12 col-sm-6 mb-2 text-right">
                                        <div class="input-group">
                                            <div class="input-group-prepend ">
                                                <label class="input-group-text group-label"
                                                       for="player_number">@lang('رقم اللاعب')</label>
                                            </div>
                                            <input type="text" name="link" class="form-control group-input"
                                                   id="player_number" required>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 mb-2 text-right">
                                        <div class="input-group">
                                            <div class="input-group-prepend ">
                                                <label class="input-group-text group-label"
                                                       for="player_name">@lang('اسم اللاعب')</label>
                                            </div>
                                            <input type="text" name="player_name" class="form-control group-input"
                                                   id="player_name" required>
                                            <div class="col-2 col-sm-2 d-flex align-items-center refresh mb-2">
                                                <i class="fas fa-sync-alt " onclick="getName({{$category->id}})"></i>
                                            </div>
                                        </div>
                                    </div>

                                @elseif(isset($category->field_name))
                                    <div class="col-sm-8 m-1 text-right">
                                        <label for="link"
                                               class="font-weight-bold">{{$category->field_name}}
                                            <span
                                                    class="text-danger">*</span></label>
                                        <input type="text" class="form-control has-error bold" id="link" name="link"
                                               required>
                                    </div>
                                @endif
                                @if(isset($category->custom_additional_field_name) && $category->type!="GAME")
                                    {{--<form action="{{url('user/address')}}" class="mt-5 check-out-form" method="post">--}}
                                    @foreach(explode(',',$category->custom_additional_field_name) as $field)
                                        <div class="col-sm-8 m-1 text-right">
                                            <label for="link"
                                                   class="font-weight-bold">{{$field}} <span
                                                        class="text-danger">*</span></label>
                                            <input type="text" class="form-control has-error bold"
                                                   name="custom[{{$field}}]"
                                                   required>
                                        </div>
                                    @endforeach

                                <!-- <div class="col-sm-2">
                                            <a href="#" id="get_player_name" class="pull-right mr-2" >
                                                <i class="fa fa-cart-plus"></i>
                                            </a>
                                        </div> -->


                                @endif
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="input-group">
                                        <div class="input-group-prepend ">
                                            <div class="input-group-text group-label">@lang('Quantity')</div>
                                        </div>
                                        <input type="number" id="quantity" name="quantity"
                                               class="form-control group-input" required
                                               @if($category->type == '5SIM' || $category->type=='CODE')
                                               readonly
                                                @endif
                                        >
                                    </div>
                                </div>
                                @if($category->type != '5SIM' && $category->type!='CODE')
                                    <div class="form-group col-md-6">
                                        <div class="input-group">
                                            <div class="input-group-prepend ">
                                                <div class="input-group-text group-label">@lang('Min')</div>
                                            </div>
                                            <input type="text" name="min" class="form-control group-input" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text group-label">@lang('Max')</div>
                                            </div>
                                            <input type="text" name="max" class="form-control group-input" readonly>
                                        </div>
                                    </div>
                                @endif
                                <div class="form-group col-md-6">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text group-label">@lang('Price')</div>
                                        </div>
                                        <input type="text" id="price"
                                               class="form-control total_price text--success group-input"
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
        function getName(id) {
            var player_number = $('#player_number').val();
            if (player_number == "") {
                $('.vald-player-number').removeClass('hidden');
            }
            else {
                $.ajax({
                    url: '/user/player/' + id + '/' + player_number,
                    type: "GET",
                    success: function (response) {
                        console.log(response)
                        $('#player_name').val(response.username);
                    },
                })
            }
        };


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
                modal.find('input[name=quantity]').val(1);
                modal.find('input[name=price]').val("{{ $general->cur_sym }}" + price_per_k.toFixed(3));
                console.log(modal.find('input[name=quantity]').val(1))
                //Calculate total price

                {{--$(document).on("keyup", "#link", function () {--}}
                {{--var link = $('#link').val()--}}
                {{--var url="{{route('player',[$category->api,':link'])}}";--}}
                {{--url = url.replace(':link', link);--}}
                {{--// modal.find('input[name=custom]').val(1);--}}
                {{--});--}}

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
