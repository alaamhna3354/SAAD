@extends($activeTemplate.'layouts.master')
@section('content')
    <div class="row">
        <div class="col-lg-12">

            <div class="card b-radius--10 mb-4">
                    <div class="card-body p-0">
                        <div class="table-responsive--sm table-responsive">
                            <table class="table table--light tabstyle--two custom-data-table">
                                <thead>
                                <tr>
                                    <th scope="col">@lang('Order ID')</th>
                                    <th scope="col">@lang('Category')</th>
                                    <th scope="col">@lang('Service')</th>
                                    <th scope="col">@lang('Link')</th>
                                    <th scope="col">@lang('Quantity')</th>
                                    <th scope="col">@lang('Code')</th>
                                    <th scope="col">@lang('verify')</th>
                                    <th scope="col">@lang('Date')</th>
                                    <th scope="col">@lang('Status')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($orders as $item)
                                    <tr>
                                        <td data-label="@lang('Order ID')">{{ $item->id }}</td>
                                        <td data-label="@lang('Category')">{{__($item->category->name)}}</td>
                                        <td data-label="@lang('Service')" class="break_line">{{ __($item->service->name) }}</td>
                                        <td data-label="@lang('Link')"><a href="{{ empty(parse_url($item->link, PHP_URL_SCHEME)) ? 'https://' : null }}{{ $item->link }}" target="_blank">{{ $item->link }}</a></td>
                                        <td data-label="@lang('Quantity')">{{ $item->quantity }}</td>
                                        <td data-label="@lang('Code')">{{ $item->code }}</td>
                                        <td data-label="@lang('verify')" id="verfiy">
                                            <span id="{{$item->id}}">
                                            @if($item->verify )
                                                {{ $item->verify }}
                                            @elseif($item->category->type=='5SIM')
                                                <i class="fa fa-refresh" onclick="checksms({{ $item->id }})" ></i>
                                                @endif
                                            </span >
                                        </td>
                                        <td data-label="@lang('Date')">{{ showDateTime($item->created_at) }}</td>
                                        <td data-label="@lang('Status')">
                                            @if($item->status === 0)
                                                <span
                                                    class="text--small badge font-weight-normal badge--warning">@lang('Pending')</span>
                                            @elseif($item->status === 1)
                                                <span
                                                    class="text--small badge font-weight-normal badge--primary">@lang('Processing')</span>
                                            @elseif($item->status === 2)
                                                <span
                                                    class="text--small badge font-weight-normal badge--success">@lang('Completed')</span>
                                            @elseif($item->status === 3)
                                                <span
                                                    class="text--small badge font-weight-normal badge--danger">@lang('Cancelled')</span>
                                            @elseif($item->status === 4)
                                                <span
                                                        class="text--small badge font-weight-normal badge--danger">@lang('Refunded')</span>
                                            @elseif($item->status===5)
                                                <span
                                                        class="text--small badge font-weight-normal badge--dark">@lang('Waiting Code')</span>
                                            @endif

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
                        {{ $orders->links('admin.partials.paginate') }}
                    </div>
                </div><!-- card end -->

        </div>
    </div>
@endsection

@push('style')
    <style>
        .break_line{
            white-space: initial !important;
        }
    </style>
@endpush
<script>
    function checksms($id) {
        var url = "{{ route('user.checksms', ':id') }}";
        url = url.replace(':id', $id);
        {{--document.location.href=url;--}}
        $.ajax({
            type: 'GET',
            url:  url,
            // url : url.replace(':id', $id),
            // data: "id=" + $id , //laravel checks for the CSRF token in post requests

            success: function (data) {
               if(data!='0')
               {
                   $('#'+$id).text(data)
               }
               else {alert('تأكد انك طلبت الرمز او اعد المحاولة بعد قليل')}
            }
        });

    }
</script>
