@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light tabstyle--two custom-data-table">
                            <thead>
                            <tr>
                                <th scope="col">@lang('Name')</th>
                                <th scope="col">@lang('Image')</th>
                                <th scope="col">@lang('Status')</th>
                                <th scope="col">@lang('Actions')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($categories as $item)
                                <tr>
                                    <td data-label="@lang('Name')">{{__($item->name)}}</td>
                                    <td data-label="@lang('Image')"><img src="{{ getImage(imagePath()['category']['path'].'/'. $item->image,imagePath()['category']['size'])}}"></td>
                                    <td data-label="@lang('Status')">
                                        @if($item->status === 1)
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
                                           data-url="{{ route('admin.categories.update', $item->id)}}" data-name="{{ $item->name }}"
                                           data-sort="{{$item->sort}}" data-type="{{$item->type}}"
                                           data-field="{{$item->field_name}}" data-special="{{$item->additional_field}}">
                                            <i class="la la-edit"></i>
                                        </a>
                                        <a href="javascript:void(0)" class="icon-btn btn--{{ $item->status ? 'danger' : 'success' }} ml-1 statusBtn" data-original-title="@lang('Status')" data-toggle="tooltip" data-url="{{ route('admin.categories.status', $item->id) }}">
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
            </div><!-- card end -->
        </div>
    </div>



    {{-- NEW MODAL --}}
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"><i
                                class="fa fa-share-square"></i> @lang('Add New Category')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                </div>
                <form class="form-horizontal" method="post" action="{{ route('admin.categories.store')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-row form-group">
                            <label class="font-weight-bold ">@lang('Name') <span
                                        class="text-danger">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control has-error bold " id="code" name="name" required placeholder="@lang('Enter category name')">
                            </div>
                        </div>
                        <div class="avatar-edit">
                            <input type="file" class="profilePicUpload" name="image" id="profilePicUpload1" accept=".png, .jpg, .jpeg">
                            <label for="profilePicUpload1" class="bg--success">@lang('Upload Image')</label>
                        </div>
                        <div class="form-group">
                            <label>@lang('Select Type')</label>
                            <select class="form-control" id="type" name="type" onchange="showExtraField()">
                                <option disabled value="" selected hidden>@lang('Select Type')</option>
                                <option value="GAME">@lang('GAME')</option>
                                <option value="CODE">@lang('CODE')</option>
                                <option value="BALANCE">@lang('BALANCE')</option>
                                <option value="5SIM">@lang('5SIM')</option>
                            </select>
                            @if($errors->has('type'))
                                <div class="error text-danger">@lang($errors->first('type')) </div>
                            @endif
                        </div>
                        <div class="form-row form-group">
                            <label class="font-weight-bold ">@lang('الحقل المميز : اسم اللاعب او رقم الهاتف او .....') </label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control has-error bold " id="field" name="field_name"  placeholder="@lang('اتركه فارغاً اذا كان المنتج هو بطاقات غوغل بلاي او ماشابه')">
                            </div>
                        </div>
                        <div class="form-row form-group">
                            <label class="font-weight-bold ">@lang('الحقول الإضافية : مثل اسم اللاعب -رقم الهاتف او كلمة المرور ') <span
                                        class="text-danger">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control has-error bold " id="code" name="custom_additional_field_name"  placeholder="@lang('لإضافة اكثر من حقل ضع فاصلة بين اسماء الحقول مثال: البريد,كلمة المرور')">
                            </div>
                        </div>

                        <div class="form-row form-group" hidden>
                            <label class="font-weight-bold ">@lang('Api Url هذا الحقل للمطور يرجى عدم تعديله ') <span
                                        class="text-danger">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control has-error bold " id="code" name="api"  placeholder="@lang('api')">
                            </div>
                        </div>
                        <div class="form-row form-group" >
                            <label class="font-weight-bold ">@lang('ترتيب المنتج') <span
                                        class="text-danger">*</span></label>
                            <div class="col-sm-12">
                                <input type="number" class="form-control has-error bold " id="sort" name="sort"  placeholder="@lang('')">
                            </div>
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
         aria-hidden="true" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"><i
                                class="fa fa-fw fa-share-square"></i>@lang('Edit')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-row form-group">
                            <label class="font-weight-bold ">@lang('Name') <span
                                        class="text-danger">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control has-error bold " id="code" name="name" value="{{$item->name ?? ''}}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Select Type')</label>
                            <select class="form-control" id="type" name="type">
                                <option value="{{$item->type ?? ''}}" selected
                                        hidden>{{$item->type ?? ''}}</option>
                                <option value="GAME">@lang('GAME')</option>
                                <option value="CODE">@lang('CODE')</option>
                                <option value="BALANCE">@lang('BALANCE')</option>
                                <option value="5SIM">@lang('5SIM')</option>
                            </select>
                            @if($errors->has('type'))
                                <div class="error text-danger">@lang($errors->first('type')) </div>
                            @endif
                        </div>
                        <div class="form-row form-group">
                            <label class="font-weight-bold ">@lang('الحقل المميز : اسم اللاعب او رقم الهاتف او .....') </label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control has-error bold " id="field" name="field_name"  placeholder="@lang('Field Name')" value="{{$item->field_name ?? ''}}">
                            </div>
                        </div>
                        <div class="form-row form-group">
                            <label class="font-weight-bold ">@lang('الحقول الإضافية : مثل اسم اللاعب -رقم الهاتف او كلمة المرور ') <span
                                        class="text-danger">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control has-error bold " id="code" name="custom_additional_field_name"  placeholder="@lang('additional Field')">
                            </div>
                        </div>
                        <div class="form-row form-group" hidden>
                            <label class="font-weight-bold ">@lang('Api Url هذا الحقل للمطور يرجى عدم تعديله') <span
                                        class="text-danger">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control has-error bold " id="code" name="api"  placeholder="@lang('api')">
                            </div>
                        </div>
                        <div class="form-row form-group" >
                            <label class="font-weight-bold ">@lang('ترتيب المنتج') <span
                                        class="text-danger">*</span></label>
                            <div class="col-sm-12">
                                <input type="number" class="form-control has-error bold " id="sort" name="sort" value="{{$item->sort ?? ''}}">
                            </div>
                        </div>
                        {{--<div class="form-row form-group">--}}
                        {{--<label class="font-weight-bold ">@lang('Image') <span--}}
                        {{--class="text-danger">*</span></label>--}}
                        {{--<div class="col-sm-12">--}}
                        {{--<input type="file" id="image" name="image">--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        <div class="avatar-edit">
                            <input type="file" class="profilePicUpload" name="image" id="profilePicUpload1" accept=".png, .jpg, .jpeg">
                            <label for="profilePicUpload1" class="bg--success">@lang('Upload Image')</label>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                            <button type="submit" class="btn btn--primary" id="btn-save"
                                    value="add">@lang('Update')</button>
                        </div>
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
    <a class="btn btn-sm btn--primary box--shadow1 text-white text--small" data-toggle="modal" data-target="#myModal"><i
                class="fa fa-fw fa-plus"></i>@lang('Add New')</a>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";
            $('.editBtn').on('click', function () {
                var modal = $('#editModal');
                var url = $(this).data('url');
                var name = $(this).data('name');
                var sort= $(this).data('sort');
                var field= $(this).data('field');
                var special= $(this).data('special');
                var type= $(this).data('type');
                console.log(type);
                modal.find('form').attr('action', url);
                modal.find('input[name=name]').val(name);
                modal.find(('input[name=sort]')).val(sort);
                modal.find(('input[name=field_name]')).val(field);
                modal.find(('input[name=custom_additional_field_name]')).val(special);
                modal.find(('select[name=type]')).val(type);
                modal.modal('show');
            });

            $('.statusBtn').on('click', function () {
                var modal = $('#statusModal');
                var url = $(this).data('url');

                modal.find('form').attr('action', url);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
