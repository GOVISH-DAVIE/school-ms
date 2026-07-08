<?php
use App\Models\Routine;
use App\Models\Section;
use App\Models\Classes;

$active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
$routines = ($class_id && $section_id)
    ? Routine::where([
        'class_id' => $class_id, 'section_id' => $section_id,
        'session_id' => $active_session, 'school_id' => auth()->user()->school_id,
      ])->get()
    : collect();
$clsName = $class_id ? optional(Classes::find($class_id))->name : null;
$secName = $section_id ? optional(Section::find($section_id))->name : null;
?>

@extends('admin.navigation')

@section('content')
<style>
  .rt-hero{ background:linear-gradient(135deg,#00955f,#007a4d); color:#fff; border-radius:14px;
    padding:20px 24px; margin-bottom:18px; box-shadow:0 10px 30px rgba(0,149,95,.16); }
  .rt-hero h4{ color:#fff; font-weight:700; margin:0; }
  .rt-hero p{ color:rgba(255,255,255,.9); margin:3px 0 0; font-size:14px; }
</style>

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
          <option value="{{ $class->id }}" {{ $class_id == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-4">
      <label class="eForm-label">{{ get_phrase('Section') }}</label>
      <select name="section_id" id="section_id" class="form-select eForm-select" required>
        <option value="">{{ get_phrase('Select a section') }}</option>
        <?php foreach(Section::where(['class_id' => $class_id])->get() as $section): ?>
          <option value="{{ $section->id }}" {{ $section_id == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <button class="eBtn btn-primary w-100" type="submit">{{ get_phrase('View routine') }}</button>
    </div>
  </form>
</div></div></div>

@if($class_id && $section_id)
  <div class="rt-hero d-flex justify-content-between align-items-center flex-wrap" style="gap:10px;">
    <div>
      <h4>{{ $clsName }}{{ $secName ? ' · '.get_phrase('Section').' '.$secName : '' }}</h4>
      <p>{{ get_phrase('Weekly timetable') }} · {{ $routines->count() }} {{ get_phrase('slots') }}</p>
    </div>
    <div class="text-end" style="font-size:13px;">
      <div style="opacity:.85;">{{ get_phrase('Today') }}</div>
      <div style="font-weight:700; font-size:16px;">{{ date('l, d M') }}</div>
    </div>
  </div>

  <div id="class_routines">
    @include('partials.timetable', ['routines' => $routines, 'cellShow' => 'teacher', 'admin' => true])
  </div>
@else
  <div class="eSection-wrap text-center text-muted py-5">
    <i class="bi bi-calendar-week" style="font-size:34px; color:#d3d8e0;"></i>
    <p class="mb-0 mt-2">{{ get_phrase('Select a class and section to view or edit its routine.') }}</p>
  </div>
@endif

<script type="text/javascript">
  "use strict";
  function classWiseSection(classId) {
    let url = "{{ route('admin.class_wise_sections', ['id' => ':classId']) }}";
    url = url.replace(":classId", classId);
    $.ajax({ url: url, success: function (response) { $('#section_id').html(response); } });
  }
</script>
@endsection
