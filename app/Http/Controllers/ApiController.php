<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\Category;
use App\Models\GeneralSetting;
use App\Models\Order;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Requests;

class ApiController extends Controller
{
    public function process(Request $request)
    {
        $rules = [
            'action' => 'required|string|in:services,add,status',
            'key' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors()->getMessages());
        }

        //Checking api key exist
        if (!User::where('api_key', $request->key)->exists()) {
            return response()->json(['error' => 'Invalid api key']);
        }

        //Checking the request action is services
        $action = $request->action;
        return $this->$action($request);

    }

    //List of services

    public function api()
    {
        $page_title = 'API Documentation';
        return view(activeTemplate() . 'user.api.api', compact('page_title'));
    }

    //Place new order

    public function generateNewKey()
    {
        $user = auth()->user();
        $user->api_key = sha1(time());
        $user->save();

        $notify[] = ['success', 'Generated new api key!'];
        return back()->withNotify($notify);
    }

    //Order Status

    public function fivesim($params)
    {
        $token=env('fivesim_token', 'null');
        $ch = curl_init();
        $country = 'russia';
        $operator = 'any';
        $url = 'https://5sim.net/v1/user/buy/activation/' . $params;
//        $url='https://5sim.net/v1/guest/products/'.$country.'/'.$operator;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $token;
        $headers[] = 'Accept: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpcode!=200) {
            return 0;
        }
        curl_close($ch);

        $result = json_decode($result, True);

//        $result=$this->finishOrder($result['id']);

        return $result;
    }

    public function checkSMS($orderID)
    {
        $order=Order::find($orderID);
        $id=$order->order_id_api;
       $token= env('fivesim_token', 'null');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://5sim.net/v1/user/check/' . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $token;
        $headers[] = 'Accept: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $result = json_decode($result, True);
        if (isset($result['sms'][0])) {
            $code = $result['sms'][0]['code'];
            if (isset($code)) {
             return   $this->finishOrder($id, $orderID);
            }
        }
        else return '0';

    }

    public function finishOrder($id,$orderid)
    {
       $token= env('fivesim_token', 'null');
        $ch = curl_init();
        $finishOrderUrl = 'https://5sim.net/v1/user/finish/' . $id;
        curl_setopt($ch, CURLOPT_URL, $finishOrderUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $token;
        $headers[] = 'Accept: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        $result = json_decode($result, True);
       $res= (new OrderController())->finish5SImOrder($orderid,$result);
        return $res;
    }






    /*
     * Web routes
     */

    // API Documentation

    private function services($request)
    {
        $services = Service::active()->with('category')->get(['id', 'name', 'price_per_k as rate', 'min', 'max']);
        return response()->json($services);
    }

    private function add($request)
    {
        //Service Validation
        $service_rules = [
            'service' => 'required|integer|gt:0'
        ];
        $validator = Validator::make($request->all(), $service_rules);
        if ($validator->fails()) {
            return response()->json($validator->errors()->getMessages());
        }

        //Service
        $service = Service::find($request->service);
        if (!$service) {
            return response()->json(['error' => 'Invalid Service Id']);
        }

        //Validation
        $rules = [
            'link' => 'required|string',
            'quantity' => 'required|integer|gte:' . $service->min . '|lte:' . $service->max,
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors()->getMessages());
        }

        $price = getAmount(($service->price_per_k) * $request->quantity);

        //Subtract user balance
        $user = User::where('api_key', $request->key)->firstOrFail();
        if ($user->balance < $price) {
            return response()->json(['error' => 'Insufficient balance']);
        }
        $user->balance -= $price;
        $user->save();

        //Save order record
        $order = new Order();
        $order->user_id = $user->id;
        $order->category_id = $service->category_id;
        $order->service_id = $service->id;
        $order->link = $request->link;
        $order->quantity = $request->quantity;
        $order->price = $price;
        $order->remain = $request->quantity;
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

        return response()->json(['order' => $order->id]);
    }


    private function status($request)
    {
        //Validation
        $rules = [
            'order' => 'required|integer'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors()->getMessages());
        }

        //Service
        $order = Order::where('id', $request->order)->select(['status', 'start_counter', 'remain'])->first();

        if (!$order) {
            return response()->json(['error' => 'Invalid Order Id']);
        }

        $order['status'] = ($order->status == 0 ? 'pending' : ($order->status == 1 ? 'processing' : ($order->status == 2 ? 'completed' : ($order->status == 3 ? 'cancelled' : 'refunded'))));

        return response()->json($order);
    }

    public function getPlayer($api,$id)
    {
        $category=Category::find($api);
//       $url="http://sim90.com/api/getPlayerName/".$category->slug."/".$playerid;
        $key=env('player_key', 'null');
        $url="http://www.m7-system.com:8080/match?key=".$key."&id=".$id."&product=".$category->slug;
//       $token='76|HZ04dcna7KKEjEChTE9Ydhzuk1xzGTJhbo2vkLnK';
//        $getPlayer = Http::withToken($token)->get($url);
        $getPlayer=Http::get($url);
        return   $result = json_decode($getPlayer, True);



//        $category=Category::find($api);
//        $url="http://sim90.com/api/getPlayerName/".$category->slug."/".$id;
//        $token='76|HZ04dcna7KKEjEChTE9Ydhzuk1xzGTJhbo2vkLnK';
//        $getPlayer = Http::withToken($token)->get($url);
//        return   $result = json_decode($getPlayer, True);
//
//        //        [freefire,pubg,likee,bego,ahlanChat,pubgLite,yalla]
    }
}
