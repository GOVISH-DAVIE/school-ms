@extends('teacher.navigation')

@section('content')
@php
  use App\Models\Routine;

  $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
  $routines = Routine::where([
      'teacher_id' => auth()->user()->id,
      'session_id' => $active_session,
      'school_id'  => auth()->user()->school_id,
  ])->get();

  $slotCount = $routines->count();
@endphp

<style>
  .rt-hero{ background:linear-gradient(135deg,#00955f,#007a4d); color:#fff; border-radius:14px;
    padding:22px 26px; margin-bottom:20px; box-shadow:0 10px 30px rgba(0,149,95,.16); }
  .rt-hero h4{ color:#fff; font-weight:700; margin:0; }
  .rt-hero p{ color:rgba(255,255,255,.9); margin:4px 0 0; font-size:14px; }
  .rt-hero .stat{ background:rgba(255,255,255,.14); padding:6px 12px; border-radius:20px; font-size:13px; }
</style>

<div class="rt-hero d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
  <div>
    <h4>{{ get_phrase('My teaching schedule') }}</h4>
    <p>{{ auth()->user()->name }} · {{ get_phrase('Weekly timetable') }}</p>
  </div>
  <div class="d-flex align-items-center" style="gap:10px;">
    <span class="stat"><i class="bi bi-clock-history"></i> {{ $slotCount }} {{ get_phrase('classes / week') }}</span>
    <div class="text-end" style="font-size:13px;">
      <div style="opacity:.85;">{{ get_phrase('Today') }}</div>
      <div style="font-weight:700; font-size:16px;">{{ date('l, d M') }}</div>
    </div>
  </div>
</div>

@include('partials.timetable', ['routines' => $routines, 'cellShow' => 'class'])
@endsection
