@extends('layouts.master')
@section('title' , 'انشاء فاتورة بيع')
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

<style>
    @keyframes fadeInScale {
        0% {
            opacity: 0;
            transform: scale(0.8);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    .invoice-title {
        font-family: 'Poppins', sans-serif;
        font-size: 36px;
        font-weight: bold;
        color: #9b1717;
        text-align: center;
        margin-bottom: 5px;
        animation: fadeInScale 1s ease-in-out;
    }
</style>
{{-- <link rel="stylesheet" href="/asset/css/googleapifont">
    <link rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Poppins:200,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900"> --}}
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
            <!-- row -->
            <h1 class="invoice-title">فاتورة بيع</h1>
            <div class="row">
                <div class="col-md-12 mb-30">
                    <div class="card card-statistics h-100">
                        <div class="card-body">
                                <form id="myForm"  class=" row mb-30" action="{{ route('invoice.store') }}" method="POST">
                                    @csrf
                                    <div class="card-body">

                                        <div class="form-group col-md-12">
                                            <input type="hidden" name="status" value="inactive">
                                            {{-- <label for="inputState5" style="font-weight: bold; margin-bottom: 10px; font-family: 'Poppins', sans-serif;">نوع الفاتورة</label>
                                            <select name="status" id="inputState5" class="form-control" style="width: 100%; padding: 5px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px; color: #000; font-family: 'Poppins', sans-serif; font-weight: 400;">
                                                <option selected value="pending" style="font-weight: 500;">ايجار</option>
                                                <option value="inactive" style="font-weight: 500;">بيع</option>
                                            </select> --}}
                                        </div>
                                            {{-- <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <div id="dateContainer1" class="input-group">
                                                    <label for="gregorianDate1" class="form-label">تاريخ الاستلام:</label>
                                                    <input type="text" id="gregorianDate1" value="{{ old('date_of_receipt') }}" name="date_of_receipt" class="form-control" placeholder="yyyy-mm-dd">
                                                </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <div id="dateContainer2" class="input-group">
                                                        <label for="gregorianDate2" class="form-label">تاريخ الرجوع:</label>
                                                        <input type="text" id="gregorianDate2" value="{{ old('return_date') }}" name="return_date" class="form-control" placeholder="yyyy-mm-dd">
                                                    </div>
                                                </div>
                                            </div> --}}
                                          <div class="form-row">
                                            <div class="form-group col-md-6">
                                              <label for="inputEmail4">اسم العميل</label>
                                              <input required type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="الاسم" id="inputEmail5">
                                            </div>
                                            <div class="form-group col-md-6">
                                              <label for="inputPassword4">رقم العميل</label>
                                              <input required type="text" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="رقم التلفون" id="inputPassword5">
                                            </div>
                                          </div>
                                          <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label for="city" style="font-family: 'Poppins', sans-serif; font-weight: bold;">المدينة/المركز</label>
                                                <select name="city" id="city" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-family: 'Poppins', sans-serif; font-size: 16px; color: #000;">
                                                    <option style="text-align: center;" value="" {{ old('city') == '' ? 'selected' : '' }}>-- اختر  --</option>
                                                    <option value="أشمون" {{ old('city') == 'أشمون' ? 'selected' : '' }}>اشمون</option>
                                                    <option value="سمادون" {{ old('city') == 'سمادون' ? 'selected' : '' }}>سمادون</option>
                                                    <option value="طاليا" {{ old('city') == 'طاليا' ? 'selected' : '' }}>طاليا</option>
                                                    <option value="الكوادي" {{ old('city') == 'الكوادي' ? 'selected' : '' }}>الكوادي</option>
                                                    <option value="الفرعنية" {{ old('city') == 'الفرعنية' ? 'selected' : '' }}>الفرعنية</option>
                                                    <option value="جامع بدر" {{ old('city') == 'جامع بدر' ? 'selected' : '' }}>جامع بدر</option>
                                                    <option value="رملة الانجب" {{ old('city') == 'رملة الانجب' ? 'selected' : '' }}>رملة الانجب</option>
                                                    <option value="محلة سبك" {{ old('city') == 'محلة سبك' ? 'selected' : '' }}>محلة سبك</option>
                                                    <option value="سبك الاحد" {{ old('city') == 'سبك الاحد' ? 'selected' : '' }}>سبك الاحد</option>
                                                    <option value="شما" {{ old('city') == 'شما' ? 'selected' : '' }}>شما</option>
                                                    <option value="سنتريس" {{ old('city') == 'سنتريس' ? 'selected' : '' }}>سنتريس</option>
                                                    <option value="دلهمو" {{ old('city') == 'دلهمو' ? 'selected' : '' }}>دلهمو</option>
                                                    <option value="طهواي" {{ old('city') == 'طهواي' ? 'selected' : '' }}>طهواي</option>
                                                    <option value="شنشور" {{ old('city') == 'شنشور' ? 'selected' : '' }}>شنشور</option>
                                                    <option value="الباجور" {{ old('city') == 'الباجور' ? 'selected' : '' }}>الباجور</option>
                                                    <option value="منوف" {{ old('city') == 'منوف' ? 'selected' : '' }}>منوف</option>
                                                    <option value="مدينة السادات" {{ old('city') == 'مدينة السادات' ? 'selected' : '' }}>مدينة السادات</option>
                                                    <option value="سرس الليان" {{ old('city') == 'سرس الليان' ? 'selected' : '' }}>سرس الليان</option>
                                                    <option value="تلا" {{ old('city') == 'تلا' ? 'selected' : '' }}>تلا</option>
                                                    <option value="الشهداء" {{ old('city') == 'الشهداء' ? 'selected' : '' }}>الشهداء</option>
                                                    <option value="شبين الكوم" {{ old('city') == 'شبين الكوم' ? 'selected' : '' }}>شبين الكوم</option>
                                                    <option value="قويسنا" {{ old('city') == 'قويسنا' ? 'selected' : '' }}>قويسنا</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-8">
                                                <label for="inputCity">العنوان</label>
                                                <input type="text" name="address" value="{{ old('address') }}" class="form-control" id="inputCity5" placeholder="العنوان">
                                            </div>
                                          </div>

                                        <div class="repeater">
                                            <div data-repeater-list="list-product">
                                                <div data-repeater-item>
                                                    <div class="form-row">
                                                        <div class="form-group col-md-2">
                                                            <label for="parent">القسم الرئيسي</label>
                                                            <select name="parent_id" data-repeater-parent  id="parent" class="form-control">
                                                              @foreach ($parent_sections as $section )
                                                              <option  value="{{ $section->id }}">{{ $section->name }}</option>
                                                              @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-md-2">
                                                            <label for="section">القسم الفرعي</label>
                                                            <select name="section_id" data-repeater-section id="section" class="form-control">
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <label for="product">المنتج</label>
                                                            <select name="product_id" data-repeater-product id="product" class="form-control">
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-md-2">
                                                            <label for="price">السعر النهائي</label>
                                                            <input data-repeater-price type="text" name="price" class="form-control" id="price">
                                                          </div>
                                                        <div class="form-group col-md-2">
                                                            <label for="payment">دفع</label>
                                                            <input data-repeater-payment type="text" name="payment" class="form-control" id="payment">
                                                          </div>

                                                        <div class="col text-center">
                                                            <label for="" class="mr-sm-2">العمليات:</label>
                                                            <div class="d-flex justify-content-center">
                                                                <input class="btn btn-success mr-2 confirm-btn" style="width: 50px;" data-repeater-confirm data-action="confirm" type="button" value="تأكيد" />
                                                                <input class="btn btn-primary mr-2" style="width: 50px;" data-repeater-edit type="button" value="تعديل" />
                                                                <input class="btn btn-danger" style="width: 50px;" data-repeater-delete type="button" value="حذف" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-20">
                                                <div class="col-12">
                                                    <button class="button btn mb-2 btn-success" data-repeater-create type="button">
                                                        <span class="fe fe-14 fe-plus"></span>
                                                        اضافة منتج جديد
                                                    </button>
                                                </div>
                                            </div>
                                            <br>
                                        </div>

                                        <div class="repeater-1">
                                            <div data-repeater-list="list-product-1">
                                                <div data-repeater-item>
                                                    <div class="form-row">
                                                            <div class="form-group col-md-2">
                                                              <label for="title">اسم المنتج</label>
                                                              <input data-repeater-title type="text" name="title" class="form-control" placeholder="اسم المنتج" id="title">
                                                            </div>
                                                            <div class="form-group col-md-3">
                                                              <label for="data">البيانات</label>
                                                              <input data-repeater-data type="text" name="data" class="form-control" placeholder="البيانات" id="data">
                                                            </div>
                                                            <div class="form-group col-md-2">
                                                              <label for="price">السعر النهائي</label>
                                                              <input data-repeater-price type="text" name="price" class="form-control" id="price_">
                                                            </div>
                                                            <div class="form-group col-md-2">
                                                              <label for="payment">دفع</label>
                                                              <input data-repeater-payment type="text" name="payment" class="form-control"  id="payment_">
                                                            </div>
                                                        <div class="col text-center">
                                                            <label for="" class="mr-sm-2">العمليات:</label>
                                                            <div class="d-flex justify-content-center">
                                                                <input class="btn btn-success mr-2 confirm-btn" style="width: 150px;" data-repeater-confirm data-action="confirm" type="button" value="تأكيد" />
                                                                <input class="btn btn-primary mr-2" style="width: 150px;" data-repeater-edit type="button" value="تعديل" />
                                                                <input class="btn btn-danger" style="width: 150px;" data-repeater-delete type="button" value="حذف" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-20">
                                                <div class="col-12">
                                                    <button class="button btn mb-2 btn-info" data-repeater-create  type="button">
                                                        <span class="fe fe-14 fe-plus"></span>
                                                        اضافات اخري
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <br>
                                        <button type="submit" class="btn btn-primary">تاكيد البيانات</button>
                                        {{-- <button type="submit" class="btn btn-primary" onclick="this.disabled = true;">تاكيد البيانات</button> --}}
                                        {{-- <button type="submit" class="btn btn-primary" onclick="submitForm(this);">تاكيد البيانات</button> --}}
                                        {{-- <button type="submit" class="btn btn-primary" onclick="this.disabled = true; this.innerText='Processing...';">تاكيد البيانات</button> --}}
                                    </div>
                                </div> <!-- /. card-body -->
                                </form>

                        </div>
                    </div>
                </div>
            </div>
            <!-- row closed -->
        {{-- End Content --}}
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
      window.dataLayer = window.dataLayer || [];

      function gtag()
      {
        dataLayer.push(arguments);
      }
      gtag('js', new Date());
      gtag('config', 'UA-56159088-1');
    </script>
{{-- <script src="/assets/js/jquery-3.3.1.min.js"></script> --}}
<script src="/assets/js/plugins-jquery.js"></script>
<script src="/assets/js/moment.min.js"></script>
<script src="/assets/js/pikaday.js"></script>

<script>
    document.getElementById('myForm').addEventListener('submit', function(event) {
        var button = event.target.querySelector('button[type="submit"]');
        button.disabled = true;
        button.textContent = 'جاري تنفيذ طلبك';
    });
</script>
<script>
$(document).ready(function() {
    var counter = 0;
        // Initialize the repeater plugin
        var repeater = $('.repeater').repeater({
            initEmpty: true,
            show: function () {
                // Increment counter
                // Get the current repeater item
                var $item = $(this);

                // // Assign unique IDs to the new item
                $item.find('[data-action="confirm"]').attr('id', `confirm-button-${counter}`);
                $item.find('[data-repeater-edit]').attr('id', `edit-button-${counter}`);
                $item.find('[data-repeater-delete]').attr('id', `delete-button-${counter}`);
                $item.find('select[id^="parent"]').attr('id', `parent-${counter}`);
                $item.find('select[id^="section"]').attr('id', `section-${counter}`);
                $item.find('select[id^="product"]').attr('id', `product-${counter}`);
                $item.find('input[id^="title"]').attr('id', `title-${counter}`);
                $item.find('input[id^="data"]').attr('id', `data-${counter}`);
                $item.find('input[id^="price"]').attr('id', `price-${counter}`);
                $item.find('input[id^="payment"]').attr('id', `payment-${counter}`);
                $item.find('[data-repeater-edit]').prop('disabled', true);
                counter++;
                $(this).slideDown();
            },
            hide: function (deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });

        var repeater1 = $('.repeater-1').repeater({
            initEmpty: true,
            show: function () {
                // Increment counter
                // Get the current repeater item
                var $item = $(this);

                // // Assign unique IDs to the new item
                $item.find('[data-action="confirm"]').attr('id', `confirm-button-${counter}`);
                $item.find('[data-repeater-edit]').attr('id', `edit-button-${counter}`);
                $item.find('[data-repeater-delete]').attr('id', `delete-button-${counter}`);
                // $item.find('select[id^="parent"]').attr('id', `parent-${counter}`);
                // $item.find('select[id^="section"]').attr('id', `section-${counter}`);
                // $item.find('select[id^="product"]').attr('id', `product-${counter}`);
                $item.find('input[id^="title"]').attr('id', `title-${counter}`);
                $item.find('input[id^="data"]').attr('id', `data-${counter}`);
                $item.find('input[id^="price_"]').attr('id', `price_-${counter}`);
                $item.find('input[id^="payment_"]').attr('id', `payment_-${counter}`);
                $item.find('[data-repeater-edit]').prop('disabled', true);
                counter++;
                $(this).slideDown();
            },
            hide: function (deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });



        var oldData1 = {!! json_encode(old('list-product-1', [])) !!};
        var oldData = {!! json_encode(old('list-product', [])) !!};

        // console.log(oldData1);
        // console.log(oldData);

        if (oldData.length > 0) {
            let repeaterData = oldData.map(function (data, index) {
                let sectionId = 'section-' + index;
                let productId = 'product-' + index;

                getSection(data.parent_id, sectionId, function () {
                    let sectionSelect = $('#' + sectionId);
                    let options = sectionSelect.find('option');

                    if (options.length > 0) {
                        sectionSelect.val(data.section_id).trigger('change');

                        getProductData(
                            data.section_id || data.parent_id,
                            productId,
                            data.date_of_receipt,
                            data.return_date,
                            data.product_id,
                            function () {
                                let productSelect = $('#' + productId);
                                let productOptions = productSelect.find('option');

                                if (productOptions.length === 0) {
                                    // Retry getProductData if product not loaded
                                    getProductData(
                                        data.section_id || data.parent_id,
                                        productId,
                                        data.date_of_receipt,
                                        data.return_date,
                                        data.product_id,
                                        null,
                                        10 // Retry up to 10 times
                                    );
                                } else {
                                    productSelect.val(data.product_id).trigger('change');
                                }
                            },
                            10 // Initial retry count for getProductData
                        );
                    } else {
                        // Retry getSection if section select didn't populate
                        getSection(data.parent_id, sectionId, null, 10);
                    }

                }, 10); // Initial retry count for getSection

                return {
                    parent_id: data.parent_id || data.section_id,
                    section_id: data.section_id,
                    product_id: data.product_id,
                    price: data.price,
                    payment: data.payment
                };
            });

            repeater.setList(repeaterData);
        }
        if (oldData1.length > 0) {
            let repeaterData1 = oldData1.map(function (data) {
                return { title: data.title, data: data.data, 'price' : data.price , 'payment': data.payment};
            });

            repeater1.setList(repeaterData1);
        }

        // Function to get section list
        function getSection(parentId, SecId, callback , retryCount = 0) {
            var url = "{{ route('invoice.get.section') }}";
            $.ajax({
                type: 'GET',
                url: url,
                data: { parent_id: parentId },
                dataType: 'json',
                success: function (response) {
                    var sectionSelect = $('#' + SecId);
                    sectionSelect.empty();

                    if (response.data && response.data.length > 0) {
                        $.each(response.data, function (index, section) {
                            sectionSelect.append('<option value="' + section.id + '">' + section.name + '</option>');
                        });
                    } else {
                        sectionSelect.append('<option value="" disabled>لا يوجد اقسام فرعية داخل القسم</option>');
                    }

                    // Execute the callback after populating the dropdown
                    if (typeof callback === "function") {
                        callback();
                    }

                    sectionSelect.trigger('change');
                },
                error: function () {
                    if (retryCount > 0) {
                        // console.warn("Request failed. Retrying... (" + (11 - retryCount) + "/10)");
                        setTimeout(function () {
                            getSection(parentId, SecId, callback , retryCount - 1);
                        }, 1000);
                    }
                }
            });
        }



        // Function to get product list
        function getProductData(sectionId, productID,  dateOfReceipt ,returnDate, oldProductId, callback, retryCount = 0) {
            // console.log(returnDate);

            var dateOfReceipt = dateOfReceipt || $('#gregorianDate1').val();
            var returnDate = returnDate || $('#gregorianDate2').val();

            var url = "{{ route('invoice.get.product') }}";
            $.ajax({
                type: 'GET',
                url: url,
                data: {
                        section_id: sectionId,
                        date_of_receipt: dateOfReceipt,
                        return_date: returnDate,
                        status: 'inactive'
                    },
                dataType: 'json',
                success: function (response) {
                    var productSelect = $('#' + productID);
                    productSelect.empty(); // Clear existing options


                    if (response.data && response.data.length > 0) {
                        $.each(response.data, function (index, product) {

                            var productName = product.title + " : ";
                            if (product.model) {
                                productName += ' - ' + product.model;
                            }
                            if (product.color) {
                                productName += ' - ' + product.color;
                            }
                            if (product.size) {
                                productName += ' - ' + product.size;
                            }
                            productName = productName.trim().replace(/^-/, '');

                            productSelect.append('<option value="' + product.id + '">' + productName + '</option>');

                        });

                        // **Wait until options are fully loaded before selecting old value**
                        const observer = new MutationObserver(function () {
                            if (oldProductId && productSelect.find('option[value="' + oldProductId + '"]').length > 0) {
                                productSelect.val(oldProductId).trigger('change');
                                observer.disconnect(); // Stop observing once the value is set
                            }
                        });

                        observer.observe(productSelect[0], { childList: true });


                    } else {
                        productSelect.append('<option value="" disabled>لا يوجد منتجات داخل القسم</option>');
                    }

                    // Execute the callback after populating the dropdown
                    if (typeof callback === "function") {
                        callback();
                    }
                },
                error: function () {
                    if (retryCount > 0) {
                        console.warn("Request failed. Retrying... (" + (11 - retryCount) + "/10)");
                        setTimeout(function () {
                            getProductData(sectionId, productID, dateOfReceipt, returnDate, oldProductId, callback, retryCount - 1);
                        }, 1000);
                    }
                }
        });

    }



        $(document).on('click', '[data-action="confirm"]', function() {
            $('[data-action="confirm"]').prop('disabled', true);
            $('[data-repeater-create]').prop('disabled', false);
            $('[data-repeater-edit]').prop('disabled', false);
            $('[data-repeater-parent]').addClass('disabled');
            $('[data-repeater-section]').addClass('disabled');
            $('[data-repeater-product]').addClass('disabled');
            $('[data-repeater-data]').prop('readonly', true);
            $('[data-repeater-price]').prop('readonly', true);
            $('[data-repeater-payment]').prop('readonly', true);
            $('[data-repeater-title]').prop('readonly', true);
        });

        // Handle click event for the "Edit" button
        $(document).on('click', '[data-repeater-edit]', function() {
            $(this).prop('disabled', true);
            var buttonId = $(this).attr('id');
            var confirmButtonId = buttonId.replace('edit-button', 'confirm-button');
            var selectParent = buttonId.replace('edit-button', 'parent');
            var selectSection = buttonId.replace('edit-button', 'section');
            var selectProduct = buttonId.replace('edit-button', 'product');
            var inputTitle = buttonId.replace('edit-button', 'title');
            var inputData = buttonId.replace('edit-button', 'data');
            var inputPrice = buttonId.replace('edit-button', 'price');
            var inputPrice_ = buttonId.replace('edit-button', 'price_');
            var inputPayment = buttonId.replace('edit-button', 'payment');
            var inputPayment_ = buttonId.replace('edit-button', 'payment_');
            $('[data-repeater-create]').prop('disabled', true);
            $('#' + confirmButtonId).prop('disabled', false);
            $('#' + selectParent).removeClass('disabled');
            $('#' + selectSection).removeClass('disabled');
            $('#' + selectProduct).removeClass('disabled');
            $('#' + inputTitle).prop('readonly', false);
            $('#' + inputData).prop('readonly', false);
            $('#' + inputPrice).prop('readonly', false);
            $('#' + inputPrice_).prop('readonly', false);
            $('#' + inputPayment).prop('readonly', false);
            $('#' + inputPayment_).prop('readonly', false);
        });

        $(document).on('click', '[data-repeater-create]', function() {
            // $(this).prop('disabled', true);
            $('[data-repeater-create]').prop('disabled', true);
        });
        $(document).on('click', '[data-repeater-delete]', function() {
            $('[data-repeater-create]').prop('disabled', false);
        });

        // Handle change event for the "section" select
        $(document).on('change', '[data-repeater-section]', function() {
            var section_id = $(this).val();
            // var parent_id = $('[data-repeater-parent]').val();
            var sectionId = $(this).attr('id');
            var parent_id = $('#' + sectionId.replace('section', 'parent')).val();
            var product_id = sectionId.replace('section', 'product');
            var dateOfReceipt = $('#gregorianDate1').val();
            var returnDate = $('#gregorianDate2').val();
            var status = $('#inputState5').val() || "pending";
            if(section_id){
                getProductData(section_id, product_id, dateOfReceipt, returnDate);
            }else{
                getProductData(parent_id, product_id, dateOfReceipt, returnDate);
                // console.log(parent_id);
            }
        });

        $(document).on('change', '[data-repeater-parent]', function() {
            var parent_id = $(this).val();
            var parentId = $(this).attr('id');
            var section_id = parentId.replace('parent', 'section');
            getSection(parent_id, section_id);
        });

         // Handle change event for the date fields
        // Handle change event for the date fields
        // Handle change event for the date fields and status select
        // $('#gregorianDate1, #gregorianDate2, #inputState5').on('change', function() {
        //     $('[data-repeater-item]').each(function() {
        //         var row = $(this); // Get the current row

        //         var sectionId = row.find('[data-repeater-section]').val(); // Get the selected section ID
        //         var parentId = row.find('[data-repeater-parent]').val(); // Get the parent section ID
        //         var SecId = row.find('[data-repeater-section]').attr('id'); // Get the section select ID
        //         var productID = row.find('[data-repeater-product]').attr('id'); // Get the product select ID
        //         var dateOfReceipt = $('#gregorianDate1').val(); // Get the value of the dateOfReceipt field
        //         var returnDate = $('#gregorianDate2').val(); // Get the value of the returnDate field
        //         var status = $('#inputState5').val() || "pending"; // Get the value of the status field

        //         if (sectionId && (productID || SecId)) {
        //             getProductData(sectionId, productID, dateOfReceipt, returnDate);
        //         } else {
        //             getProductData(parentId, productID, dateOfReceipt, returnDate);
        //         }
        //     });
        // });

});
</script>
@endsection

