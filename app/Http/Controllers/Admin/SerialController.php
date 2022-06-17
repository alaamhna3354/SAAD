<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Serial;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SerialController extends Controller
{
    public function index()
    {
        $page_title = 'Serials';
        $empty_message = 'No Result Found';
       $categories = Category::active()->orderBy('name')->get();
        $services = Service::active()->orderBy('name')->get();
        $serials = Serial::with('service')->latest()->paginate(getPaginate());
        return view('admin.serials.index', compact('page_title', 'services', 'empty_message', 'serials','categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service' => 'required|integer',
            'code' => 'required|string|max:191',
        ]);
        $codes = explode("\r\n", $request['code']);

        foreach ($codes as $code) {
            $serial = new Serial();
            $serial->service_id = $request->service;
            $serial->code = $code;
            $serial->details = $request->details;
            $serial->save();
        }
        $notify[] = ['success', 'Serial added!'];
        return back()->withNotify($notify);

    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'service' => 'required|integer',
            'code' => 'required|string|max:191',
        ]);
        $serial = Serial::findOrFail($id);
        $serial->service_id = $request->service;
        $serial->code = $request->code;
        $serial->details = $request->details;
        $serial->save();
        $notify[] = ['success', 'Serial updated!'];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        $serial = Serial::findOrFail($id);
        $serial->status = ($serial->status ? 0 : 1);
        $serial->save();

        $notify[] = ['success', 'Status updated!'];
        return back()->withNotify($notify);
    }

    public function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
