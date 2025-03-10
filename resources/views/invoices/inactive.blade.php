@extends('layouts.master')
@section('title' , 'الفواتير')
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
<div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-12">
        {{-- Content --}}
        <div class="row">
            <!-- Striped rows -->
            <div class="col-md-12 my-4">
              <h2 class="h4 mb-1">فواتير البيع</h2>
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
                            <div class="form-group col-auto ml-3 d-none">
                                <label class="my-1 mr-2 sr-only" for="status">الحالة</label>
                                <select name="status" class="custom-select my-1 mr-sm-2" id="status">
                                    <option value="inactive"></option>
                                </select>
                            </div>
                      </form>
                    </div>
                  </div>
                  <!-- table -->
                  <table class="table" id="invoicesTable">
                    <thead>
                      <tr>
                        <th>اسم العميل</th>
                        <th>المنتجات</th>
                        {{-- <th>السعر</th> --}}
                        {{-- <th>المدفوع</th> --}}
                        @can('access-superAdmin')
                        <th>الباقي</th>
                        @endcan
                        <th>تاريخ الفاتورة</th>
                        <th>تاريخ الاستلام</th>
                        <th>تاريخ الرجوع</th>
                        <th>الحالة</th>
                        <th>العمليات</th>
                      </tr>
                    </thead>
                    <tbody>
                    @foreach ($invoices as $invoice)
                      <tr>
                        <td><a href="{{ route('clients.show' , $invoice->id) }}" style="
                            text-decoration: none;
                            color: #3498db; /* Link color */
                            font-weight: 600; /* Bold text */
                            border-radius: 5px; /* Rounded corners */
                            transition: background-color 0.3s ease, color 0.3s ease; /* Smooth transition */
                        ">{{ $invoice->client->name }}</a></td>
                        <td>
                            <a style="text-decoration: none;" href="{{ route('invoice.show' , $invoice->id) }}"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: red">{{ $invoice->orders->count() }}</span></a>
                        </td>
                        {{-- <td>{{ $invoice->orders->sum('price') }}</td> --}}
                        {{-- <td>{{ $invoice->orders->sum('payment') }}</td> --}}
                        @can('access-superAdmin')
                        <td>{{ $invoice->orders->sum('price') - $invoice->orders->sum('payment')  }}</td>
                        @endcan
                        <td>{{\Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y') }}</td>
                        <td>{{ $invoice->date_of_receipt ? \Carbon\Carbon::parse($invoice->date_of_receipt)->format('d-m-Y') : 'لا يوجد' }}</td>
                        <td>{{ $invoice->return_date ? \Carbon\Carbon::parse($invoice->return_date)->format('d-m-Y') : 'لا يوجد' }}</td>
                        @if($invoice->status == 'pending')
                            <td><span class="badge badge-warning">ايجار</span></td>
                        @else
                            <td><span class="badge badge-danger">بيع</span></td>
                        @endif
                        <td class="text-center">
                            {{-- Pay Button (outside dropdown) --}}
                            @can('access-superAdmin')
                                @if ($invoice->orders->sum('price') - $invoice->orders->sum('payment') != 0)
                                    <form action="{{ route('invoice.pay', $invoice->id) }}" method="post" class="d-inline-block">
                                        @csrf
                                        <button type="button" class="btn btn-sm btn-outline-info pay-btn"
                                            data-toggle="modal" data-target="#verticalModal2"
                                            data-invoice-id="{{ $invoice->id }}">
                                            <i class="fe fe-dollar-sign"></i> دفع
                                        </button>
                                    </form>
                                @endif

                                {{-- Restore Button (outside dropdown) --}}
                                @if (is_null($invoice->restored_at))
                                    <form action="{{ route('invoice.restore', $invoice->id) }}" method="post" class="d-inline-block">
                                        @csrf
                                        @method("PUT")
                                        <button type="button" class="btn btn-sm btn-outline-warning restore-btn"
                                            data-toggle="modal" data-target="#verticalModal"
                                            data-invoice-id="{{ $invoice->id }}">
                                            <i class="fe fe-rotate-ccw"></i> استرجاع
                                        </button>
                                    </form>
                                @endif
                            @endcan

                            {{-- Dropdown menu for edit, delete, print --}}
                            <div class="dropdown d-inline-block">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                    id="invoiceMenu{{ $invoice->id }}" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <i class="fe fe-more-vertical"></i>
                                </button>

                                <div class="dropdown-menu dropdown-menu-right"
                                    aria-labelledby="invoiceMenu{{ $invoice->id }}"
                                    style="min-width: 200px; font-size: 14px; direction: rtl; text-align: right; padding: 10px; border-radius: 8px;">
                                    <a href="{{ route('invoice.print', $invoice->id) }}"
                                        class="dropdown-item d-flex align-items-center py-2 text-success">
                                         <i class="fe fe-printer ml-2"></i> طباعة
                                     </a>
                                    @can('access-superAdmin')
                                        <a href="{{ route('invoice.edit', [$invoice->id, $invoice->status]) }}"
                                           class="dropdown-item d-flex align-items-center py-2 text-primary">
                                            <i class="fe fe-edit ml-2"></i> تعديل
                                        </a>

                                        <button type="button"
                                            class="dropdown-item d-flex align-items-center py-2 text-danger delete-btn"
                                            data-toggle="modal" data-target="#verticalModal1"
                                            data-invoice-id="{{ $invoice->id }}">
                                            <form action="{{ route('invoice.destroy', $invoice->id) }}" method="post" class="d-inline m-0 p-0">
                                                @csrf
                                                @method("DELETE")
                                            </form>
                                            <i class="fe fe-trash-2 ml-2"></i> حذف
                                        </button>
                                    @endcan

                                </div>
                            </div>

                        </td>
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



<div class="modal fade" id="verticalModal" tabindex="-1" role="dialog" aria-labelledby="verticalModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verticalModalTitle">استرجاع الفاتورة</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">هل انت متاكد من استرجاع الفاتورة</div>
            <div class="modal-footer">
                <button type="button" class="btn mb-2 btn-secondary" data-dismiss="modal">غلق</button>
                <form id="restore-form" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn mb-2 btn-primary" onclick="this.disabled = true; this.form.submit();">
                        استرجاع
                    </button>
                    {{-- <button type="submit" class="btn mb-2 btn-primary">استرجاع</button> --}}
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="verticalModal1" tabindex="-1" role="dialog" aria-labelledby="verticalModal1Title" aria-hidden="true">
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
                    <button type="submit" class="btn mb-2 btn-danger" onclick="this.disabled = true; this.form.submit();">
                        حذف
                    </button>
                    {{-- <button type="submit" class="btn mb-2 btn-danger">حذف</button> --}}
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verticalModal2" tabindex="-1" role="dialog" aria-labelledby="verticalModal2Title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verticalModal2Title">دفع الفاتورة</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">هل انت متاكد من دفع الفاتورة</div>
            <div class="modal-footer">
                <button type="button" class="btn mb-2 btn-secondary" data-dismiss="modal">غلق</button>
                <form id="pay-form" method="POST">
                    @csrf
                    <button type="submit" class="btn mb-2 btn-info" onclick="this.disabled = true; this.form.submit();">
                        دفع
                    </button>

                    {{-- <button type="submit" class="btn mb-2 btn-info" onclick="disable">دفع</button> --}}
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Update Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">تحديث المدفوعات</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form inside the modal -->
                <form id="updateForm" action="{{ route('invoice.update','update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="invoice_id" name="invoice_id">
                    <input type="hidden" id="status" name="status">
                    <div class="form-group">
                        <label for="name">الاسم:</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="الاسم" required>
                    </div>
                    <div class="form-group">
                        <label for="address">العنوان:</label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="أدخل العنوان">
                    </div>
                    <div class="form-group">
                        <label for="phone">الهاتف:</label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="أدخل رقم الهاتف">
                    </div>
                    <div class="form-group"  id="dateContainer1">
                        <label for="date_of_receipt">تاريخ الاستلام:</label>
                        <input type="date" class="form-control" id="date_of_receipt" name="date_of_receipt">
                    </div>
                    <div class="form-group"  id="dateContainer2">
                        <label for="return_date">تاريخ العودة:</label>
                        <input type="date" class="form-control" id="return_date" name="return_date">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" id="saveChanges">حفظ التعديلات</button>
            </div>
        </div>
    </div>
</div>
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
    $(document).ready(function () {
        $('.btn-outline-success').prop('disabled', false);
    });
</script>
<script>
    $(document).ready(function() {
        $('#updateModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var invoiceId = button.data('invoice-id'); // Extract the invoice ID
            var status = button.data('status'); // Extract the invoice ID
            var name = button.data('name'); // Extract the name
            var address = button.data('address'); // Extract the address
            var phone = button.data('phone'); // Extract the phone
            var dateOfReceipt = button.data('date-of-receipt'); // Extract the date of receipt
            var returnDate = button.data('return-date'); // Extract the return date

            var modal = $(this);
            modal.find('#invoice_id').val(invoiceId); // Set the invoice ID in the hidden input
            modal.find('#status').val(status); // Set the name in the input field
            modal.find('#name').val(name); // Set the name in the input field
            modal.find('#address').val(address); // Set the address in the input field
            modal.find('#phone').val(phone); // Set the phone in the input field
            modal.find('#date_of_receipt').val(dateOfReceipt); // Set the date of receipt in the input field
            modal.find('#return_date').val(returnDate); // Set the return date in the input field
        });

        $('#saveChanges').on('click', function() {
            $('#updateForm').submit(); // Submit the form inside the modal
        });
    });
</script>

<script>
    window.dataLayer = window.dataLayer || [];
    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'UA-56159088-1');
  </script>
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
                    console.log(data);
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
        $('.restore-btn').prop('disabled', false);
        $('#verticalModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var invoiceId = button.data('invoice-id');
            var form = $('#restore-form');
            form.attr('action', '{{ route("invoice.restore", ":invoice") }}'.replace(':invoice', invoiceId));
            $('#restore-form restore[type="submit"]').removeClass('disabled').prop('disabled', false);
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
</script>
<script>
    $(document).ready(function() {
        $('.pay-btn').prop('disabled', false);
        $('#verticalModal2').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var invoiceId = button.data('invoice-id');
            var form = $('#pay-form');
            form.attr('action', '{{ route("invoice.pay", ":invoice") }}'.replace(':invoice', invoiceId));
            $('#pay-form pay[type="submit"]').removeClass('disabled').prop('disabled', false);
        });
    });
</script>
<script src="/assets/js/moment.min.js"></script>
<script src="/assets/js/pikaday.js"></script>
<script>
    $(document).ready(function() {
        // Debounce function to limit the frequency of function execution
        let debounceTimer;

        function debounce(func, delay) {
            return function(...args) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => func.apply(this, args), delay);
            };
        }

        // Fetch invoices from the server with optional pagination
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
                    // Update the table and pagination with the data returned
                    $('#invoicesTable tbody').html(data.tableRows);
                    $('.pagination').html(data.pagination);
                }
            });
        }

        // Debounced version of fetchInvoices
        const debouncedFetchInvoices = debounce(function() {
            fetchInvoices();
        }, 500); // 500ms delay

        // Attach debounced fetchInvoices to input change events
        $('#search, #status').on('keyup change', debouncedFetchInvoices);
        $('#start_date, #end_date').on('change', debouncedFetchInvoices); // Trigger request on date change

        // Handle pagination clicks
        $(document).on('click', '.pagination a', function(event) {
            event.preventDefault();
            let url = $(this).attr('href');
            let page = new URL(url).searchParams.get('page');
            fetchInvoices(page);
        });

        // Date handling with Pikaday and moment.js
        var gregorianDateInput1 = document.getElementById('start_date');
        var gregorianDateInput2 = document.getElementById('end_date');
        var statusSelect = document.getElementById('status');
        var dateContainer1 = document.getElementById('dateContainer1');
        var dateContainer2 = document.getElementById('dateContainer2');

        var picker1 = new Pikaday({
            field: gregorianDateInput1,
            format: 'YYYY-MM-DD',
            i18n: {
                previousMonth: 'الشهر السابق',
                nextMonth: 'الشهر القادم',
                months: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
                weekdays: ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'],
                weekdaysShort: ['أحد', 'اثنين', 'ثلاثاء', 'أربعاء', 'خميس', 'جمعة', 'سبت']
            },
            onSelect: function(date) {
                var gregorianDate = moment(date).format('YYYY-MM-DD');
                gregorianDateInput1.value = gregorianDate;

                // Set the second date picker to one day after the selected date
                var nextDay = moment(date).add(1, 'days').toDate();
                picker2.setDate(nextDay);

                // Trigger debounced invoice fetch when date is selected
                debouncedFetchInvoices(); // Correctly call debouncedFetchInvoices here
            },
            isRTL: true
        });

        var picker2 = new Pikaday({
            field: gregorianDateInput2,
            format: 'YYYY-MM-DD',
            i18n: {
                previousMonth: 'الشهر السابق',
                nextMonth: 'الشهر القادم',
                months: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
                weekdays: ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'],
                weekdaysShort: ['أحد', 'اثنين', 'ثلاثاء', 'أربعاء', 'خميس', 'جمعة', 'سبت']
            },
            isRTL: true,
            onSelect: function(date) {
                var gregorianDate = moment(date).format('YYYY-MM-DD');
                gregorianDateInput2.value = gregorianDate;
                debouncedFetchInvoices(); // Trigger fetch when the end date is selected
            }
        });

        function updateDateContainers() {
            if (dateContainer1 && dateContainer2) {
                if (statusSelect.value === 'inactive') {
                    dateContainer1.style.display = 'none';
                    dateContainer2.style.display = 'none';
                    gregorianDateInput1.value = ''; // Clear the value
                    gregorianDateInput2.value = ''; // Clear the value
                } else {
                    dateContainer1.style.display = 'block';
                    dateContainer2.style.display = 'block';
                }
            }
        }

        if (statusSelect) {
            statusSelect.addEventListener('change', function() {
                updateDateContainers();
            });
        }

        updateDateContainers();
    });
    </script>

@endsection
