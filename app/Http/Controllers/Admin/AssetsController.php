<?php

namespace App\Http\Controllers\Admin;

use App\Asset;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyAssetRequest;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AssetsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('asset_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assets = Asset::all();

        return view('admin.assets.index', compact('assets'));
    }

    public function create()
    {
        abort_if(Gate::denies('asset_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.assets.create');
    }

    public function store(StoreAssetRequest $request)
    {
        $asset = Asset::create($request->all());
        $asset->updateStock($request->all()['current_stock']);
        return redirect()->route('admin.assets.index');

    }

    public function edit(Asset $asset)
    {
        abort_if(Gate::denies('asset_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.assets.edit', compact('asset'));
    }

    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        
        
        if ($request->all()['current_stock']<0) {
            return redirect()->route('admin.assets.index')->with([
                'error' => 'Cannot update stock.',
            ]);
        }
        if (isset($request->all()['current_stock_update'])) {
            $request->all()['current_stock']=intval($request->all()['current_stock_update']);
        }
        $asset->update($request->all());
        $asset->updateStock($request->all()['current_stock']+$request->all()['current_stock_update']);
        return redirect()->route('admin.assets.index');

    }

    public function show(Asset $asset)
    {
        abort_if(Gate::denies('asset_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.assets.show', compact('asset'));
    }

    public function destroy(Asset $asset)
    {
        abort_if(Gate::denies('asset_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $asset->delete();

        return back();

    }

    public function massDestroy(MassDestroyAssetRequest $request)
    {
        Asset::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);

    }
    public function loadInformation($id){
        return view('admin.assets.dynamic_edit',['asset'=> Asset::find($id)]);
    }
}
