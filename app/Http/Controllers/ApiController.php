<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\GeneralSetting;
use App\Models\Order;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
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

        $token = 'eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE2NzkzNDE1NTAsImlhdCI6MTY0NzgwNTU1MCwicmF5IjoiYjIzMGE5YTZhYTAyOTI1MmY5ZmE0YzQ1ZGVlMDliZjkiLCJzdWIiOjk5Njk1MH0.QLv6oqP4_tZH8-GoX1mZ-b9jOn6gvHigSnKIIX5TOK6veEd-uYOri-gNc3qdwU_ZDv4-xr69Q_nH0UDmu9L7jUOfuG6MvnEUdg0XbzXOMiMd7wGd7_tiiK5LIFSBHaQXFhwDtuvOIL2b-hHK5G-PR_JdFbgIjFpuvRYfxsNgt3neqhmLjoqqOsxpiYrmDk8mAvEAYglyJzZE1jz2mNDKbWUK4tUPrXefvVqUCOxbayjDEcD9bv0nK0vz4hamVt-9SvLd-nJbB6Qlna5I-12sFBS4kw7FgWQnhoLTV35YNDff7-2EmxfX8Mrg5-o_oBt8QURRQ7j8AGoklhiP4B-fww';
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
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
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
        $token = 'eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE2NzkzNDE1NTAsImlhdCI6MTY0NzgwNTU1MCwicmF5IjoiYjIzMGE5YTZhYTAyOTI1MmY5ZmE0YzQ1ZGVlMDliZjkiLCJzdWIiOjk5Njk1MH0.QLv6oqP4_tZH8-GoX1mZ-b9jOn6gvHigSnKIIX5TOK6veEd-uYOri-gNc3qdwU_ZDv4-xr69Q_nH0UDmu9L7jUOfuG6MvnEUdg0XbzXOMiMd7wGd7_tiiK5LIFSBHaQXFhwDtuvOIL2b-hHK5G-PR_JdFbgIjFpuvRYfxsNgt3neqhmLjoqqOsxpiYrmDk8mAvEAYglyJzZE1jz2mNDKbWUK4tUPrXefvVqUCOxbayjDEcD9bv0nK0vz4hamVt-9SvLd-nJbB6Qlna5I-12sFBS4kw7FgWQnhoLTV35YNDff7-2EmxfX8Mrg5-o_oBt8QURRQ7j8AGoklhiP4B-fww';;
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
//        $result='{
//  "id": 11631253,
//  "created_at": "2018-10-13T08:13:38.809469028Z",
//  "phone": "+79000381454",
//  "product": "vkontakte",
//  "price": 21,
//  "status": "RECEIVED",
//  "expires": "2018-10-13T08:28:38.809469028Z",
//  "sms": [
//      {
//        "id":3027531,
//        "created_at":"2018-10-13T08:20:38.809469028Z",
//        "date":"2018-10-13T08:19:38Z",
//        "sender":"VKcom",
//        "text":"VK: 09363 - use this code to reclaim your suspended profile.",
//        "code":"09363"
//      }
//  ],
//  "forwarding": false,
//  "forwarding_number": "",
//  "country":"russia"
//}';
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
        $token = 'eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE2NzkzNDE1NTAsImlhdCI6MTY0NzgwNTU1MCwicmF5IjoiYjIzMGE5YTZhYTAyOTI1MmY5ZmE0YzQ1ZGVlMDliZjkiLCJzdWIiOjk5Njk1MH0.QLv6oqP4_tZH8-GoX1mZ-b9jOn6gvHigSnKIIX5TOK6veEd-uYOri-gNc3qdwU_ZDv4-xr69Q_nH0UDmu9L7jUOfuG6MvnEUdg0XbzXOMiMd7wGd7_tiiK5LIFSBHaQXFhwDtuvOIL2b-hHK5G-PR_JdFbgIjFpuvRYfxsNgt3neqhmLjoqqOsxpiYrmDk8mAvEAYglyJzZE1jz2mNDKbWUK4tUPrXefvVqUCOxbayjDEcD9bv0nK0vz4hamVt-9SvLd-nJbB6Qlna5I-12sFBS4kw7FgWQnhoLTV35YNDff7-2EmxfX8Mrg5-o_oBt8QURRQ7j8AGoklhiP4B-fww';
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
//        curl_close($ch);
//        $result='{
//  "id": 11631253,
//  "created_at": "2018-10-13T08:13:38.809469028Z",
//  "phone": "+79000381454",
//  "product": "vkontakte",
//  "price": 21,
//  "status": "FINISHED",
//  "expires": "2018-10-13T08:28:38.809469028Z",
//  "sms": [
//      {
//        "id":3027531,
//        "created_at":"2018-10-13T08:20:38.809469028Z",
//        "date":"2018-10-13T08:19:38Z",
//        "sender":"VKcom",
//        "text":"VK: 09363 - use this code to reclaim your suspended profile.",
//        "code":"09363"
//      }
//  ],
//  "forwarding": false,
//  "forwarding_number": "",
//  "country":"russia"
//}';
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
//    public function getPlayer($api,$id)
//    {
////        $apiKey="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI4NWYwNGNkMC05OThkLTAxM2EtNGNmZC0xNzdkOTFhMjYxNGEiLCJpc3MiOiJnYW1lbG9ja2VyIiwiaWF0IjoxNjQ5NDM4MTc2LCJwdWIiOiJibHVlaG9sZSIsInRpdGxlIjoicHViZyIsImFwcCI6Ii1iZWUxMTBkYS1kNjQyLTRiOTgtOTliNi0wNDY0Mjg3ZTRlODkifQ.JiPITHPdJHbp2pchkOY2hgdqv6Y6tgjRPGYYO8ievZs";
////        $region = "pc-as"; // choose platform and region
////        $players = "account.69a0587badc340f09a97771109eff2a8"; // choose a player (ign)
////        $headers = array(
////            'Authorization' => $apiKey,
////            'Accept' => 'application/vnd.api+json'
////        );
////        $getPlayer = Requests::get('https://api.playbattlegrounds.com/shards/'.$region.'/players?filter[playerIds]='.$players.'', $headers);
////        $getPlayerContent = json_decode($getPlayer->body, true);
////        $name = $getPlayerContent['data'][0]['attributes']['name'];
////        return $name;
//        $getPlayer = Http::post('https://as7abcard.com/pubg-files/pubg.php?action=getPlayerName&game=pubg&playerID=5262427733', ["ct"=>"ql18TgDgBmsvEu5aAJkypBwDgyHyjV8iJYJSmq1E4Kf9DS20PBpkjx3kDwrkPLc9v7o2NJ0LnrkVQNCwC0FQ+4/VaGKGdk60NOtd7ExY8zI=","iv"=>"0f4e33d8213109fa64a412cb07b2659d","s"=>"c5f09a65b90f316a"]);
//        return $getPlayer->body();
//    }

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
}
