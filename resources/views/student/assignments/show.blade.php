@extends('student.navigation')

@section('content')
@php
  $sub = \App\Models\Subject::find($assignment->subject_id);
  $graded = $submission && $submission->status=='returned';
@endphp
<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
      <div class="d-flex flex-column">
        <h4>{{ $assignment->title }}</h4>
        <ul class="d-flex align-items-center eBreadcrumb-2">
          <li><a href="{{ route('student.assignment_home', ['type'=>'active']) }}">{{ get_phrase('My assignments') }}</a></li>
          <li><a href="#">{{ get_phrase('Detail') }}</a></li>
        </ul>
      </div>
      <a class="eBtn btn-secondary" href="{{ route('student.assignment_home', ['type'=>'active']) }}">{{ get_phrase('Back') }}</a>
    </div>
  </div></div>
</div>

<div class="row">
  <div class="col-lg-8 offset-lg-2">
    <div class="eSection-wrap mb-3">
      <p><b>{{ get_phrase('Subject') }}:</b> {{ $sub->name ?? '-' }} &nbsp;|&nbsp;
         <b>{{ get_phrase('Total marks') }}:</b> {{ $assignment->total_marks }} &nbsp;|&nbsp;
         <b>{{ get_phrase('Deadline') }}:</b> {{ $assignment->deadline ? date('d M Y', $assignment->deadline) : '—' }}</p>
      @if($assignment->description)<p><b>{{ get_phrase('Instructions') }}:</b><br>{{ $assignment->description }}</p>@endif
      @if($assignment->attachment)
        <a class="eBtn btn-secondary" target="_blank" href="{{ asset('assets/uploads/assignments/'.$assignment->attachment) }}">{{ get_phrase('Download attachment') }}</a>
      @endif
    </div>

    @if($graded)
      <div class="eSection-wrap">
        <h5 class="mb-2">{{ get_phrase('Result') }}</h5>
        <p class="mb-1"><span class="badge bg-success" style="font-size:14px;">{{ get_phrase('Grade') }}: {{ $submission->obtained_marks }}/{{ $assignment->total_marks }}</span></p>
        @if($submission->feedback)<p><b>{{ get_phrase('Teacher feedback') }}:</b> {{ $submission->feedback }}</p>@endif
        <hr>
        <p><b>{{ get_phrase('Your submission') }}:</b></p>
        @if($submission->submission_text)<p>{{ $submission->submission_text }}</p>@endif
        @if($submission->attachment)<a target="_blank" href="{{ asset('assets/uploads/submissions/'.$submission->attachment) }}">{{ get_phrase('Your file') }}</a>@endif
      </div>
    @else
      <div class="eSection-wrap">
        <h5 class="mb-3">{{ $submission ? get_phrase('Update your submission') : get_phrase('Submit your work') }}</h5>
        <form method="POST" action="{{ route('student.assignment.submit', $assignment->id) }}" enctype="multipart/form-data">
          @csrf
          <div class="fpb-7">
            <label class="eForm-label" for="submission_text">{{ get_phrase('Answer') }}</label>
            <textarea class="form-control eForm-control" name="submission_text" rows="5" placeholder="{{ get_phrase('Type your answer...') }}">{{ $submission->submission_text ?? '' }}</textarea>
          </div>
          <div class="fpb-7">
            <label class="eForm-label" for="attachment">{{ get_phrase('Attachment (optional)') }}</label>
            <input type="file" class="form-control eForm-control-file" name="attachment">
            @if($submission && $submission->attachment)
              <small class="d-block mt-1">{{ get_phrase('Current') }}: <a target="_blank" href="{{ asset('assets/uploads/submissions/'.$submission->attachment) }}">{{ get_phrase('file') }}</a></small>
            @endif
          </div>
          <button class="btn-form" type="submit">{{ $submission ? get_phrase('Update submission') : get_phrase('Submit assignment') }}</button>
        </form>
      </div>
    @endif
  </div>
</div>
@endsection
