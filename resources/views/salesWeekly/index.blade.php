@extends('layouts.master')
@section('title' , 'المبيعات')
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
              <h2 class="mb-2 page-title">الايجار و البيع</h2>
              <div class="row my-4">
                <!-- Small table -->
                <div class="col-md-12">
                    <div class="card shadow">
                        <div class="card-body">
                       <!-- Add a create button to trigger the creation of a new item -->
                        <!-- table -->
                      <table class="table datatables" id="productsTable">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>عرض البيانات</th>
                            <th>الحالة</th>
                            <th>تاريخ بدايه الاسبوع</th>
                            <th>تاريخ نهايه الاسبوع</th>
                            <th>الحساب الكلي</th>
                            <th>العمليه</th>
                          </tr>
                        </thead>
                        <tbody>
                        @foreach ($sales as $sale)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <form method="GET"  action="{{ route('sales.orders', ['start_week' => $sale->week_start_date, 'end_week' => $sale->week_end_date, 'status' => $sale->status]) }}" style="display: inline;">
                                    @isset ($sale->deleted_at)
                                    <input type="hidden" name="archive" value="true">
                                    @endisset
                                    <button type="submit" style="border: none; background: none; color: red; cursor: pointer; text-decoration: none; padding: 0;">
                                        البيانات
                                    </button>
                                </form>
                            </td>

                            @if($sale->status == 'pending')
                            <td><span class="badge badge-warning">ايجار</span></td>
                            @else
                                <td><span class="badge badge-danger">بيع</span></td>
                            @endif
                            <td>{{ $sale->week_start_date }}</td>
                            <td>{{ $sale->week_end_date }}</td>
                            <td>{{ $sale->total_price}}</td>
                            <td>
                                @if (is_null($sale->deleted_at))
                                <button disabled type="button" class="btn btn-warning archive-btn" data-toggle="modal" data-target="#archiveModal" data-archive-id="{{ $sale->ids }}">
                                    أرشفة
                                </button>
                                @else
                                لا توجد
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $sales->links() }}
                      <!-- Delete Modal -->
                      <div class="modal fade" id="archiveModal" tabindex="-1" role="dialog" aria-labelledby="archiveModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="archiveModalLabel">تأكيد الأرشفة</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    هل انت متاكد من ارشفة حساب هذا الاسبوع ؟
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
                                    <form id="archive-form" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-warning" id="archive-btn">أرشفة</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>


                    </div>
                  </div>
                </div> <!-- simple table -->
              </div> <!-- end section -->
            </div> <!-- .col-12 -->
          </div> <!-- .row -->
        </div> <!-- .container-fluid -->
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
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-56159088-1"></script>


<script>
    $(document).ready(function() {
        // Enable all buttons with class "archive-btn"
        $('.archive-btn').prop('disabled', false);

        // Enable the button when the archive modal is shown
        $('#archiveModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var sales_id = button.data('archive-id');
            var form = $('#archive-form');
            form.attr('action', '{{ route("sales.archive", ":sales_id") }}'.replace(':sales_id', sales_id));
            $('#archive-form button[type="submit"]').removeClass('disabled').prop('disabled', false);
        });
    });
</script>

{{--
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'UA-56159088-1');

    $(document).ready(function(){
      let debounceTimer;

      function fetchProducts(page = 1) {
          let search = $('#search').val();
          let status = $('#status').val();
          $.ajax({
              url: '{{ route('product.search') }}',
              method: 'GET',
              data: {
                sectionId: @json($section->id), // Ensure proper escaping
                search: search,
                status: status,
                page: page
            },
              success: function(data) {
                console.log(data);

                  $('#productsTable tbody').html(data.tableRows);
                  $('.pagination').html(data.pagination);
              },
            error: function(xhr, status, error) {
                // Handle error
                console.error('Error:', error);
            }

          });
      }

      function debounce(func, delay) {
          return function(...args) {
              clearTimeout(debounceTimer);
              debounceTimer = setTimeout(() => func.apply(this, args), delay);
          };
      }

      const debouncedFetchProducts = debounce(function() {
          fetchProducts();
      }, 500); // 500ms delay

      $('#search, #status').on('keyup change', debouncedFetchProducts);

      $(document).on('click', '.pagination a', function(event) {
          event.preventDefault();
          let url = $(this).attr('href');
          let page = new URL(url).searchParams.get('page');
          fetchProducts(page);
      });
  });

</script> --}}

@endsection
