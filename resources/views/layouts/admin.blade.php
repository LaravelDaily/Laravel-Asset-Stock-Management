<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ trans('panel.site_title') }}</title>
  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/all.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/jquery.dataTables.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/buttons.dataTables.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/select.dataTables.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/coreui.min.css') }}" rel="stylesheet" />
  <script src="{{ asset('js/2d26724d1b.js') }}"></script>
  <link href="{{ asset('css/dropzone.min.css') }}" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('css/jquery-ui.min.css') }}" integrity="sha512-aOG0c6nPNzGk+5zjwyJaoRUgCdOrfSDhmMID2u4+OIslr0GjpLKo7Xm0Ao3xmpM4T8AmIouRkqwj1nrdVsLKEQ==" crossorigin="anonymous" />
  <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
  @yield('styles')
</head>

<body class="app header-fixed sidebar-fixed aside-menu-fixed pace-done sidebar-lg-show">
  <header class="app-header navbar">
    <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
      <span class="navbar-toggler-icon"></span>
    </button>
    <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show">
      <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="#">
      <span class="navbar-brand-full">
        <span>
          <img src="{{ asset('images/mbanner.PNG') }}" style="height: 50px;"/>
        </span>
      </span>
      <span class="navbar-brand-minimized">{{ trans('panel.site_title') }}</span>
    </a>
    <ul class="nav navbar-nav ml-auto">
      @if(count(config('panel.available_languages', [])) > 1)
      <li class="nav-item dropdown d-md-down-none">
        <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          {{ strtoupper(app()->getLocale()) }}
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          @foreach(config('panel.available_languages') as $langLocale => $langName)
          <a class="dropdown-item" href="{{ url()->current() }}?change_language={{ $langLocale }}">{{ strtoupper($langLocale) }} ({{ $langName }})</a>
          @endforeach
        </div>
      </li>
      @endif


    </ul>

    <div class="mr-2">
      <a class="btn btn-link" href="{{ route('admin.notifications.index') }}">
        <i class="fas fa-bell" style="font-size:medium">
          @if ($count_notification > 0)
          <span class="badge badge-pill badge-danger">{{ $count_notification }}</span>
          @endif
        </i>
      </a>

    </div>

  </header>

  <div class="app-body">
    @include('partials.menu')
    <main class="main">


      <div style="padding-top: 20px" class="container-fluid">
        @if(session('message'))
        <div class="row mb-2">
          <div class="col-lg-12">
            <div class="alert alert-success" role="alert">{{ session('message') }}</div>
          </div>
        </div>
        @endif
        @if($errors->count() > 0)
        <div class="alert alert-danger">
          <ul class="list-unstyled">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif
        @yield('content')

      </div>


    </main>
    <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
      {{ csrf_field() }}
    </form>
  </div>
  <script src="{{ asset('js/jquery.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/popper.min.js') }}"></script>
  <script src="{{ asset('js/coreui.min.js') }}"></script>
  <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>
  <script src="{{ asset('js/buttons.flash.min.js') }}"></script>
  <script src="{{ asset('js/buttons.html5.min.js') }}"></script>
  <script src="{{ asset('js/buttons.print.min.js') }}"></script>
  <script src="{{ asset('js/buttons.colVis.min.js') }}"></script>
  <script src="{{ asset('js/pdfmake.min.js') }}"></script>
  <script src="{{ asset('js/vfs_fonts.js') }}"></script>
  <script src="{{ asset('js/jszip.min.js') }}"></script>
  <script src="{{ asset('js/dataTables.select.min.js') }}"></script>
  <script src="{{ asset('js/ckeditor.js') }}"></script>
  <script src="{{ asset('js/moment.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
  <script src="{{ asset('js/select2.full.min.js') }}"></script>
  <script src="{{ asset('js/dropzone.min.js') }}"></script>
  <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('js/main.js') }}"></script>
  <script src="{{ asset('js/printThis.js') }}"></script>
  <script>
    $(function() {
      let copyButtonTrans = '{{ trans('
      global.datatables.copy ') }}'
      let csvButtonTrans = '{{ trans('
      global.datatables.csv ') }}'
      let excelButtonTrans = '{{ trans('
      global.datatables.excel ') }}'
      let pdfButtonTrans = '{{ trans('
      global.datatables.pdf ') }}'
      let printButtonTrans = '{{ trans('
      global.datatables.print ') }}'
      let colvisButtonTrans = '{{ trans('
      global.datatables.colvis ') }}'
      let selectAllButtonTrans = '{{ trans('
      global.select_all ') }}'
      let selectNoneButtonTrans = '{{ trans('
      global.deselect_all ') }}'

      let languages = {
        'en': 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/English.json'
      };

      $.extend(true, $.fn.dataTable.Buttons.defaults.dom.button, {
        className: 'btn'
      })
      $.extend(true, $.fn.dataTable.defaults, {
        language: {
          url: languages['{{ app()->getLocale() }}']
        },
        columnDefs: [{
          orderable: false,
          className: 'select-checkbox',
          targets: 0
        }, {
          orderable: false,
          searchable: false,
          targets: -1
        }],
        select: {
          style: 'multi+shift',
          selector: 'td:first-child'
        },
        order: [],
        scrollX: true,
        pageLength: 100,
        dom: 'lBfrtip<"actions">',
        buttons: [{
            extend: 'selectAll',
            className: 'btn-primary',
            text: selectAllButtonTrans,
            exportOptions: {
              columns: ':visible'
            }
          },
          {
            extend: 'selectNone',
            className: 'btn-primary',
            text: selectNoneButtonTrans,
            exportOptions: {
              columns: ':visible'
            }
          },
          {
            extend: 'copy',
            className: 'btn-default',
            text: copyButtonTrans,
            exportOptions: {
              columns: ':visible'
            }
          },
          {
            extend: 'csv',
            className: 'btn-default',
            text: csvButtonTrans,
            exportOptions: {
              columns: ':visible'
            }
          },
          {
            extend: 'excel',
            className: 'btn-default',
            text: excelButtonTrans,
            exportOptions: {
              columns: ':visible'
            }
          },
          {
            extend: 'pdf',
            className: 'btn-default',
            text: pdfButtonTrans,
            exportOptions: {
              columns: ':visible'
            }
          },
          {
            extend: 'print',
            className: 'btn-default',
            text: printButtonTrans,
            exportOptions: {
              columns: ':visible,:not(.no-print)'
            }
          },
          {
            extend: 'colvis',
            className: 'btn-default',
            text: colvisButtonTrans,
            exportOptions: {
              columns: ':visible'
            }
          }
        ]
      });

      $.fn.dataTable.ext.classes.sPageButton = '';
    });
  </script>
  <script>
    $("#price_buy").blur(function() {
      $('#price_buy').val(parseFloat(this.value).toFixed(2));
    });
    $("#price_sell").blur(function() {
      $('#price_sell').val(parseFloat(this.value).toFixed(2));
    });

    $(document).ajaxStart(function() {
      $(".btn").css("pointer-events", "none");
      $("div.spanner").addClass("show");
      $("div.overlay").addClass("show");
      $(document).keypress(function(event) {
        if (event.keyCode === 10 || event.keyCode === 13) {
          event.preventDefault();
        }
      });
    });

    $(document).ajaxComplete(function() {
      $(".btn").css("pointer-events", "auto");
      $("div.spanner").removeClass("show");
      $("div.overlay").removeClass("show");
      $(document).keypress(function(event) {

      });
    });
    $(document).submit(function(event) {
      $("div.spanner").addClass("show");
      $("div.overlay").addClass("show");
      $(".btn").css("pointer-events", "none");
      $(document).keypress(function(event) {
        if (event.keyCode === 10 || event.keyCode === 13) {
          event.preventDefault();
        }
      });
    });
    $("a").click(function(event) {
      if ($(this).attr("href") != "#") {
        $("div.spanner").addClass("show");
        $("div.overlay").addClass("show");
        $(".btn").css("pointer-events", "none");
        $(document).keypress(function(event) {
          if (event.keyCode === 10 || event.keyCode === 13) {
            event.preventDefault();
          }
        });
      }
    });
    var _timeoutLoaderHider = null;
    var _confirm = window.confirm;
    window.confirm = function() {
      //catch result
      var confirmed = _confirm.apply(window, arguments);
      if (confirmed) {

      } else {

        if (checkIfLoaderisVisible) {
          _timeoutLoaderHider = setTimeout(function() {
            $("div.spanner").removeClass("show");
            $("div.overlay").removeClass("show");
            $(".btn").css("pointer-events", "auto");
          }, 1000);
        } else {
          clearTimeout(_timeoutLoaderHider);
        }

      }
      return confirmed;
    };

    function checkIfLoaderisVisible() {
      if ($("div.spanner").hasClass("show")) {
        return true;
      } else {
        return false;
      }
    }
  </script>

  @yield('scripts')
  <div class="overlay"></div>
  <div class="spanner">
    <div class="loader"></div>
    <p>Loading, please be patient.</p>
  </div>
</body>

</html>