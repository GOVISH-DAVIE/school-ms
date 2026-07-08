@extends('admin.navigation')

@section('content')
<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
      <div class="d-flex flex-column">
        <h4>{{ get_phrase('Class routines') }}</h4>
        <ul class="d-flex align-items-center eBreadcrumb-2">
          <li><a href="#">{{ get_phrase('Home') }}</a></li>
          <li><a href="#">{{ get_phrase('Academic') }}</a></li>
          <li><a href="#">{{ get_phrase('Routines') }}</a></li>
        </ul>
      </div>
      <div class="export-btn-area">
        <a href="javascript:;" class="export_btn" onclick="rightModal('{{ route('admin.routine.open_modal') }}', '{{ get_phrase('Add class routine') }}')"><i class="bi bi-plus"></i>{{ get_phrase('Add class routine') }}</a>
      </div>
    </div>
  </div></div>
</div>

<div class="row mb-3"><div class="col-12"><div class="eSection-wrap">
  <form method="GET" action="{{ route('admin.routine.routine_list') }}" class="row g-2 align-items-end">
    <div class="col-md-4">
      <label class="eForm-label">{{ get_phrase('Class') }}</label>
      <select name="class_id" id="class_id" class="form-select eForm-select" required onchange="classWiseSection(this.value)">
        <option value="">{{ get_phrase('Select a class') }}</option>
        @foreach($classes as $class)
          <option value="{{ $class->id }}">{{ $class->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-4">
      <label class="eForm-label">{{ get_phrase('Section') }}</label>
      <select name="section_id" id="section_id" class="form-select eForm-select" required>
        <option value="">{{ get_phrase('Select a section') }}</option>
      </select>
    </div>
    <div class="col-md-2">
      <button class="eBtn btn-primary w-100" type="submit">{{ get_phrase('View routine') }}</button>
    </div>
  </form>
</div></div></div>

<div class="eSection-wrap text-center text-muted py-5">
  <i class="bi bi-calendar-week" style="font-size:34px; color:#d3d8e0;"></i>
  <p class="mb-0 mt-2">{{ get_phrase('Select a class and section to view or edit its weekly routine.') }}</p>
</div>

<script type="text/javascript">
  "use strict";
  function classWiseSection(classId) {
    let url = "{{ route('admin.class_wise_sections', ['id' => ':classId']) }}";
    url = url.replace(":classId", classId);
    $.ajax({ url: url, success: function (response) { $('#section_id').html(response); } });
  }
</script>
@endsection
