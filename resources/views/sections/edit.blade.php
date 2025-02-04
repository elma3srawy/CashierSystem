@extends('layouts.master')
@section('title' , 'تعديل علي القسم')
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

<div class="card my-4">
    <div class="card-body">
        <form id="example-form" action="{{ route('section.update' , $section->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div>
                <h3>تعديل القسم</h3>
                <section>
                    <div class="form-group">
                        <label for="name">* اسم القسم</label>
                        <input id="name" required name="name" type="text" class="form-control required" value="{{ $section->name }}">
                    </div>
                    <div class="form-group">
                        <label for="title">* العنوان</label>
                        <input id="title" name="title" type="text" class="form-control required" value="{{ $section->title }}">
                    </div>
                    @if(!$section->section_id)
                    <div class="form-group">
                        <label for="status">* الحالة</label>
                        <select id="status" name="status" class="form-control required">
                            <option value="1" {{ $section->status == 1 ? 'selected' : '' }}>ايجار</option>
                            <option value="0" {{ $section->status == 0 ? 'selected' : '' }}>بيع</option>
                        </select>
                    </div>
                    @endif
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </section>
            </div>
        </form>
    </div> <!-- .card-body -->
</div> <!-- .card -->
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
@endsection