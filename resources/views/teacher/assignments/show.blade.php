@extends('teacher.navigation')

@section('content')
@php
  $cls = \App\Models\Classes::find($assignment->class_id);
  $sec = \App\Models\Section::find($assignment->section_id);
  $sub = \App\Models\Subject::find($assignment->subject_id);
@endphp
<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
      <div class="d-flex flex-column">
        <h4>{{ $assignment->title }}</h4>
        <ul class="d-flex align-items-center eBreadcrumb-2">
          <li><a href="{{ route('teacher.assignment_home', ['type'=>'published']) }}">{{ get_phrase('Assignments') }}</a></li>
          <li><a href="#">{{ get_phrase('Submissions') }}</a></li>
        </ul>
      </div>
      <a class="eBtn btn-secondary" href="{{ route('teacher.assignment_home', ['type'=>'published']) }}">{{ get_phrase('Back') }}</a>
    </div>
  </div></div>
</div>

<div class="row mb-3">
  <div class="col-12"><div class="eSection-wrap">
    <p><b>{{ get_phrase('Class') }}:</b> {{ $cls->name ?? '-' }} / {{ $sec->name ?? '-' }} &nbsp;|&nbsp;
       <b>{{ get_phrase('Subject') }}:</b> {{ $sub->name ?? '-' }} &nbsp;|&nbsp;
       <b>{{ get_phrase('Total marks') }}:</b> {{ $assignment->total_marks }} &nbsp;|&nbsp;
       <b>{{ get_phrase('Deadline') }}:</b> {{ $assignment->deadline ? date('d M Y', $assignment->deadline) : '—' }}</p>
    @if($assignment->description)<p class="mb-1"><b>{{ get_phrase('Instructions') }}:</b> {{ $assignment->description }}</p>@endif
    @if($assignment->attachment)
      <a class="eBtn btn-secondary" target="_blank" href="{{ asset('assets/uploads/assignments/'.$assignment->attachment) }}">{{ get_phrase('Download attachment') }}</a>
    @endif
  </div></div>
</div>

<div class="row">
  <div class="col-12"><div class="eSection-wrap">
    <h5 class="mb-3">{{ get_phrase('Student submissions') }}</h5>
    <div class="table-responsive">
      <table class="table eTable eTable-2">
        <thead>
          <tr>
            <th>{{ get_phrase('Student') }}</th>
            <th>{{ get_phrase('Status') }}</th>
            <th>{{ get_phrase('Submission') }}</th>
            <th>{{ get_phrase('Grade & hand back') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($students as $student)
            @php $sm = $submissions[$student->id] ?? null; @endphp
            <tr>
              <td>{{ $student->name }}<br><small class="text-muted">{{ $student->email }}</small></td>
              <td>
                @if(!$sm)
                  <span class="badge bg-secondary">{{ get_phrase('Not submitted') }}</span>
                @elseif($sm->status=='returned')
                  <span class="badge bg-success">{{ get_phrase('Returned') }} — {{ $sm->obtained_marks }}/{{ $assignment->total_marks }}</span>
                @else
                  <span class="badge bg-primary">{{ get_phrase('Submitted') }}</span>
                @endif
              </td>
              <td>
                @if($sm)
                  @if($sm->submission_text)<div>{{ $sm->submission_text }}</div>@endif
                  @if($sm->attachment)<a target="_blank" href="{{ asset('assets/uploads/submissions/'.$sm->attachment) }}">{{ get_phrase('File') }}</a>@endif
                  <small class="text-muted d-block">{{ $sm->submitted_at ? date('d M Y H:i', $sm->submitted_at) : '' }}</small>
                @else — @endif
              </td>
              <td>
                @if($sm)
                  <form method="POST" action="{{ route('teacher.assignment.grade', $sm->id) }}" class="d-flex flex-column gr-5" style="gap:6px;">
                    @csrf
                    <input type="number" name="obtained_marks" min="0" max="{{ $assignment->total_marks }}" value="{{ $sm->obtained_marks }}" placeholder="{{ get_phrase('Marks') }}" class="form-control eForm-control" required style="max-width:120px;">
                    <input type="text" name="feedback" value="{{ $sm->feedback }}" placeholder="{{ get_phrase('Feedback') }}" class="form-control eForm-control">
                    <button class="eBtn btn-primary" type="submit">{{ $sm->status=='returned' ? get_phrase('Update grade') : get_phrase('Hand back') }}</button>
                  </form>
                @else
                  <span class="text-muted">{{ get_phrase('Awaiting submission') }}</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div></div>
</div>
@endsection
