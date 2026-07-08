@extends('admin.navigation')

@section('content')
@php
  $cls = \App\Models\Classes::find($assignment->class_id);
  $sec = \App\Models\Section::find($assignment->section_id);
  $sub = \App\Models\Subject::find($assignment->subject_id);
  $teacher = \App\Models\User::find($assignment->teacher_id);
@endphp
<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
      <div class="d-flex flex-column">
        <h4>{{ $assignment->title }}</h4>
        <ul class="d-flex align-items-center eBreadcrumb-2">
          <li><a href="{{ route('admin.assignment_home', ['type'=>'published']) }}">{{ get_phrase('Assignments') }}</a></li>
          <li><a href="#">{{ get_phrase('Detail') }}</a></li>
        </ul>
      </div>
      <a class="eBtn btn-secondary" href="{{ route('admin.assignment_home', ['type'=>'published']) }}">{{ get_phrase('Back') }}</a>
    </div>
  </div></div>
</div>

<div class="row mb-3"><div class="col-12"><div class="eSection-wrap">
  <p><b>{{ get_phrase('Teacher') }}:</b> {{ $teacher->name ?? '-' }} &nbsp;|&nbsp;
     <b>{{ get_phrase('Class') }}:</b> {{ $cls->name ?? '-' }} / {{ $sec->name ?? '-' }} &nbsp;|&nbsp;
     <b>{{ get_phrase('Subject') }}:</b> {{ $sub->name ?? '-' }} &nbsp;|&nbsp;
     <b>{{ get_phrase('Total marks') }}:</b> {{ $assignment->total_marks }}</p>
  @if($assignment->description)<p><b>{{ get_phrase('Instructions') }}:</b> {{ $assignment->description }}</p>@endif
</div></div></div>

<div class="row"><div class="col-12"><div class="eSection-wrap">
  <h5 class="mb-3">{{ get_phrase('Submissions') }}</h5>
  <div class="table-responsive">
    <table class="table eTable eTable-2">
      <thead><tr>
        <th>{{ get_phrase('Student') }}</th>
        <th>{{ get_phrase('Status') }}</th>
        <th>{{ get_phrase('Marks') }}</th>
        <th>{{ get_phrase('Feedback') }}</th>
      </tr></thead>
      <tbody>
        @foreach($students as $student)
          @php $sm = $submissions[$student->id] ?? null; @endphp
          <tr>
            <td>{{ $student->name }}</td>
            <td>
              @if(!$sm)<span class="badge bg-secondary">{{ get_phrase('Not submitted') }}</span>
              @elseif($sm->status=='returned')<span class="badge bg-success">{{ get_phrase('Returned') }}</span>
              @else<span class="badge bg-primary">{{ get_phrase('Submitted') }}</span>@endif
            </td>
            <td>{{ $sm && $sm->status=='returned' ? $sm->obtained_marks.'/'.$assignment->total_marks : '—' }}</td>
            <td>{{ $sm->feedback ?? '—' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div></div></div>
@endsection
