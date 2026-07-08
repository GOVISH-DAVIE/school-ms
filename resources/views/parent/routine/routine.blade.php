@extends('parent.navigation')

@section('content')
@php
  use App\Models\Routine;
  use App\Models\Enrollment;
  use App\Models\Classes;
  use App\Models\Section;
  use App\Models\User;

  // resolve selected child
  $selected = request('student_id');
  if (!$selected && count($student_data)) {
      $selected = $student_data[0]['user_id'] ?? ($student_data[0]['id'] ?? null);
  }

  $routines = collect();
  $childName = null; $clsName = null; $secName = null;
  if ($selected) {
      $enroll = Enrollment::where('user_id', $selected)->first();
      $childName = optional(User::find($selected))->name;
      if ($enroll) {
          $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
          $routines = Routine::where([
              'class_id' => $enroll->class_id, 'section_id' => $enroll->section_id,
              'session_id' => $active_session, 'school_id' => auth()->user()->school_id,
          ])->get();
          $clsName = optional(Classes::find($enroll->class_id))->name;
          $secName = optional(Section::find($enroll->section_id))->name;
      }
  }
@endphp

<style>
  .rt-hero{ background:linear-gradient(135deg,#00955f,#007a4d); color:#fff; border-radius:14px;
    padding:22px 26px; margin-bottom:20px; box-shadow:0 10px 30px rgba(0,149,95,.16); }
  .rt-hero h4{ color:#fff; font-weight:700; margin:0; }
  .rt-hero p{ color:rgba(255,255,255,.9); margin:4px 0 0; font-size:14px; }
  .rt-hero select{ border:1px solid rgba(255,255,255,.4); background:rgba(255,255,255,.15); color:#fff; border-radius:8px; padding:8px 12px; }
  .rt-hero select option{ color:#181c32; }
</style>

<div class="rt-hero d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
  <div>
    <h4>{{ get_phrase('Class routine') }}</h4>
    <p>{{ $childName ? $childName.' · ' : '' }}{{ $clsName }}{{ $secName ? ' · '.get_phrase('Section').' '.$secName : '' }}</p>
  </div>
  <form method="GET" action="{{ route('parent.routine') }}">
    <select name="student_id" onchange="this.form.submit()">
      @foreach($student_data as $child)
        @php $cid = $child['user_id'] ?? ($child['id'] ?? null); @endphp
        <option value="{{ $cid }}" {{ (string)$selected===(string)$cid ? 'selected' : '' }}>{{ $child['name'] ?? '' }}</option>
      @endforeach
    </select>
  </form>
</div>

@if(count($student_data) === 0)
  <div class="eSection-wrap text-center text-muted py-4">{{ get_phrase('No children linked to your account.') }}</div>
@else
  @include('partials.timetable', ['routines' => $routines, 'cellShow' => 'teacher'])
@endif
@endsection
