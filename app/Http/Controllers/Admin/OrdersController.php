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
    public function index()
    {
        $orders = Order::all();

        return view('admin.orders.index', compact('orders'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
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
        return view('admin.orders.dynamic_order', ['order'=> Order::find($id)]);
    }
    public function getBranches()
    {
        return Branch::all();
    }
    public function ajaxRequestPost(Request $request)
    {
        $input = $request->all();
        if ($input['action']=="saveOrder") {
            return $this->saveOrder(json_decode($input['order']));
        }
        return response()->json(['success'=>'Got Simple Ajax Request.']);
    }
    public function saveOrder($order)
    {
        $asset=Asset::find($order->assetToAdd);
        if($asset->getStock()<$order->assetQty){
            return response()->json(['fail'=>'Not Enough Stock']);
        }
      
        if (!isset($order->id)) {
            $_order=new Order;
            $_order->branch_id=$order->branch_id;
            $_order->total_price=0;
            $_order->order_date=date("Y-m-d h:i:s");
            $_order->save();
            $_order->addOrderDetail($order->assetToAdd, $order->assetQty);
            $_order->itemList=$_order->getOrderDetails();
            return response()->json(['success'=>'Added item!','order'=>$_order]);
        }else{
            $_order=Order::find($order->id);
            $_order->addOrderDetail($order->assetToAdd, $order->assetQty);
            $_order->itemList=$_order->getOrderDetails();
            return response()->json(['success'=>'Added item!','order'=>$_order]);
        }
       
        
    }
}
