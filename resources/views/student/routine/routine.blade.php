@extends('student.navigation')

@section('content')
@php
  use App\Models\Routine;
  use App\Models\Classes;
  use App\Models\Section;

  $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
  $routines = Routine::where([
      'class_id' => $class_id, 'section_id' => $section_id,
      'session_id' => $active_session, 'school_id' => auth()->user()->school_id,
  ])->get();

  $clsName = optional(Classes::find($class_id))->name;
  $secName = optional(Section::find($section_id))->name;
@endphp

<style>
  .rt-hero{ background:linear-gradient(135deg,#00955f,#007a4d); color:#fff; border-radius:14px;
    padding:22px 26px; margin-bottom:20px; box-shadow:0 10px 30px rgba(0,149,95,.16); }
  .rt-hero h4{ color:#fff; font-weight:700; margin:0; }
  .rt-hero p{ color:rgba(255,255,255,.9); margin:4px 0 0; font-size:14px; }
</style>

<div class="rt-hero d-flex justify-content-between align-items-center flex-wrap" style="gap:10px;">
  <div>
    <h4>{{ get_phrase('My class routine') }}</h4>
    <p>{{ $clsName }}{{ $secName ? ' · '.get_phrase('Section').' '.$secName : '' }} · {{ get_phrase('Weekly timetable') }}</p>
  </div>
  <div class="text-end" style="font-size:13px;">
    <div style="opacity:.85;">{{ get_phrase('Today') }}</div>
    <div style="font-weight:700; font-size:16px;">{{ date('l, d M') }}</div>
  </div>
</div>

@include('partials.timetable', ['routines' => $routines, 'cellShow' => 'teacher'])
@endsection
