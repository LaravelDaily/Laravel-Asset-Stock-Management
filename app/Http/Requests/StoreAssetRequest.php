<?php

namespace App\Http\Requests;

use App\Asset;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class StoreAssetRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('asset_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;

    }

    public function rules()
    {
        return [
            'name'         => 'required',
            'danger_level' => 'required|integer',
        ];

    }
}
