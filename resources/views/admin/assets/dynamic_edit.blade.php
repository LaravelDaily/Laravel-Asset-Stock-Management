

    <div class="card-body" id="asset-edit-modal">
        <form method="POST" action="{{ route("admin.assets.update", [$asset->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.asset.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $asset->name) }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.asset.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="price_buy">Bought Price</label>
                <input class="form-control {{ $errors->has('price_buy') ? 'is-invalid' : '' }}" type="number" name="price_buy" id="price_buy" value="{{ old('price_buy', number_format($asset->price_buy, 2)) }}" required>
                @if($errors->has('price_buy'))
                    <div class="invalid-feedback">
                        {{ $errors->first('price_buy') }}
                    </div>
                @endif
                <span class="help-block"></span>
            </div>
            <div class="form-group">
                <label class="required" for="price_sell">Sell Price</label>
                <input class="form-control {{ $errors->has('price_sell') ? 'is-invalid' : '' }}" type="number" name="price_sell" id="price_sell" value="{{ old('price_sell', number_format($asset->price_sell, 2)) }}" required>
                @if($errors->has('price_sell'))
                    <div class="invalid-feedback">
                        {{ $errors->first('price_sell') }}
                    </div>
                @endif
                <span class="help-block"></span>
            </div>
            <div class="form-group">
                <label class="required" for="current_stock">Qty</label>
                <p>  <span class='btn btn-success' id="input-qty">
                        Add Qty
                     </span> </p>
                <span style="display: block;text-align: center;">
                <span class="btn btn-success stock-add">+</span>
                <input class="form-control {{ $errors->has('current_stock') ? 'is-invalid' : '' }}" type="number" name="current_stock" id="current_stock" value="{{ old('current_stock', $asset->getStock()) }}" required>
                <span class="btn btn-danger stock-min">-</span>
                </span>
                <div><span id='stock-original' class="font-weight-bold"></span></div>
                <div><span id='stock-estimate'></span></div>
                @if($errors->has('current_stock'))
                    <div class="invalid-feedback">
                        {{ $errors->first('current_stock') }}
                    </div>
                @endif
                <span class="help-block"></span>
            </div>
            <div class="form-group">
                <label class="required" for="danger_level">Danger level</label>
                <input class="form-control {{ $errors->has('danger_level') ? 'is-invalid' : '' }}" type="number" name="danger_level" id="danger_level" value="{{ old('danger_level', $asset->danger_level) }}" required>
                @if($errors->has('danger_level'))
                    <div class="invalid-feedback">
                        {{ $errors->first('danger_level') }}
                    </div>
                @endif
                <span class="help-block"></span>
            </div>
            <div class="form-group" style="display:none" >
                <button class="btn btn-danger" type="submit" id="save-data">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
        </div>
        <script>
            var formatter = new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP',
            });
            var oldQty=$("#asset-edit-modal #current_stock").val();
            console.log(oldQty);
            $(".stock-add").click(function(){
                var _val=($("#current_stock").val());
                $("#asset-edit-modal #current_stock").val(parseInt(_val)+1);
                UpdateEstimateCost();
            });
            $(".stock-min").click(function(){
                var _val=($("#current_stock").val());
                $("#asset-edit-modal #current_stock").val(parseInt(_val)- 1);
                UpdateEstimateCost();
            });
            $("#asset-edit-modal input[type='number']").click(function(){
                $(this).select();
            });
            $("#price_buy,#current_stock").change(function(){
                UpdateEstimateCost();
            });
            $("#input-qty").click (function(){
                var _stock =0;
                $_value=(prompt("Specify quantity to add", 0));
                if(Number.isInteger(parseInt($_value))){
                    _stock=parseInt($_value);
                }else{
                    alert("Please enter a number");
                }
            


                var _val=($("#current_stock").val());
                $("#asset-edit-modal #current_stock").val(parseInt(_val)+parseInt( _stock));
                UpdateEstimateCost();
                
            });
            $("#stock-original").html("Current Stock :"+$("#asset-edit-modal #current_stock").val());
 

        function UpdateEstimateCost(){
            
            var estimatecost=$("#price_buy").val();
            var newQty=$("#asset-edit-modal #current_stock").val();
            var diff=newQty-oldQty;
            if(diff==0){
                $("#stock-estimate").fadeOut();
            }else{
                $("#stock-estimate").fadeIn();
            }
            var cost=formatter.format(estimatecost*(newQty-oldQty));
            
            if(estimatecost*(newQty-oldQty)>0){
                cost="Estimated cost: "+cost;
                $("#stock-estimate").html(cost);
            }else if(estimatecost*(newQty-oldQty)<0){
                $("#stock-estimate").html("");
            }
           
            
        }
        

        

        </script>
 
