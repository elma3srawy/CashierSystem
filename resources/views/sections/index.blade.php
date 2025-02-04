@extends('layouts.master')
@section('title' , 'الاقسام')
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
        <h2 class="mb-2 page-title">عرض جميع الاقسام</h2>
        <div class="row my-4">
            <div class="col-md-12 text-left mb-2">
                <a data-toggle="modal" data-target="#varyModal" class="btn btn-primary fe fe-24 fe-plus" href="#"><span class="ml-1 item-text">اضافة قسم جديد</span>
                </a>
            </div>
          <!-- Small table -->
          <div class="col-md-12">
            <div class="card shadow">
              <div class="card-body">
                <!-- table -->
                <table class="table datatables" id="dataTable-1">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>اسم القسم</th>
                      <th>عدد الاقسام الفرعية</th>
                      <th>عدد المنتجات</th>
                      <th>الحالة</th>
                      <th>تاريخ الانشاء</th>
                      <th class="text-center">العملية</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($sections as $section)
                    <tr>
                        <td>{{ $loop->index + 1 }}</td>
                        <td>{{ $section->name }}</td>
                        <td>
                            @if ($section->subSection()->count() > 0)
                                <a style="text-decoration: none;" href="{{ route('section.show' , $section->id) }}"><i class="fe fe-16 fe-eye"></i>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fe fe-16 fe-arrow-left"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: red">{{ $section->subSection()->count() }}</span></a>
                            @else
                                <b>{{ "لا يوجد"}}</b>
                            @endif
                        </td>
                        <td>
                            @if ($section->products()->count() > 0)
                                <a style="text-decoration: none;" href="{{ route('products.index' , $section->id) }}"><i class="fe fe-16 fe-eye"></i>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fe fe-16 fe-arrow-left"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: red">{{ $section->products()->count() }}</span></a>
                            @else
                                <b>{{ "لا يوجد"}}</b>
                            @endif
                        </td>
                        @if ($section->status)
                        <td style="font-size: 18px; font-weight: bold; color: green; text-decoration: none;">{{ "ايجار" }}</td>
                        @else
                            <td style="font-size: 18px; font-weight: bold; color: red; text-decoration: none;">{{ "بيع" }}</td>
                        @endif
                        <td>{{ $section->created_at->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('section.edit' , $section->id) }}">
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        <i class="fe fe-16 fe-edit"></i>&nbsp;تعديل
                                    </button>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-toggle="modal" data-target="#verticalModal" data-section-id="{{ $section->id }}" disabled>
                                    <i class="fe fe-16 fe-delete"></i>&nbsp;حذف
                                </button>
                            </div>
                        </td>
                    </tr>
                     @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div> <!-- simple table -->
        </div> <!-- end section -->
      </div> <!-- .col-12 -->
    </div> <!-- .row -->
  </div> <!-- .container-fluid -->


<div class="modal fade" id="verticalModal" tabindex="-1" role="dialog" aria-labelledby="verticalModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verticalModalTitle">حذف قسم</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">هل انت متاكد من حذف القسم</div>
            <div class="modal-footer">
                <button type="button" class="btn mb-2 btn-secondary" data-dismiss="modal">غلق</button>
                <form id="delete-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn mb-2 btn btn-danger">حذف</button>
                </form>
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
    <script src='/js/jquery.dataTables.min.js'></script>
    <script src='/js/dataTables.bootstrap4.min.js'></script>
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
<script>
$(document).ready(function() {
    // Enable all buttons with class "delete-btn"
    $('.delete-btn').prop('disabled', false);

    // Enable the button when the modal is shown
    $('#verticalModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var sectionId = button.data('section-id');
        var form = $('#delete-form');
        form.attr('action', '{{ route("section.destroy", ":section") }}'.replace(':section', sectionId));
        $('#delete-form button[type="submit"]').removeClass('disabled').prop('disabled', false);
    });
});
</script>
@endsection
