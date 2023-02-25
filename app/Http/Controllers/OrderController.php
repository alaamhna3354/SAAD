<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\GeneralSetting;
use App\Models\Order;
use App\Models\Service;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class OrderController extends Controller
{
    public function order(Request $request, $category_id, $service_id)
    {
        $service = Service::findOrFail($service_id);
        $request->validate([
//            'link' => 'required|string',
//            'quantity' => 'required|integer|gte:' . $service->min . '|lte:' . $service->max,
        ]);
        if ($service->category->type == 'CODE' || $service->category->type == '5SIM')
            $price = (Auth::user()->is_special ? ($service->special_price ? getAmount($service->special_price) : getAmount($service->price_per_k)) : getAmount($service->price_per_k));
        else
            $price = (Auth::user()->is_special ? ($service->special_price ? getAmount($service->special_price) : getAmount($service->price_per_k)) : getAmount($service->price_per_k)) * $request->quantity;
        //Subtract user balance
        $user = auth()->user();
        if ($user->balance < $price) {
            $notify[] = ['error', 'Insufficient balance. Please deposit and try again!'];
            return back()->withNotify($notify);
        }
        if ($service->category->type == 'CODE') {
            $serviceCode = $service->serials->where('is_used', 0)->first();
            if ($serviceCode == null) {
                $notify[] = ['error', 'No Code Available ,Please Contact with Support To Order Code.'];
                return back()->withNotify($notify);
            }
        }
        DB::beginTransaction();
        try {
            if ($service->category->type != '5SIM')
                $user->balance -= $price;
            $user->save();

            //Make order
            $order = new Order();
            $order->user_id = $user->id;
            $order->category_id = $category_id;
            $order->service_id = $service_id;
            $order->link = $request->link;
            $order->quantity = $request->quantity;
            $order->price = $price;
            $order->remain = $request->quantity;
            $order->api_order = $service->api_service_id ? 1 : 0;
            if (isset($request->custom))
                $order->details = json_encode($request->custom, JSON_UNESCAPED_UNICODE);
            if ($service->category->type == 'CODE') {
                $order->code = $serviceCode->code;
                $order->status = 2;
            } elseif ($service->category->type == '5SIM') {
                $codes = (new ApiController)->fivesim($service->api_service_params);
                if ($codes == 0) {
                    $notify[] = ['error', 'حاول لاحقا او تواصل مع مدير الموقع.'];
                    return back()->withNotify($notify);
                } else {
                    $order->code = $codes['phone'];
                    $order->order_id_api = $codes['id'];
                    $order->status = 5;
                }
            }
            $order->save();
            if ($service->category->type != '5SIM') {
                //Create Transaction
                $transaction = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->amount = $price;
                $transaction->post_balance = getAmount($user->balance);
                $transaction->trx_type = '-';
                $transaction->details = 'Order for ' . $service->name;
                $transaction->trx = getTrx();
                $transaction->save();
            }
            if($service->api_service_id) {
                $apiOrder = $this->apiOrder($service->api_service_id, $order->link, $order->quantity);
                $order->api_order_id=$apiOrder['order'];
                $order->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
        //Create admin notification
        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New order request for ' . $service->name;
        $adminNotification->click_url = urlPath('admin.orders.details', $order->id);
        $adminNotification->save();

        //Send email to user
        $gnl = GeneralSetting::first();
        if ($service->category->type == 'CODE') {
            notify($user, 'COMPLETED_ORDER_code', [
                'service_name' => $service->name,
                'price' => $price,
                'currency' => $gnl->cur_text,
                'post_balance' => getAmount($user->balance),
                'code' => $serviceCode->code
            ]);
            $serviceCode->is_used = 1;
            $serviceCode->user = $user->id;
            $serviceCode->save();
        } elseif ($service->category->type == '5SIM') {
            notify($user, 'COMPLETED_ORDER_code', [
                'service_name' => $service->name,
                'price' => $price,
                'currency' => $gnl->cur_text,
                'post_balance' => getAmount($user->balance),
                'code' => $order->code
            ]);

        } else
            notify($user, 'PENDING_ORDER', [
                'service_name' => $service->name,
                'price' => $price,
                'currency' => $gnl->cur_text,
                'post_balance' => getAmount($user->balance),
            ]);
        adminnotify($user, 'NEW_ORDER', [
            'service_name' => $service->name,
            'username' => $user->username,
            'category_name' => $service->category->name,
        ]);
        $notify[] = ['success', 'Successfully placed your order!'];
        return back()->withNotify($notify);
    }

    public function massOrder()
    {
        $page_title = 'Mass Order';
        return view(activeTemplate() . 'user.orders.mass_order', compact('page_title'));
    }

    public function massOrderStore(Request $request)
    {
        if (substr_count($request->mass_order, '|') !== 4) {
            $notify[] = ['error', 'Service structures are not correct. Please retype!'];
            return back()->withNotify($notify)->withInput();
        }

        $separate_new_line = explode(PHP_EOL, $request->mass_order);
        foreach ($separate_new_line as $item) {

            $service_array = explode('|', $item);

            // Validation
            if (count($service_array) !== 3) {
                $notify[] = ['error', 'Service structures are not correct. Please retype!'];
                return back()->withNotify($notify)->withInput();
            }

            //Find service by service ID
            $service = Service::find($service_array[0]);

            if (!$service) {
                $notify[] = ['error', 'Service ID not found!'];
                return back()->withNotify($notify)->withInput();
            }

            if (filter_var($service_array[2], FILTER_VALIDATE_INT) === false) {
                $notify[] = ['error', 'Quantity should be an integer value!'];
                return back()->withNotify($notify)->withInput();
            }

            if ($service_array[2] < $service->min) {
                $notify[] = ['error', 'Quantity should be greater than or equal to ' . $service->min];
                return back()->withNotify($notify)->withInput();
            }

            if ($service_array[2] > $service->max) {
                $notify[] = ['error', 'Quantity should be less than or equal to ' . $service->max];
                return back()->withNotify($notify)->withInput();
            }
            // End validation

            $price = getAmount((Auth::user()->is_special ? ($service->special_price ? getAmount($service->special_price) : getAmount($service->price_per_k)) : getAmount($service->price_per_k)) * $service_array[2]);

            //Subtract user balance
            $user = auth()->user();
            if ($user->balance < $price) {
                $notify[] = ['error', 'Insufficient balance. Please deposit and try again!'];
                return back()->withNotify($notify);
            }

            $user->balance -= $price;
            $user->save();

            //Make order
            $order = new Order();
            $order->user_id = $user->id;
            $order->category_id = $service->category->id;
            $order->service_id = $service->id;
            $order->link = $service_array[1];
            $order->quantity = $service_array[2];
            $order->price = $price;
            $order->remain = $service_array[2];
            $order->save();

            //Create Transaction
            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $price;
            $transaction->post_balance = getAmount($user->balance);
            $transaction->trx_type = '-';
            $transaction->details = 'Order for ' . $service->name;
            $transaction->trx = getTrx();
            $transaction->save();

            //Create admin notification
            $adminNotification = new AdminNotification();
            $adminNotification->user_id = $user->id;
            $adminNotification->title = 'New order request for ' . $service->name;
            $adminNotification->click_url = urlPath('admin.orders.details', $order->id);
            $adminNotification->save();

            //Send email to user
            $gnl = GeneralSetting::first();
            notify($user, 'PENDING_ORDER', [
                'service_name' => $service->name,
                'price' => $price,
                'currency' => $gnl->cur_text,
                'post_balance' => getAmount($user->balance)
            ]);
        }

        $notify[] = ['success', 'Successfully placed your order!'];
        return back()->withNotify($notify);
    }

    //Orders history
    public function orderHistory()
    {
        $page_title = 'Order History';
        $empty_message = "No result found";
        $orders = Order::where('user_id', auth()->id())->with(['category', 'service'])->latest()->paginate(getPaginate());
        return view(activeTemplate() . 'user.orders.order_history', compact('page_title', 'orders', 'empty_message'));
    }

    public function orderPending()
    {
        $page_title = 'Pending Order';
        $empty_message = "No result found";
        $orders = Order::where('user_id', auth()->id())->pending()->with(['category', 'service'])->paginate(getPaginate());
        return view(activeTemplate() . 'user.orders.order_history', compact('page_title', 'orders', 'empty_message'));
    }

    public function orderProcessing()
    {
        $page_title = 'Processing Order';
        $empty_message = "No result found";
        $orders = Order::where('user_id', auth()->id())->processing()->with(['category', 'service'])->paginate(getPaginate());
        return view(activeTemplate() . 'user.orders.order_history', compact('page_title', 'orders', 'empty_message'));
    }

    public function orderCompleted()
    {
        $page_title = 'Completed Order';
        $empty_message = "No result found";
        $orders = Order::where('user_id', auth()->id())->completed()->with(['category', 'service'])->paginate(getPaginate());
        return view(activeTemplate() . 'user.orders.order_history', compact('page_title', 'orders', 'empty_message'));
    }

    public function orderCancelled()
    {
        $page_title = 'Cancelled Order';
        $empty_message = "No result found";
        $orders = Order::where('user_id', auth()->id())->cancelled()->with(['category', 'service'])->paginate(getPaginate());
        return view(activeTemplate() . 'user.orders.order_history', compact('page_title', 'orders', 'empty_message'));
    }

    public function orderRefunded()
    {
        $page_title = 'Refunded Order';
        $empty_message = "No result found";
        $orders = Order::where('user_id', auth()->id())->refunded()->with(['category', 'service'])->paginate(getPaginate());
        return view(activeTemplate() . 'user.orders.order_history', compact('page_title', 'orders', 'empty_message'));
    }

    public function finish5SImOrder($id, $result)
    {
        $order = Order::find($id);
        $user = auth()->user();
        if ($user->balance < $order->price) {
            $notify[] = ['error', 'Insufficient balance. Please deposit and try again!'];
            return back()->withNotify($notify);
        }
        $user->balance -= $order->price;
        $user->save();
        $order->status = 2;
        $order->verify = $result['sms'][0]['code'];
        $order->save();

        //Create Transaction
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $order->price;
        $transaction->post_balance = getAmount($user->balance);
        $transaction->trx_type = '-';
        $transaction->details = 'Order for ' . $order->service->name;
        $transaction->trx = getTrx();
        $transaction->save();
        return $result['sms'][0]['code'];
//        $notify[] = ['success', 'Successfully placed your order!'];
//        return back()->withNotify($notify);

    }

    public function apiOrder($service,$link,$qty)
    {

        $general = GeneralSetting::first();
        $url = $general->api_url;
        $arr = [
            'key' => $general->api_key,
            'action' => "add",
            'service' => $service,
            'link' => $link,
            'quantity' =>$qty
        ];

        $response = json_decode(curlPostContent($general->api_url,$arr));

        if (@$response->error){
            $notify[] = ['info', 'Please enter your api credentials from API Setting Option'];
            $notify[] = ['error', $response->error];
            throw new \Exception($notify);
        }
        return $response = collect($response);
    }

}
