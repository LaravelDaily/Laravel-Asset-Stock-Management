<?php

namespace App\Http\Requests;

use App\Assett;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyAssettRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('assett_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;

    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:assetts,id',
        ];

    }
}
