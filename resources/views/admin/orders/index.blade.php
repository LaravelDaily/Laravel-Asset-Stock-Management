@extends('layouts.admin')
@section('content')
<?php
use App\Branch;
?>
@can('asset_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success add-order"  data-url="{{ route('admin.dynamicOrder', 0) }}">
                {{ trans('global.add_order') }} 
            </a>
        </div>
    </div>
@endcan


<div id="view-modal" class="modal fade"   data-modal-index="1"
    tabindex="-1" role="dialog" 
    aria-labelledby="myModalLabel" 
    aria-hidden="true" style="display: none;">
     <div class="modal-dialog modal-dialog-centered"> 
          <div class="modal-content"> 

               <div class="modal-header"> 
                    <h4 class="modal-title">
                     Add Order
                    </h4> 
                    <button type="button" class="close" 
                        data-dismiss="modal" 
                        aria-hidden="true">
                        ×
                     </button> 
               </div> 
               <div class="modal-body"> 

                   <div id="modal-loader" 
                        style="display: none; text-align: center;">
                    <img src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mobile/1.4.5/images/ajax-loader.gif">
                   </div>

                   <!-- content will be load here -->                          
                   <div id="dynamic-content"></div>

                </div> 
                <div class="modal-footer"> 
                      <button type="button" 
                          class="btn btn-default">
                          {{ trans('global.process_order') }}
                      </button>  
                </div> 

         </div> 
      </div>
</div><!-- /.modal -->   
<div class="modal fade" id="asset-qty" data-modal-index="2">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
         <h4 class="modal-title">Please enter quantity</h4>
         <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
       
      </div>
      <div class="modal-body">
      <p class="info">Remaining:</p>
      <div class="form-group">
            <input class="form-control" type="number" max="9999" value="1" >
      </div>
      <div class="form-group">
            <span id="confirm-qty" class="btn btn-success w-100">CONFIRM (Press Enter)</span>
      </div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<div class="card">
    <div class="card-header">
        {{ trans('global.order') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Order">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.order.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.branch.title_singular') }}
                        </th>
                        <th>
                            {{ trans('cruds.order.fields.created_at') }}  
                        </th>
                        <th>
                            {{ trans('cruds.order.fields.total_price') }}  
                        </th>
                        <th>
                            {{ trans('cruds.order.fields.status') }}  
                        </th>
                        <th class="no-print">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $key => $order)
                        <tr class="order-item" data-entry-id="{{ $order->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $order->id ?? '' }}
                            </td>
                            <td>
                                {{ Branch::find($order->branch_id)->name?? '' }}
                            </td>
                            <td>
                                {{ $order->created_at ?? '' }}
                            </td>
                            <td>
                                 {{ $order->getTotalPrice() ?? '' }}
                            </td>
                            <td>
                              {{ $order->status ?? '' }}
                            </td>
                            <td class="no-print">
                             
                            <a class="btn btn-xs btn-primary view-order"  data-url="{{ route('admin.dynamicOrder', $order->id ) }}">
                                 View Transaction
                             </a>

                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>



@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('asset_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.orders.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  $.extend(true, $.fn.dataTable.defaults, {
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  $('.datatable-Order:not(.ajaxTable)').DataTable({ buttons: dtButtons })
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
})

$(".add-order,.view-order").click(function(){
 var url = $(this).data('url');
 var hide=false;
 if(!$(this).closest(".order-item").length){
 
 if (!confirm('{{ trans('global.confirm_create_order') }}')) {
    hide=true;
    $("#view-modal").modal("hide");
    return;
 }else{
    $("#view-modal").modal("show");
 } 
 }else{
    $("#view-modal").modal("show");
 } 
  $("#view-modal").fadeIn();
  $('#dynamic-content').html(''); // leave it blank before ajax call
  $('#modal-loader').show();      // load ajax loader
  $.ajax({
            url: url,
            type: 'GET',
            dataType: 'html'
       })
    .done(function(data){
            $('#dynamic-content').html('');    
            $('#dynamic-content').html(data); // load response 
            $('#modal-loader').hide();        // hide ajax loader  
            $("#view-modal .modal-footer button").click(function(){
            $("#view-modal #save-data").click();
            }); 
        })
    .fail(function(){
            $('#dynamic-content').html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
            $('#modal-loader').hide();
    });
});



</script>
@endsection