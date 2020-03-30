<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Assett;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssettRequest;
use App\Http\Requests\UpdateAssettRequest;
use App\Http\Resources\Admin\AssettResource;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AssettsApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('assett_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new AssettResource(Assett::all());

    }

    public function store(StoreAssettRequest $request)
    {
        $assett = Assett::create($request->all());

        return (new AssettResource($assett))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);

    }

    public function show(Assett $assett)
    {
        abort_if(Gate::denies('assett_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new AssettResource($assett);

    }

    public function update(UpdateAssettRequest $request, Assett $assett)
    {
        $assett->update($request->all());

        return (new AssettResource($assett))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);

    }

    public function destroy(Assett $assett)
    {
        abort_if(Gate::denies('assett_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assett->delete();

        return response(null, Response::HTTP_NO_CONTENT);

    }
}
