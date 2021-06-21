@extends('layouts.admin')
@section('content')
<?php
use App\Branch;
$ddBranches=Branch::all();
?>
@if(session('params'))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
  <strong>Alert!</strong> {{session('params')['status']}}
  <br/>
  {{session('params')['reason']}}
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif

    <div style="margin-bottom: 10px;" class="row">
        <div class="col">
            <a class="btn btn-success add-order text-white"  data-url="{{ route('admin.dynamicOrder', 0) }}">
                {{ trans('global.add_order') }}
            </a>
        </div>

        <div class="col-lg-10">
        <form class="form-inline float-right" action="" method="GET">

            <!-- dateFrom -->
            <label class="sr-only" for="dateFrom">yyyy-mm-dd</label>
            <div class="input-group mr-sm-2">
                <div class="input-group-prepend">
                    <div class="input-group-text">Date From:</div>
                </div>
                <input type="text" class="form-control dateInputFormat" name="dateFrom" id="dateFrom" placeholder="yyyy-mm-dd">
            </div>

            <!-- dateTo -->
            <label class="sr-only" for="dateTo">yyyy-mm-dd</label>
            <div class="input-group mr-sm-2">
                <div class="input-group-prepend">
                    <div class="input-group-text">Date To:</div>
                </div>
                <input type="text" class="form-control dateInputFormat" name="dateTo" id="dateTo" placeholder="yyyy-mm-dd">
            </div>

            <select name="branchID" class="form-control mr-sm-2">
                <option>Select Branch</option>
                @foreach($ddBranches as $key => $branch)
                <option value="{{ $branch->id }}">{{ $branch->name ?? ''}}</option>
                @endforeach
            </select>

            <select name="ddStatus" class="form-control mr-sm-2">
                <option value="All">Select Status</option>
                <option value="Open">Open</option>
                <option value="Processed">Processed</option>
            </select>

            <button type="submit" class="btn btn-warning text-black">{{ trans('global.filter') }}</button>
        </form>
    </div>
    </div>

<div id="view-modal" class="modal fade" data-modal-index="1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
<form id="frmOrder" method="POST" action="{{ route("admin.orders.store") }}" enctype="multipart/form-data">

@csrf
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Order</h4>
                <input type="hidden" id="hidOrderID" name="order_id">
                <input type="hidden" id="hidStatus" name="order_status">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                </button>
            </div>
            <div class="modal-body" style="padding-bottom:0;padding-top:6px">

                   <div id="modal-loader"
                        style="display: none; text-align: center;">
                    <img src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mobile/1.4.5/images/ajax-loader.gif">
                   </div>

                   <!-- content will be load here -->
                   <div id="dynamic-content"></div>


            </div>
            <div class="modal-footer">
                <!-- <p id="bProcessOrder"  class="btn btn-default">{{ trans('global.process_order') }}</p> -->
                <button type="button" style="padding:8px" class="close" data-dismiss="modal" aria-hidden="true">DONE
                </button>               
            </div>

         </div>
         
    </div>
</form>
</div><!-- /.modal -->

<div class="modal fade" id="asset-qty" data-modal-index="2">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
         <h4 class="modal-title">Please enter quantity</h4>
         <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

      </div>
      <div class="modal-body">
      <p class="info"><span class="name"></span></span>&nbsp; (Remaining stock:<span class="amount">Checking....</span>)</p>
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
                            {{ trans('cruds.order.fields.branch_order_code') }}
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
                                {{ $order->branch_order_code ?? 'BR#-########' }}
                            </td>
                            <td>
                                {{ Branch::find($order->branch_id)->name?? '' }}
                            </td>
                            <td>
                                {{ $order->created_at ?? '' }}
                            </td>
                            <td>
                                 {{ $order->total_price ?? ''}}
                            </td>
                            <td>
                              {{ $order->status ?? '' }}
                            </td>
                            <td class="no-print">

                            <a class="btn btn-xs btn-primary text-white view-order"  data-url="{{ route('admin.dynamicOrder', $order->id ) }}">
                                 View Transaction
                             </a>
                             @if($order->status=="Open")
                             <a class="btn btn-xs btn-primary text-white"   href="{{ route("admin.processOrder",$order->id   ) }}">
                                 Process
                             </a>
                             @endif
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
@can('order_delete')
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

$("#dateFrom").datepicker({ dateFormat: "yy-mm-dd" });
$("#dateTo").datepicker({ dateFormat: "yy-mm-dd" });


</script>
@endsection
