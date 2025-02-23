@extends('layouts.master')
@section('title' , 'اخر الفواتير')
@section('style')
<link rel="stylesheet" href="/assets/css/pikaday.css">
<style>
.disabled {
    opacity: 0.5;
    pointer-events: none;
    cursor: not-allowed;
    background-color: #ccc;
    color: #666;
  }
</style>
@endsection
@section('content')
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <h4 class="alert-heading text-center">Error!</h4>
    <hr>
    <ul class="list-unstyled mb-0">
        @foreach ($errors->all() as $error)
            <li class="text-danger">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ $error }}
            </li>
        @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
<div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-12">
        {{-- Content --}}
        <div class="row">
            <!-- Striped rows -->
            <div class="col-md-12 my-4">
              <h2 class="h4 mb-1">اخر الفواتير</h2>
              <p class="mb-4"></p>
              <div class="card shadow">
                <div class="card-body">
                  <div class="toolbar row mb-3">
                    <div class="col">
                      <form class="form-inline">
                        <div class="form-row">
                          <div class="form-group col-auto">
                            <label for="search" class="sr-only">بحث</label>
                            <input type="text" class="form-control" id="search" value="" placeholder="بحث">
                          </div>
                          <div class="form-group col-auto ml-3">
                            {{-- <label class="my-1 mr-2 sr-only" for="status">الحالة</label> --}}
                            {{-- <select name="status" class="custom-select my-1 mr-sm-2" id="status">
                                <option value="">جميع الحالات</option>
                                <option value="pending">ايجار</option>
                                <option value="inactive">بيع</option>
                            </select> --}}
                            <!-- Date Range -->

                        </div>
                        </div>
                      </form>
                    </div>
                  </div>
                  <!-- table -->
                  <table class="table" id="invoicesTable">
                    <thead>
                      <tr>
                        <th>اسم العميل</th>
                        <th>اسم المنتج</th>
                        <th>السعر</th>
                        <th>المدفوع</th>
                        <th>تاريخ الاستلام</th>
                        <th>تاريخ الرجوع</th>
                        <th>الحالة</th>
                        {{-- <th>العمليات</th> --}}
                      </tr>
                    </thead>
                    <tbody id="recent-invoice-table">
                        {{-- {!! dump($invoices->response()->getData()) !!} --}}
                    @foreach ($invoices->toArray(request()) as $invoice)
                      <tr>
                        <td>{{ $invoice['name'] }}</td>
                        <td>{{ $invoice['data'] ?? 'لا يوجد منتج' }}</td>
                        <td>{{ $invoice['price'] }}</td>
                        <td>{{ $invoice['payment'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($invoice['date_of_receipt'])->format('Y-m-d')}}</td>
                        <td>
                            {{ $invoice['return_date'] ? \Carbon\Carbon::parse($invoice['return_date'])->format('Y-m-d') : "لا يوجد" }}
                        </td>
                        @if($invoice['status'] == 'pending')
                            <td><span class="badge badge-warning">ايجار</span></td>
                        @else
                            <td><span class="badge badge-danger">بيع</span></td>
                        @endif
                        {{-- <td class="text-center">
                            <div class="btn-group">
                                <form action="{{ route('invoice.destroy', $invoice->id) }}" method="post">
                                    @csrf
                                    @method("DELETE")
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-toggle="modal" data-target="#verticalModal1" data-invoice-id="{{ $invoice->id }}" disabled>
                                        <i class="fe fe-16 fe-trash-2"></i>&nbsp;حذف
                                    </button>
                                </form>
                            </div>
                        </td> --}}
                    </tr>
                      @endforeach
                    </tbody>
                  </table>
                  <nav aria-label="Table Paging" class="mb-0 text-muted">
                      <ul class="pagination justify-content-end mb-0">
                          {{ $invoices->links() }}
                    </ul>
                  </nav>
                </div>
              </div>
            </div> <!-- simple table -->
          </div> <!-- end section -->
        </div> <!-- .col-12 -->
      </div> <!-- .row -->
        {{-- End Content --}}
        </div> <!-- .col-12 -->
    </div> <!-- .row -->
</div> <!-- .container-fluid -->




{{-- <div class="modal fade" id="verticalModal1" tabindex="-1" role="dialog" aria-labelledby="verticalModal1Title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verticalModal1Title">حذف الفاتورة</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">هل انت متاكد من حذف الفاتورة</div>
            <div class="modal-footer">
                <button type="button" class="btn mb-2 btn-secondary" data-dismiss="modal">غلق</button>
                <form id="delete-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn mb-2 btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div> --}}







@endsection
@section('script')
<script src="/js/jquery.min.js"></script>
<script src="/js/popper.min.js"></script>
<script src="/js/moment.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/simplebar.min.js"></script>
<script src='/js/daterangepicker.js'></script>
<script src='/js/jquery.stickOnScroll.js'></script>
<script src="/js/tinycolor-min.js"></script>
<script src="/js/config.js"></script>
<script src="/js/apps.js"></script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="/assets/js/googletagmanger.js"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'UA-56159088-1');
</script>
<script>
    $(document).ready(function () {
        $('.btn-outline-success').prop('disabled', false);
    });
</script>

{{-- 
<script>
    $(document).ready(function(){
        let debounceTimer;

        function fetchInvoices(page = 1) {
            let search = $('#search').val();
            let status = $('#status').val();
            let start_date = $('#start_date').val();
            let end_date = $('#end_date').val();
            $.ajax({
                url: '{{ route('invoice.search') }}',
                method: 'GET',
                data: { search: search, start_date: start_date, end_date: end_date, status: status, page: page },
                success: function(data) {
                    // console.log(data);
                    // $('#invoicesTable tbody').html(data.tableRows);
                    // $('.pagination').html(data.pagination);
                }
            });
        }

        function debounce(func, delay) {
            return function(...args) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => func.apply(this, args), delay);
            };
        }

        const debouncedFetchInvoices = debounce(function() {
            fetchInvoices();
        }, 500);

        $('#search, #status').on('keyup change', debouncedFetchInvoices);

        $(document).on('click', '.pagination a', function(event) {
            event.preventDefault();
            let url = $(this).attr('href');
            let page = new URL(url).searchParams.get('page');
            fetchInvoices(page);
        });
    });
    </script>

<script>
    $(document).ready(function() {
        $('.delete-btn').prop('disabled', false);
        $('#verticalModal1').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var invoiceId = button.data('invoice-id');
            var form = $('#delete-form');
            form.attr('action', '{{ route("invoice.destroy", ":invoice") }}'.replace(':invoice', invoiceId));
            $('#delete-form delete[type="submit"]').removeClass('disabled').prop('disabled', false);
        });
    });
</script> --}}
<script>
    $(document).ready(function() {
    $('#search').on('keyup', function() {
        let query = $(this).val();

        $.ajax({
            url: "{{ route('invoice.recent.search') }}", // Laravel route for search
            type: "GET",
            data: { search: query },
            success: function(data) {
                $('#recent-invoice-table').html(data); // Update the table body
            }
        });
    });
});

</script>

@endsection
