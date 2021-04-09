@extends('layouts.admin')
@section('content')

<div class="card" id="asset-edit-modal">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.asset.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.assets.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.asset.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                @if($errors->has('name'))
                <div class="invalid-feedback">
                    {{ $errors->first('name') }}
                </div>
                @endif
                <span class="help-block">{{ trans('cruds.asset.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="price_buy">Bought Price</label>
                <input class="form-control {{ $errors->has('price_buy') ? 'is-invalid' : '' }}" type="number" name="price_buy" id="price_buy" value="{{ old('price_buy', '0.00') }}" min="0.00" max="100000.00" step="0.25" placeholder="0.00" required>
                @if($errors->has('price_buy'))
                <div class="invalid-feedback">
                    {{ $errors->first('price_buy') }}
                </div>
                @endif
                <span class="help-block"></span>
            </div>
            <div class="form-group">
                <label class="required" for="price_sell">Sell Price</label>
                <input class="form-control {{ $errors->has('price_sell') ? 'is-invalid' : '' }}" type="number" name="price_sell" id="price_sell" value="{{ old('price_sell', '0.00') }}" min="0.00" max="100000.00" step="0.25" placeholder="0.00" required>
                @if($errors->has('price_sell'))
                <div class="invalid-feedback">
                    {{ $errors->first('price_sell') }}
                </div>
                @endif
                <span class="help-block"></span>
            </div>
            <div class="form-group">
                <label class="required" for="current_stock">Qty</label>
                <span style="display: block;text-align: center;">
                <span class="btn btn-success stock-add">+</span>
                <input class="form-control {{ $errors->has('current_stock') ? 'is-invalid' : '' }}" type="number" name="current_stock" id="current_stock" value="{{ old('current_stock', 0) }}" required>
                <span class="btn btn-danger stock-min">-</span>
                </span>
                @if($errors->has('current_stock'))
                    <div class="invalid-feedback">
                        {{ $errors->first('current_stock') }}
                    </div>
                @endif
                <span class="help-block"></span>
            </div>
            <div class="form-group">
                <label class="required" for="danger_level">Danger level</label>
                <input class="form-control {{ $errors->has('danger_level') ? 'is-invalid' : '' }}" type="number" name="danger_level" id="danger_level" value="{{ old('danger_level', 0) }}" required>
                @if($errors->has('danger_level'))
                <div class="invalid-feedback">
                    {{ $errors->first('danger_level') }}
                </div>
                @endif
                <span class="help-block"></span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
@section('scripts')
@parent
<script>
    $(".stock-add").click(function(){
        var _val=($("#current_stock").val());
    $("#asset-edit-modal #current_stock").val(parseInt(_val)+1);
    });
    $(".stock-min").click(function(){
        var _val=($("#current_stock").val());
         $("#asset-edit-modal #current_stock").val(parseInt(_val)- 1);
    });
    $("#asset-edit-modal input[type='number']").click(function(){
        $(this).select();
    });
    $('#name').select(); 
 </script>
@endsection