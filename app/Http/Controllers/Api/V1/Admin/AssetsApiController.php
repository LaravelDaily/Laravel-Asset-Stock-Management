<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Asset;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Http\Resources\Admin\AssetResource;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AssetsApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('asset_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new AssetResource(Asset::all());

    }

    public function store(StoreAssetRequest $request)
    {
        $asset = Asset::create($request->all());

        return (new AssetResource($asset))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);

    }

    public function show(Asset $asset)
    {
        abort_if(Gate::denies('asset_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new AssetResource($asset);

    }

    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        $asset->update($request->all());

        return (new AssetResource($asset))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);

    }

    public function destroy(Asset $asset)
    {
        abort_if(Gate::denies('asset_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $asset->delete();

        return response(null, Response::HTTP_NO_CONTENT);

    }
}
