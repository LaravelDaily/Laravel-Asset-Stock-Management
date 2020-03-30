<?php

namespace App\Http\Controllers\Admin;

use App\Assett;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyAssettRequest;
use App\Http\Requests\StoreAssettRequest;
use App\Http\Requests\UpdateAssettRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AssettsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('assett_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assetts = Assett::all();

        return view('admin.assetts.index', compact('assetts'));
    }

    public function create()
    {
        abort_if(Gate::denies('assett_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.assetts.create');
    }

    public function store(StoreAssettRequest $request)
    {
        $assett = Assett::create($request->all());

        return redirect()->route('admin.assetts.index');

    }

    public function edit(Assett $assett)
    {
        abort_if(Gate::denies('assett_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.assetts.edit', compact('assett'));
    }

    public function update(UpdateAssettRequest $request, Assett $assett)
    {
        $assett->update($request->all());

        return redirect()->route('admin.assetts.index');

    }

    public function show(Assett $assett)
    {
        abort_if(Gate::denies('assett_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.assetts.show', compact('assett'));
    }

    public function destroy(Assett $assett)
    {
        abort_if(Gate::denies('assett_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $assett->delete();

        return back();

    }

    public function massDestroy(MassDestroyAssettRequest $request)
    {
        Assett::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);

    }
}
