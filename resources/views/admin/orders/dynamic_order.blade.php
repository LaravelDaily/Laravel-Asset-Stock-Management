<?php
use App\Branch;
use App\Asset;
use App\Order;

$branches = Branch::all();
$assets=Asset::all();
$assetNames=[];
foreach ($assets as $asset) {
    $_asset=new stdClass();
    $_asset->label=$asset->name;
    $_asset->id=$asset->id;
    array_push($assetNames, $_asset);
}
?>
<div class="card-body" id="order-modal">
        <form method="POST" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label style="display:block"  for="branch">Branch</label>
                <select id="order-branch" class="form-select">

                    <option <?php if(!isset($order)){echo 'selected';}?> value="0">{{ trans('global.select_branch') }}</option>
                    @foreach($branches as $key => $branch)
                    <option value="{{ $branch->id }}" <?php if(isset($order)){if($branch->id==$order->branch_id){echo 'selected';}}?>>{{ $branch->name }}</option>
                    @endforeach
                </select>
                <span class="help-block"></span>
            </div>
            <div class="form-group">
                <label  for="asset_name">{{ trans('cruds.order.fields.asset_name') }}</label>
                <input class="form-control" data-selected-id=0 type="text" id="asset_name" value="" >
                <span class="btn btn-success add-asset"  tabindex="0">ADD</span>
            </div>
           
            <div id="custom-search-input">
						<div class="input-group"  style="margin-bottom:8px">
							<input id="search" type="text" class="form-control input-lg" placeholder="Search" />
							<i class="fa fa-search" style="display: block;
                                 position: absolute;
                                 right: 3%;
                                 margin-top: 8px;"></i>
						</div>
						<ul class="card list-group" style="padding:8px;min-height:240px;max-height:240px;margin-bottom:0;overflow-y:auto">
							<li class="list-group-item asset-item ignore-clone" style="display:none"><span class="assetname">Cras justo odio</span><span class="qty" >0</span></li>
						</ul>
			</div>
        </form>
</div>
<script>
var assetNames=<?php echo json_encode($assetNames);?>;

@if(isset($order))
$("#view-modal .modal-title").html("Order #"+<?php echo $order->id;?>);
@endif
@if(!isset($order))
$("#view-modal .modal-title").html("Add Order");
@endif
var autocompleteAsset=$( "#asset_name" ).autocomplete({
      source: assetNames,
      select: function(event,ui){
          $("#asset_name").val(ui.item.label);
          $("#asset_name").attr("data-selected-id",ui.item.id);
          $(".asset-name-autocomplete .ui-menu-item").remove();
            $(".add-asset").focus();
            $(".add-asset").select();
        //  console.log("ID:"+ui.item.id);
      },
      classes: {
        "ui-autocomplete": "asset-name-autocomplete",
      }
      
});
$(".ui-autocomplete").css("z-index",9999);
$("#order-modal").keypress(
  function(event){
    if (event.which == '13') {
      event.preventDefault();
    }
    if (event.key == 'Esc') {
      event.preventDefault();
    }
});
$("#asset-qty").keypress(
  function(event){
    if (event.which == '13') {
      event.preventDefault();
      confirmQty();
    }
});
$(".add-asset").keypress(
  function(event){
    if (event.which == '13') {
      event.preventDefault();
      $(".add-asset").click();
    }
});
$("#asset_name").keypress(
  function(event){
    if (event.which == '13') {
      if($(".asset-name-autocomplete").find(".ui-menu-item:first-child").length){
      $("#asset_name").val($(".asset-name-autocomplete").find(".ui-menu-item:first-child div").html());
      $(".asset-name-autocomplete").find(".ui-menu-item:first-child div").click();
      $(".add-asset").focus();
      $(".add-asset").select();
      }
    }else{
    $("#asset_name").attr("data-selected-id",0);
    }
});

$('.add-asset').on('click', function(){
  if($("#order-branch option:selected").val()==0){
      alert("Please select a branch");
      $("#order-branch").focus();
      return;
  }
  if($("#asset_name").attr("data-selected-id")==0){
      if($(".asset-name-autocomplete").find(".ui-menu-item:first-child").length){
          if(confirm("Do you mean '"+$(".asset-name-autocomplete").find(".ui-menu-item:first-child div").html()+"'?")){
             $("#asset_name").val($(".asset-name-autocomplete").find(".ui-menu-item:first-child div").html());
             $(".asset-name-autocomplete").find(".ui-menu-item:first-child div").click();
          }else{
              return;
          }
      }else{
        alert("Not a valid item");
        return;
      }
  }
  var $btn = $(this);
  $("#asset-qty").modal("show");
  $("#asset-qty").focus();
  $("#asset-qty").click();
  var currentDialog = $btn.closest('.modal-dialog'),
  targetDialog = $("#asset-qty");
  if (!currentDialog.length){
    return;
  }
  getRemainingQty();
  targetDialog.data('previous-dialog', currentDialog);
  currentDialog.addClass('aside');
  var stackedDialogCount = $('.modal.in .modal-dialog.aside').length;
  if (stackedDialogCount <= 5){
    currentDialog.addClass('aside-' + stackedDialogCount);
  }
});
$('.modal').on('hide.bs.modal', function(){
  var $dialog = $(this);  
  var previousDialog = $dialog.data('previous-dialog');
  if (previousDialog){
    previousDialog.removeClass('aside');
    $dialog.data('previous-dialog', undefined);
  }
});
$('#asset-qty').on('hide.bs.modal', function(){
  resetAssetName();
  IS_EDITING=false;
});


$('#view-modal').on('shown.bs.modal', function(){
    $(this).find("#order-branch").focus();
    $(this).find("#order-branch").select();
});
$('#view-modal').on('hide.bs.modal', function(){
    location.reload();
});
$('#asset-qty').on('shown.bs.modal', function(){
    $(this).find("input").focus();
    $(this).find("input").select();    
    $("#asset-qty #confirm-qty").one("click",confirmQty);

});
$.ajaxSetup({
headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
});

//ORDER OBJ
var order=(<?php 
            if(isset($order)){
              echo ($order);
            }else{
              echo json_encode(new stdClass());
            }
          ?>);
if(order!=null){
order.itemList=(<?php
   
   if(isset($order)){
       echo($order->getOrderDetails());
   }else{
     echo "[]";
   }
   
   
   ?>);
}
updateItemList();

var IS_EDITING=false;

var failMessage=false;
function getRemainingQty(){
  $("#asset-qty .info").html("Checking stock information...");
  var assetToAdd=$("#asset_name").attr("data-selected-id");
  $.ajax({
    type:'POST',
    url:'ajaxRequest',
    data:{action:"getAssetInfo",assetId:assetToAdd},
    success:function(data){
    
      if(data.success!=null){
        $("#asset-qty .info").html(data.asset.name+"  (remaining stock: "+data.asset.currentStock+" )");
      }else{
        $("#asset-qty .info").html("Item not found!");      
      }
      }

  
  });
}
var savingOrder=null;
function confirmQty(){
    if(savingOrder!=null){
      return;
    }

    if(order==null){
        order={};
    }
    order.branch_id=$("#order-branch").val();
    order.assetToAdd=$("#asset_name").attr("data-selected-id");
    order.assetQty=parseInt($("#asset-qty input").val());
    if(order.assetQty.toString().indexOf("e") >= 0){
        alert("Input is invalid");
        return;
    }
    if(order.assetQty.toString().indexOf("-") !=-1){
        alert("Input is invalid");
        return;
    }
    if(order.assetQty.toString().indexOf("+") >= 0){
        alert("Input is invalid");
        return;
    }
    if(order.assetQty.toString().indexOf(".") >= 0){
        alert("Input is invalid");
        return;
    }

    if(checkIfAssetExisting(order.assetToAdd) && !IS_EDITING){
      order.assetQty+=parseInt(getAssetExisting(order.assetToAdd).quantity);
    }

    if(order.assetQty<=0 ||$("#asset-qty input").val()=="" ){
        alert("Please input quantity greater than 0");
        return;
    }
    if(order.assetQty>9999){
        alert("Please input quantity less than 9999");
        return;
    }
   
    savingOrder=$.ajax({
    type:'POST',
    url:'ajaxRequest',
    data:{action:"saveOrder",order:JSON.stringify(order)},
    success:function(data){
      if(data.success!=null){
     //  alert(data.success);
       fillOrder(data.order);
       $("#asset-qty").modal('hide');
       resetAssetName();
       updateItemList();
      }else{
        if(!failMessage){
        failMessage=true;
        alert(data.fail);
        $("#asset-qty #confirm-qty").one("click",confirmQty);
        }else{
          failMessage=false;
        }
      }
      savingOrder=null;
    }
  });
} 
function  fillOrder(_order){
  order.id=_order.id;
  $("#view-modal .modal-title").html("Order #"+order.id+"");
  order.branch_id=_order.branch_id;
  order.itemList=_order.itemList;
  order.total_price=_order.total_price;
}
function resetAssetName(){
  $("#asset_name").val("");
  $("#asset_name").focus();
  $("#asset_name").attr('data-selected-id',0);
  $("#asset-qty input").val(1);
}

function updateItemList(){
  var container=$("#custom-search-input .list-group");
  var _itemList=order.itemList;
  if(_itemList==null){
      return;
  }
  for (index = 0; index < _itemList.length; ++index) {
    var assetId=_itemList[index]._asset.id;
    var element=$(container).find(".asset-item[data-asset-id='"+assetId+"']");
    if(element.length){
      //found,update,prepend
      if(_itemList[index].quantity!=parseInt($(element).find(".qty").html())){
      var updateElement=$(element).clone();
      $(newElement).removeClass("ignore-clone");
      $(updateElement).find(".qty").html(_itemList[index].quantity);
      $(element).remove();
      $(container).prepend(updateElement);
      }
    }else{
      var newElement=$(".asset-item.ignore-clone").clone();
      $(newElement).removeClass("ignore-clone");
      $(newElement).find(".assetname").html(_itemList[index]._asset.name);
      $(newElement).find(".qty").html(_itemList[index].quantity);
      $(newElement).attr("data-asset-id",_itemList[index]._asset.id);
      $(container).prepend(newElement);
      $(newElement).fadeIn("slow");
    }
    $(".asset-item:not(.ignore-clone)").css("opacity",1); 
  }

  $(".asset-item .qty").click(function(){
  var _id=$(this).closest(".asset-item").attr('data-asset-id');
  $("#asset_name").attr('data-selected-id',_id);
  IS_EDITING=true;
  $('.add-asset').click();
  $("#asset-qty input").val(parseInt($(this).html()));
  });


}

function checkIfAssetExisting(assetId){
  if(order.itemList==null){
    return false;
  }
  var container=$("#custom-search-input .list-group");
  var _itemList=order.itemList;
  for (index = 0; index < _itemList.length; ++index) {
    if(_itemList[index]._asset.id==assetId){
      return true;
    }
  }
  return false;
}
function getAssetExisting(assetId){
  if(order.itemList==null){
    alert("NO ");
    return false;
  }
  var container=$("#custom-search-input .list-group");
  var _itemList=order.itemList;
  for (index = 0; index < _itemList.length; ++index) {
    if(_itemList[index]._asset.id==assetId){
      return _itemList[index];
    }
  }
}

$("#view-modal").ready(function() {
  $("#order-branch").val(<?php 
    if (isset($order)) {
        echo $order->branch_id;
    }else{
      echo 0;
    }
    ?>);
});
$('#order-modal #search').keyup(function(){	
		var current_query = $('#search').val().toLowerCase();
		if (current_query !== "") {
			$(".list-group .asset-item:not(.ignore-clone)").hide();
			$(".list-group .asset-item:not(.ignore-clone)").each(function(){
				var current_keyword = $(this).text().toLowerCase();
				if (current_keyword.indexOf(current_query) >=0) {
					$(this).show();    	 	
				};
			});    	
		} else {
			$(".list-group .asset-item:not(.ignore-clone)").show();
		};
	});

</script>
