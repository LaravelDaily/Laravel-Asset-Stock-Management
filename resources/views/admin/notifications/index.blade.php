@extends('layouts.admin')
@section('content')

@foreach ($notifications as $notification)
<div class="card">
  <div class="card-header">
    <div class="row">
      <div class="col-sm-8 font-weight-bold
      @if ($notification->read == 0)
          text-danger
      @else
          text-primary
      @endif
      ">{{ $notification->title }}</div>
      <div class="col-sm-4 text-right">Date: {{ $notification->created_at }}</div>
    </div>
  </div>
  <div class="card-body">
    {!! $notification->message !!}
  </div>
</div>
@endforeach


@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

  $.extend(true, $.fn.dataTable.defaults, {
    order: [[ 0, 'desc' ]],
    pageLength: 100,
      columnDefs: [{
          orderable: true,
          className: '',
          targets: 0
      }]
  });
  $('.datatable-Transaction:not(.ajaxTable)').DataTable({ buttons: dtButtons })
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
})

</script>
@endsection
