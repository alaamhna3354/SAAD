@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-header">
                    <a href="{{ route('admin.services.apiServices') }}" class="btn btn-outline--primary float-sm-right">@lang('API Services')</a>
                </div>
                <div class="col-md-4 col-xl-3">
                    <div class="form-group">
                        <select name="category" id="categorylist" class="form-control statusfield" onchange="categoryFilter()">
                            <option value="-1" selected >@lang('المنتج')</option>
                            @foreach($categories as $category)
                                <option value="{{$category->name}}" >@lang($category->name)</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light tabstyle--two " id="servicelist">
                            <thead>
                            <tr>
                                <th scope="col">@lang('Name')</th>
                                <th scope="col">@lang('Category')</th>
                                <th scope="col">@lang('Price')</th>
                                <th scope="col">@lang('Min')</th>
                                <th scope="col">@lang('Max')</th>
                                <th scope="col">@lang('image')</th>
                                <th scope="col">@lang('Status')</th>
                                <th scope="col">@lang('Actions')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($services as $item)
                                <tr>
                                    <td data-label="@lang('Name')">{{__(@$item->name)}}</td>
                                    <td data-label="@lang('Category')">{{__(@$item->category->name)}}</td>
                                    <td data-label="@lang('Price Per 1k')">{{ $general->cur_sym . getAmount(@$item->price_per_k) }}</td>
                                    <td data-label="@lang('Min')">{{__(@$item->min)}}</td>
                                    <td data-label="@lang('Max')">{{__(@$item->max)}}</td>
                                    <td data-label="@lang('image')"><img src="{{ getImage(imagePath()['service']['path'].'/'. $item->image,imagePath()['service']['size']) ?? ''}}"></td>
                                    <td data-label="@lang('Status')">
                                        @if(@$item->status === 1)
                                            <span
                                                class="text--small badge font-weight-normal badge--success">@lang('Active')</span>
                                        @else
                                            <span
                                                class="text--small badge font-weight-normal badge--danger">@lang('Inactive')</span>
                                        @endif
                                    </td>
                                    <td data-label="@lang('Action')">
                                        <a href="javascript:void(0)" class="icon-btn ml-1 editBtn"
                                           data-original-title="@lang('Edit')" data-toggle="tooltip"
                                           data-url="{{ route('admin.services.update', $item->id)}}" data-name="{{ $item->name }}"
                                        data-category="{{ $item->category_id }}"
                                        data-price_per_k="{{ getAmount($item->price_per_k) }}"
                                        data-min="{{ $item->min }}" data-max="{{ $item->max }}" data-details="{{ $item->details }}" data-api_service_id="{{ $item->api_service_id }}"
                                        data-special_price="{{ getAmount($item->special_price)}}">
                                            <i class="la la-edit"></i>
                                        </a>

                                        <a href="javascript:void(0)" class="icon-btn btn--{{ $item->status ? 'danger' : 'success' }} ml-1 statusBtn" data-original-title="@lang('Status')" data-toggle="tooltip" data-url="{{ route('admin.services.status', $item->id) }}">
                                            <i class="la la-eye{{ $item->status ? '-slash' : null }}"></i>
                                        </a>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($empty_message) }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>

                <div class="card-footer">
                    {{ $services->links('admin.partials.paginate') }}
                </div>
            </div><!-- card end -->
        </div>
    </div>



    {{-- NEW MODAL --}}
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"><i
                            class="fa fa-share-square"></i> @lang('Add New')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                </div>
                <form class="form-horizontal" method="post" action="{{ route('admin.services.store')}}" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body">

                        <div class="form-group">
                            <label class="font-weight-bold ">@lang('Category') <span
                                    class="text-danger">*</span></label>
                            <select class="form-control" name="category" id="category" onchange="showExtraField()">
                                <option selected>@lang('Choose')...</option>

                                @forelse($categories as $category)
                                    <option value="{{ $category->id }}" id="{{$category->type}}">{{ $category->name }}</option>
                                @empty
                                @endforelse

                            </select>
                        </div>

                        <div class="form-row form-group">
                            <label class="font-weight-bold ">@lang('Name') <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control has-error bold " id="code" name="name" required>
                            </div>
                        </div>
                        <div class="form-group" id="extra" style="display: none;">
                            <label>@lang('Select Country')</label>
                            <select class="form-control" id="country" name="country">
                                <option disabled value="" selected hidden>@lang('Select Country')</option>
                                @foreach(get5SimCountries() as $key=> $country)
                                <option value="{{$key}}">{{$country}}</option>
                                @endforeach
                            </select>
                            @if($errors->has('type'))
                                <div class="error text-danger">@lang($errors->first('type')) </div>
                            @endif
                            <label>@lang('Select Product')</label>
                            <select class="form-control" id="product" name="product">
                                <option disabled value="" selected hidden>@lang('Select Product')</option>
                                @foreach(get5SimProducts() as  $product)
                                    <option value="{{$product}}">{{$product}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold ">@lang('Price Per 1k') <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="inlineFormInputGroupUsername2" name="price_per_k">
                                <div class="input-group-append">
                                    <div class="input-group-text">{{ $general->cur_text }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold ">@lang('Special Price') <span></span>

                            <div class="input-group">
                                <input type="text" class="form-control" id="inlineFormInputGroupUsername2" name="special_price">
                                <div class="input-group-append">
                                    <div class="input-group-text">{{ $general->cur_text }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="avatar-edit">
                            <input type="file" class="profilePicUpload" name="image" id="profilePicUpload1" accept=".png, .jpg, .jpeg">
                            <label for="profilePicUpload1" class="bg--success">@lang('Upload Image')</label>
                            <small class="mt-2 text-facebook">@lang('Supported files'): <b>jpeg, jpg.</b> @lang('Image will be resized into') {{imagePath()['category']['size']}}px </small>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">@lang('Min')</label>
                                    <input type="text" name="min" class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">@lang('Max')</label>
                                    <input type="text" name="max" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">@lang('Details')</label>
                            <textarea class="form-control" name="details" required></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary" id="btn-save" value="add">@lang('Save')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"><i
                            class="fa fa-fw fa-share-square"></i>@lang('Edit')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                </div>
                <form method="post" enctype="multipart/form-data" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        <div class="form-group">
                            <label class="font-weight-bold ">@lang('Category') <span
                                    class="text-danger">*</span></label>
                            <select class="form-control" name="category" id="category1" onchange="showExtraField1()">
                                <option selected>@lang('Choose')...</option>

                                @forelse($categories as $category)
                                    <option value="{{ $category->id }}" id="{{$category->type}}">{{ $category->name }}</option>
                                @empty
                                @endforelse

                            </select>
                        </div>

                        <div class="form-row form-group">
                            <label class="font-weight-bold ">@lang('Name') <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control has-error bold " id="code" name="name" required>
                            </div>
                        </div>
                        <div class="form-group" id="extra1" style="display: none;">
                            <label>@lang('Select Country')</label>
                            <select class="form-control" id="country" name="country">
                                <option disabled value="" selected hidden>@lang('Select Country')</option>
                                @foreach(get5SimCountries() as $key=> $country)
                                    <option value="{{$key}}">{{$country}}</option>
                                @endforeach
                            </select>
                            @if($errors->has('type'))
                                <div class="error text-danger">@lang($errors->first('type')) </div>
                            @endif
                            <label>@lang('Select Product')</label>
                            <select class="form-control" id="product" name="product">
                                <option disabled value="" selected hidden>@lang('Select Product')</option>
                                @foreach(get5SimProducts() as  $product)
                                    <option value="{{$product}}">{{$product}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="avatar-edit">
                            <input type="file" class="profilePicUpload" name="image" id="profilePicUpload1" accept=".png, .jpg, .jpeg">
                            <label for="profilePicUpload1" class="bg--success">@lang('Upload Image')</label>
                            <small class="mt-2 text-facebook">@lang('Supported files'): <b>jpeg, jpg.</b> @lang('Image will be resized into') {{imagePath()['category']['size']}}px </small>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold ">@lang('Price Per 1k') <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="inlineFormInputGroupUsername2" name="price_per_k">
                                <div class="input-group-append">
                                    <div class="input-group-text">{{ $general->cur_text }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold ">@lang('Special Price') <span></span>
                            <div class="input-group">
                                <input type="text" class="form-control" id="inlineFormInputGroupUsername2" name="special_price">
                                <div class="input-group-append">
                                    <div class="input-group-text">{{ $general->cur_text }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">@lang('Min')</label>
                                    <input type="text" name="min" class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">@lang('Max')</label>
                                    <input type="text" name="max" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">@lang('Details')</label>
                            <textarea class="form-control" name="details" required></textarea>
                        </div>
                        <div class="form-group api_service_id">

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary" id="btn-save"
                                value="add">@lang('Update')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Status MODAL --}}
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">@lang('Update Status')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <form method="post" action="">
                    @csrf
                    <input type="hidden" name="delete_id" id="delete_id" class="delete_id" value="0">
                    <div class="modal-body">
                        <p class="text-muted">@lang('Are you sure to change the status?')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('breadcrumb-plugins')
    <span style="margin: 5px">
    <a class="btn btn-sm btn--primary box--shadow1 text-white text--small" data-toggle="modal" data-target="#myModal"><i
                class="fa fa-fw fa-plus"></i>@lang('Add New')</a>
    </span>
@endpush
@push('breadcrumb-plugins')

    <form action="{{ route('admin.services.search') }}" method="GET" class="form-inline float-sm-right bg--white">
        <div class="input-group has_append">
            {{--<input type="text" name="search" class="form-control" placeholder="@lang('Username or Order ID')" value="{{ $search ?? '' }}" required>--}}
            <select class="form-control" name="search">
                <option value="" selected>@lang('Choose')...</option>
                @forelse($categories as $category)
                    <option value="{{ $category->id }}" id="{{$category->type}}">{{ $category->name }}</option>
                @empty
                @endforelse
            </select>
            <div class="input-group-append">
                <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
@endpush

@push('style')
    <style>
        .break_line{
            white-space: initial !important;
        }
    </style>
@endpush

@push('script')
    <script>
        function showExtraField(){
            var opti = document.getElementById('category').options;
            opt=opti[opti.selectedIndex].id;
            // opt=opt.options[opt.selectedIndex].id;

            if (opt == "5SIM" ){
                $('#extra').attr('style','display : block;');
                $('#country').attr(require);
            }
            else {
                    $('#extra').attr('style','display : none;')
                }

                console.log(opt)
            }
        function showExtraField1(){
            var opti = document.getElementById('category1').options;
            opt=opti[opti.selectedIndex].id;
            // opt=opt.options[opt.selectedIndex].id;

            if (opt == "5SIM" ){
                $('#extra1').attr('style','display : block;');
                $('#extra1').attr(require);
            }
            else {
                $('#extra1').attr('style','display : none;')
            }

            console.log(opt)
        }

        (function ($) {
            "use strict";
            $('.editBtn').on('click', function () {
                var modal = $('#editModal');
                var url = $(this).data('url');
                var name = $(this).data('name');
                var category_id = $(this).data('category');
                var price_per_k = $(this).data('price_per_k');
                var min = $(this).data('min');
                var max = $(this).data('max');
                var details = $(this).data('details');
                var api_service_id = $(this).data('api_service_id');
                var special_price=$(this).data('special_price');
                $('.api_service_id').empty();
                if(api_service_id){
                    $('.api_service_id').html(`<label class="font-weight-bold">@lang('Service Id (If order process through API)')</label>
                            <input type="text" name="api_service_id" value="${api_service_id}" class="form-control">`);
                }


                modal.find('form').attr('action', url);
                modal.find('input[name=name]').val(name);
                modal.find('select[name=category]').val(category_id);
                modal.find('input[name=price_per_k]').val(price_per_k);
                modal.find('input[name=special_price]').val(special_price);
                modal.find('input[name=min]').val(min);
                modal.find('input[name=max]').val(max);
                modal.find('textarea[name=details]').val(details);
                modal.modal('show');
                showExtraField1();
            });

            $('.statusBtn').on('click', function () {
                var modal = $('#statusModal');
                var url = $(this).data('url');

                modal.find('form').attr('action', url);
                modal.modal('show');
            });
        })(jQuery);

        // function categoryFilter() {
        //     var input, filter, table, tr, td, i;
        //     input = document.getElementById("categorylist");
        //     filter = input.value;
        //     table = document.getElementById("servicelist");
        //     tr = table.getElementsByTagName("tr");
        //     for (i = 0; i < tr.length; i++) {
        //         td = tr[i].getElementsByTagName("td")[1];
        //         if (td) {
        //             if (td.innerHTML == filter || filter==-1) {
        //                 tr[i].style.display = "";
        //             } else {
        //                 tr[i].style.display = "none";
        //             }
        //         }
        //     }
        // }

    </script>

@endpush
