<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class StoreStockRequest
 * @package App\Http\Requests
 */
class StoreStockRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        abort_if(Gate::denies('stock_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'asset_id'      => [
                'required',
                'integer',
                Rule::unique('stocks')
                    ->where('asset_id', request()->input('asset_id'))
                    ->where('team_id', auth()->user()->team_id)
                    ->whereNull('deleted_at'),
            ],
            'current_stock' => [
                'nullable',
                'integer',
            ],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'asset_id.unique' => 'The asset is in stock already.',
        ];
    }
}
