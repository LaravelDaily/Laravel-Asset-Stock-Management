@extends('layouts.admin')
@section('content')
@can('asset_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.assets.create") }}">
                {{ trans('global.add') }} {{ trans('cruds.asset.title_singular') }}
            </a>
        </div>
    </div>
@endcan

<div id="view-modal" class="modal fade"  
    tabindex="-1" role="dialog" 
    aria-labelledby="myModalLabel" 
    aria-hidden="true" style="display: none;">
     <div class="modal-dialog"> 
          <div class="modal-content"> 

               <div class="modal-header"> 
                    <h4 class="modal-title">
                     Stock Information
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
                          {{ trans('global.save') }}
                      </button>  
                </div> 

         </div> 
      </div>
</div><!-- /.modal -->   


<div class="card">
    <div class="card-header">
        {{ trans('cruds.asset.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Asset">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.asset.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.asset.fields.name') }}
                        </th>
                        <th>
                            Bought Price
                        </th>
                        <th>
                            Sell Price
                        </th>
                        <th>
                           Current Stock
                        </th>
                        <th class="no-print">
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assets as $key => $asset)
                        <tr class="asset-item" data-entry-id="{{ $asset->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $asset->id ?? '' }}
                            </td>
                            <td>
                                {{ $asset->name ?? '' }}
                            </td>
                            <td>
                                {{ number_format($asset->price_buy, 2) ?? '0.00' }}
                            </td>
                            <td>
                                {{ number_format($asset->price_sell, 2) ?? '0.00' }}
                            </td>
                            <td <?php 
                                if($asset->getStock()<=$asset->danger_level){
                                    echo "class='low-onstock'";
                                } 
                            ?> >
                                {{ $asset->getStock() }}
                            </td>
                            <td>
                                @can('asset_show')
                                    <a class="btn btn-xs btn-primary view-asset" data-url="{{ route('admin.dynamicAsset', $asset->id) }}" data-toggle="modal" data-target="#view-modal">
                                        {{ trans('global.update') }}
                                    </a>
                                @endcan
                                @can('asset_delete')
                                    <form action="{{ route('admin.assets.destroy', $asset->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan

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
    url: "{{ route('admin.assets.massDestroy') }}",
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
  $('.datatable-Asset:not(.ajaxTable)').DataTable({ buttons: dtButtons })
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
})

$(".view-asset").click(function(){
  $("#view-modal").fadeIn();
  $('#dynamic-content').html(''); // leave it blank before ajax call
  $('#modal-loader').show();      // load ajax loader
  var url = $(this).data('url');
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
