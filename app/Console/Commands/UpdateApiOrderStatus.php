<?php

namespace App\Console\Commands;

use App\Models\GeneralSetting;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateApiOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateOrders:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update api orders status';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $updatableOrders=Order::select('id','api_order_id')->where('api_order',1)
            ->where('updated_at', '>=',now()->subMinutes(60)->toDateTimeString())->get();
        if (!$updatableOrders)
            die();
        $general = GeneralSetting::first();
        $arr = [
            'key' => $general->api_key,
            'action' => "status",
            'orders' => $updatableOrders->pluck('api_order_id')->implode(',')
        ];
        $response = json_decode(curlPostContent($general->api_url,$arr));
        foreach ($response as $id => $value)
        {
            Order::where('api_order_id',$id)->update(['status' => $this->setStatus($value->status)]);
        }
    return 'success';
    }

    public function setStatus($status)
    {
        if ($status == "In progress" )
            return 1;
        elseif ($status == "Completed" )
            return 2;
        elseif ($status == "Canceled" )
            return 3;
        else return 0;
    }
}
