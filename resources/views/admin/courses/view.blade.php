@extends('admin.navigation')

@section('content')
@php
  $cls = \App\Models\Classes::find($course->class_id);
  $sub = \App\Models\Subject::find($course->subject_id);
  $teacher = \App\Models\User::find($course->teacher_id);
@endphp
<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
      <div class="d-flex flex-column">
        <h4>{{ $course->title }}</h4>
        <ul class="d-flex align-items-center eBreadcrumb-2">
          <li><a href="{{ route('admin.addons.courses') }}">{{ get_phrase('Courses') }}</a></li>
          <li><a href="#">{{ get_phrase('Detail') }}</a></li>
        </ul>
      </div>
      <a class="eBtn btn-secondary" href="{{ route('admin.addons.courses') }}">{{ get_phrase('Back') }}</a>
    </div>
  </div></div>
</div>

<div class="row mb-3"><div class="col-12"><div class="eSection-wrap">
  <p class="mb-0"><b>{{ get_phrase('Teacher') }}:</b> {{ $teacher->name ?? '-' }} &nbsp;|&nbsp;
     <b>{{ get_phrase('Class') }}:</b> {{ $cls->name ?? '-' }} &nbsp;|&nbsp;
     <b>{{ get_phrase('Subject') }}:</b> {{ $sub->name ?? '-' }} &nbsp;|&nbsp;
     <b>{{ get_phrase('Status') }}:</b> {{ ucfirst($course->status) }}</p>
</div></div></div>

<div class="row"><div class="col-12">
  @forelse($course->topics as $topic)
    <div class="eSection-wrap mb-3">
      <h5 class="mb-3"><i class="bi bi-collection"></i> {{ $topic->title }}</h5>
      @forelse($topic->lessons as $lesson)
        <div class="mb-3 pb-2" style="border-bottom:1px solid #f0f0f0;">
          <h6 class="mb-1">{{ $lesson->title }}</h6>
          @if($lesson->content)<div class="mb-2">{!! $lesson->content !!}</div>@endif
          @foreach($lesson->materials as $m)
            <div class="py-1"><span class="badge bg-secondary">{{ ucfirst($m->type) }}</span>
              @if($m->type=='file')
                <a target="_blank" href="{{ asset('assets/uploads/course_materials/'.$m->file) }}">{{ $m->title }}</a>
              @else
                <a target="_blank" href="{{ $m->url }}">{{ $m->title }}</a>
              @endif
            </div>
          @endforeach
        </div>
      @empty
        <p class="text-muted mb-0">{{ get_phrase('No lessons.') }}</p>
      @endforelse
    </div>
  @empty
    <div class="eSection-wrap text-center">{{ get_phrase('No content yet.') }}</div>
  @endforelse
</div></div>
@endsection
