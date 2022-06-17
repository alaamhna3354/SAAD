@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                {{--<div class="card-header">--}}
                {{--<a href="{{ route('admin.services.apiServices') }}" class="btn btn-outline--primary float-sm-right">@lang('API Services')</a>--}}
                {{--</div>--}}
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light tabstyle--two">
                            <thead>
                            <tr>
                                <th scope="col">@lang('Code')</th>
                                <th scope="col">@lang('Service')</th>
                                <th scope="col">@lang('Details')</th>
                                <th scope="col">@lang('Status')</th>
                                <th scope="col">@lang('Avalible')</th>
                                <th scope="col">@lang('User')</th>
                                <th scope="col">@lang('Actions')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($serials as $item)
                                <tr>
                                    <td data-label="@lang('Code')">{{__(@$item->code)}}</td>
                                    <td data-label="@lang('Service')">{{__(@$item->service->name)}}</td>
                                    <td data-label="@lang('Details')">{{__(@$item->details)}}</td>
                                    <td data-label="@lang('Status')">
                                        @if(@$item->status === 1)
                                            <span
                                                    class="text--small badge font-weight-normal badge--success">@lang('Active')</span>
                                        @else
                                            <span
                                                    class="text--small badge font-weight-normal badge--danger">@lang('Inactive')</span>
                                        @endif
                                    </td>
                                    <td data-label="@lang('Avalible')">
                                        @if(@$item->is_used === 0)
                                            <span
                                                    class="text--small badge font-weight-normal badge--success">@lang('Avalible')</span>
                                        @else
                                            <span
                                                    class="text--small badge font-weight-normal badge--danger">@lang('Used')</span>
                                        @endif
                                    </td>
                                    <td data-label="@lang('User')">{{$item->user}}</td>
                                    <td data-label="@lang('Action')">
                                        <a href="javascript:void(0)" class="icon-btn ml-1 editBtn"
                                           data-original-title="@lang('Edit')" data-toggle="tooltip"
                                           data-url="{{ route('admin.serials.update', $item->id)}}"
                                           data-code="{{ $item->code }}"
                                           data-service="{{ $item->service_id }}"
                                           data-details="{{ $item->details }}">
                                            <i class="la la-edit"></i>
                                        </a>

                                        <a href="javascript:void(0)"
                                           class="icon-btn btn--{{ $item->status ? 'danger' : 'success' }} ml-1 statusBtn"
                                           data-original-title="@lang('Status')" data-toggle="tooltip"
                                           data-url="{{ route('admin.services.status', $item->id) }}">
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
                    {{ $serials->links('admin.partials.paginate') }}
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
                <form class="form-horizontal" method="post" action="{{ route('admin.serials.store')}}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold ">@lang('Service') <span
                                        class="text-danger">*</span></label>
                            <select class="form-control" name="service">
                                <option selected>@lang('Choose')...</option>

                                @forelse($services as $service)
                                    @if($service->category->type=='CODE')
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                    @endif
                                @empty
                                @endforelse

                            </select>
                        </div>
                        
                        <div class="form-row form-group">
                            <label class="font-weight-bold ">@lang('Codes') <span
                                        class="text-danger">*</span></label>
                            <div class="col-sm-12">
                                <p class="label label-default">@lang('Each Code On New Line')</p>
                                <textarea class="form-control has-error bold"  id="code" name="code"  placeholder="e.g. 4775839022" required>
                                </textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">@lang('Details')</label>
                            <textarea class="form-control" name="details"></textarea>
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
                <form method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        <div class="form-group">
                            <label class="font-weight-bold ">@lang('Service') <span
                                        class="text-danger">*</span></label>
                            <select class="form-control" name="service">
                                <option selected>@lang('Choose')...</option>
                                @forelse($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @empty
                                @endforelse

                            </select>
                        </div>

                        <div class="form-row form-group">
                            <label class="font-weight-bold ">@lang('Code') <span
                                        class="text-danger">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control has-error bold " id="code" name="code" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">@lang('Details')</label>
                            <textarea class="form-control" name="details"></textarea>
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
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
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

@push('style')
    <style>
        .break_line {
            white-space: initial !important;
        }
    </style>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";
            $('.editBtn').on('click', function () {
                var modal = $('#editModal');
                var url = $(this).data('url');
                var code = $(this).data('code');
                var service_id = $(this).data('service');
                var details = $(this).data('details');

                modal.find('form').attr('action', url);
                modal.find('input[name=code]').val(code);
                modal.find('select[name=service]').val(service_id);
                modal.find('textarea[name=details]').val(details);
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
