@extends('layouts.admin')
@section('content')
@can('stock_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.stocks.create") }}">
                {{ trans('global.add') }} {{ trans('cruds.stock.title_singular') }}
            </a>
        </div>
        <div class="col-lg-12 mt-2">
            @if(session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.stock.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Stock">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.stock.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.stock.fields.asset') }}
                        </th>
                        <th>
                            {{ trans('cruds.stock.fields.current_stock') }}
                        </th>
                        <th>
                            Add Stock
                        </th>
                        <th>
                            Remove Stock
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stocks as $key => $stock)
                        <tr data-entry-id="{{ $stock->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $stock->id ?? '' }}
                            </td>
                            <td>
                                {{ $stock->asset->name ?? '' }}
                            </td>
                            <td>
                                {{ $stock->current_stock ?? '' }}
                            </td>
                            <td>
                                <form action="{{ route('admin.transactions.storeStock', $stock->id) }}" method="POST" style="display: inline-block;" class="form-inline">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="action" value="add">
                                    <input type="number" name="stock" class="form-control form-control-sm col-4" min="1">
                                    <input type="submit" class="btn btn-xs btn-danger" value="ADD">
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('admin.transactions.storeStock', $stock->id) }}" method="POST" style="display: inline-block;" class="form-inline">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="number" name="stock" class="form-control form-control-sm col-4" min="1">
                                    <input type="submit" class="btn btn-xs btn-danger" value="REMOVE">
                                </form>
                            </td>
                            <td>
                                @can('stock_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.stocks.show', $stock->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('stock_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.stocks.edit', $stock->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('stock_delete')
                                    <form action="{{ route('admin.stocks.destroy', $stock->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@can('stock_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.stocks.massDestroy') }}",
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
  $('.datatable-Stock:not(.ajaxTable)').DataTable({ buttons: dtButtons })
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
})

</script>
@endsection
