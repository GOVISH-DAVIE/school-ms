@extends('teacher.navigation')

@section('content')
@php
  $cls = \App\Models\Classes::find($quiz->class_id);
  $sec = \App\Models\Section::find($quiz->section_id);
  $sub = \App\Models\Subject::find($quiz->subject_id);
@endphp
<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
      <div class="d-flex flex-column">
        <h4>{{ $quiz->title }} <span class="badge bg-primary">{{ get_phrase('Quiz') }}</span></h4>
        <ul class="d-flex align-items-center eBreadcrumb-2">
          <li><a href="{{ route('teacher.assignment_home', ['type'=>'published']) }}">{{ get_phrase('Assignments') }}</a></li>
          <li><a href="#">{{ get_phrase('Quiz submissions') }}</a></li>
        </ul>
      </div>
      <a class="eBtn btn-secondary" href="{{ route('teacher.assignment_home', ['type'=>'published']) }}">{{ get_phrase('Back') }}</a>
    </div>
  </div></div>
</div>

<div class="row mb-3"><div class="col-12"><div class="eSection-wrap">
  <p class="mb-0"><b>{{ get_phrase('Class') }}:</b> {{ $cls->name ?? '-' }} / {{ $sec->name ?? '-' }} &nbsp;|&nbsp;
     <b>{{ get_phrase('Subject') }}:</b> {{ $sub->name ?? '-' }} &nbsp;|&nbsp;
     <b>{{ get_phrase('Questions') }}:</b> {{ $links->count() }} &nbsp;|&nbsp;
     <b>{{ get_phrase('Total marks') }}:</b> {{ $quiz->total_marks }} &nbsp;|&nbsp;
     <b>{{ get_phrase('Deadline') }}:</b> {{ $quiz->deadline ? date('d M Y', $quiz->deadline) : '—' }}</p>
</div></div></div>

<div class="row"><div class="col-12"><div class="eSection-wrap">
  <h5 class="mb-3">{{ get_phrase('Student submissions') }}</h5>
  <div class="table-responsive">
    <table class="table eTable eTable-2">
      <thead><tr>
        <th>{{ get_phrase('Student') }}</th>
        <th>{{ get_phrase('Status') }}</th>
        <th>{{ get_phrase('Score') }}</th>
        <th>{{ get_phrase('Options') }}</th>
      </tr></thead>
      <tbody>
        @foreach($students as $student)
          @php $sm = $submissions[$student->id] ?? null; @endphp
          <tr>
            <td>{{ $student->name }}<br><small class="text-muted">{{ $student->email }}</small></td>
            <td>
              @if(!$sm)<span class="badge bg-secondary">{{ get_phrase('Not attempted') }}</span>
              @elseif($sm->status=='returned')<span class="badge bg-success">{{ get_phrase('Graded') }}</span>
              @else<span class="badge bg-primary">{{ get_phrase('Awaiting review') }}</span>@endif
            </td>
            <td>{{ $sm ? $sm->obtained_marks.'/'.$quiz->total_marks : '—' }}</td>
            <td>
              @if($sm)
                <a class="eBtn btn-primary" href="{{ route('teacher.quiz.grade_view', $sm->id) }}">{{ $sm->status=='returned' ? get_phrase('Review') : get_phrase('Review & grade') }}</a>
              @else <span class="text-muted">{{ get_phrase('No attempt') }}</span> @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div></div></div>
@endsection
