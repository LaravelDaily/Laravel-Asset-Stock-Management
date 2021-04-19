<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyRoleRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Permission;
use App\Order;
use App\Asset;
use App\Branch;
use App\Custom\TechKen;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    
    public function index(Request $request)
    {
        //dd($request->all());

        if (!count($request->all())>0) {
            $orders = Order::all();
        } else {
            $qry = Order::query();

            if ($request->has('dateFrom') && $request->dateFrom != null && $request->has('dateTo') && $request->dateTo != null) {
                $ds = Carbon::createFromFormat('Y-m-d', $request->dateFrom);
                $de = Carbon::createFromFormat('Y-m-d', $request->dateTo);

                $qry->WhereBetween('updated_at', [$ds->startOfDay(), $de->endOfDay()]);
            }

            if ($request->has('branchID')) {
                if ($request->branchID != 'Select Branch') {
                    $qry->where('branch_id', (int) $request->branchID);
                }
            }

            if ($request->has('ddStatus')) {
                if ($request->ddStatus != 'All') {
                    $qry->where('status', $request->ddStatus);
                }
            }


            $orders = $qry->get();
            //dd($orders);
        }

        $ddBranches = Branch::all();

        return view('admin.orders.index', compact('orders', 'ddBranches'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        $params=$request->all();
        $orders = Order::findOrFail($params['order_id']);
        $orders->branch_id=$params['branch_id'];
        $orders->total_price=$params['total_price'];
        $orders->status="Processed";
        $reasonProcess=$this->updateInventory($orders->getOrderDetails());
        if ($reasonProcess=="true") {
            $orders->save();

            // NOTIF
            TechKen::AddNotification("Low Stock");

            $orders = Order::all();
            return view('admin.orders.index', compact('orders'));
        } else {
            // NOTIF
            TechKen::AddNotification("Low Stock");

            $orders = Order::all();
            return redirect('admin/orders')->with("params", ["orders"=>$orders,"status"=>"Cannot Process this Order","reason"=>$reasonProcess]);
        }
    }
    public function processOrder($orderId)
    {
       
        $reasonProcess="";
        $orders = Order::findOrFail($orderId);
        $orders->status="Processed";
        $reasonProcess=$this->updateInventory($orders->getOrderDetails());
        if ($reasonProcess=="true") {
            $orders->save();
            TechKen::AddNotification("Low Stock");
            $orders = Order::all();
            return redirect('admin/orders')->with("params", ["orders"=>$orders,"status"=>"Cannot Process this Order","reason"=>$reasonProcess]);
        } else {
            $orders = Order::all();
            TechKen::AddNotification("Low Stock");

            return redirect('admin/orders')->with("params", ["orders"=>$orders,"status"=>"Cannot Process this Order","reason"=>$reasonProcess]);
        }
    }

    public function updateInventory($order_details)
    {
        foreach ($order_details as $item) {
            $_asset=Asset::find($item->asset_id);
            if (isset($_asset)) {
                if ($_asset->getStock()<$item->quantity) {
                    return "Some items are low on stock.";
                }
                if (isset($_asset->deleted_at)) {
                    return "Cannot process deleted items.";
                }

            } else {
                    return "Cannot process deleted items.";
            }
        }
        foreach ($order_details as $item) {
            $_asset=Asset::find($item->asset_id);
            if (isset($_asset)) {
                $_asset->updateStock($_asset->getStock()-$item->quantity);
            } else {
                //Todo create logs here
            }
        }
        return "true";
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function loadInformation($id)
    {
        return view('admin.orders.dynamic_order', ['order' => Order::find($id)]);
    }
    public function getBranches()
    {
        return Branch::all();
    }
    public function ajaxRequestPost(Request $request)
    {
        $input = $request->all();
        if ($input['action'] == "saveOrder") {
            return $this->saveOrder(json_decode($input['order']));
        }
        if ($input['action'] == "getAssetInfo") {
            return $this->checkAssetInfo(($input['assetId']));
        }
        if ($input['action']=="removeOrder") {
            return $this->removeOrder($input['params']);
        }

        return response()->json(['success' => 'No action']);
    }
    public function checkAssetInfo($assetId)
    {
        $asset = Asset::find($assetId);
        if (isset($asset)) {
            $asset->currentStock = $asset->getStock();
            return response()->json(['success' => true, 'asset' => $asset]);
        }
    }
    public function removeOrder($params)
    {
        $params=(json_decode($params));
        $_order = Order::find($params->orderId);
        if ($_order->removeOrderDetail($params->assetId)) {
            $_order->total_price=$_order->getTotalPrice();
            $_order->save();
            return response()->json(['success' => 'Removed item!', 'itemList' =>  $_order->getOrderDetails()]);
        } else {
            return response()->json(['fail' => 'Cannot remove an item. Contact developers']);
        }
    }
    public function saveOrder($order)
    {
        $asset=Asset::find($order->assetToAdd);
        if ($asset->getStock()<$order->assetQty) {
            return response()->json(['fail'=>'Not Enough Stock']);
        }

        if (!isset($order->id)) {
            $_order = new Order;
            $_order->branch_id = $order->branch_id;
            $_order->order_date = date("Y-m-d h:i:s");
            $_order->branch_id=$order->branch_id;
            $_order->total_price=0;
            $_order->save();
            $_order->addOrderDetail($order->assetToAdd, $order->assetQty);
            $_order->total_price=$_order->getTotalPrice();
            $_order->save();
            $_order->itemList = $_order->getOrderDetails();
            return response()->json(['success' => 'Added item!', 'order' => $_order, 'addedOrder' => true]);
        } else {
            $_order = Order::find($order->id);
            if (isset($order->total_price)) {
                $_order->total_price=$order->total_price; //totalPrice
            }
            $_order->branch_id=$order->branch_id; //Branch updating
            $_order->save();
            $_order->addOrderDetail($order->assetToAdd, $order->assetQty);
            $_order->total_price=$_order->getTotalPrice();
            $_order->save();
            $_order->itemList = $_order->getOrderDetails();
            return response()->json(['success' => 'Added item!', 'order' => $_order]);
        }
    }
}
