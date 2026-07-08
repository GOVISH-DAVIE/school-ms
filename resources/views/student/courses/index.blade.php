@extends('student.navigation')

@section('content')
<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex flex-column">
      <h4>{{ get_phrase('My courses') }}</h4>
      <ul class="d-flex align-items-center eBreadcrumb-2">
        <li><a href="#">{{ get_phrase('Home') }}</a></li>
        <li><a href="#">{{ get_phrase('Courses') }}</a></li>
      </ul>
    </div>
  </div></div>
</div>

<div class="row">
  @forelse($courses as $course)
    @php
      $sub = \App\Models\Subject::find($course->subject_id);
      $topicCount = \App\Models\CourseTopic::where('course_id',$course->id)->count();
      $lessonCount = \App\Models\CourseLesson::where('course_id',$course->id)->count();
      $teacher = \App\Models\User::find($course->teacher_id);
    @endphp
    <div class="col-xl-4 col-md-6 mb-4">
      <div class="eSection-wrap h-100 d-flex flex-column click-card"
           onclick="window.location.href='{{ route('student.addons.course.view', $course->id) }}'">
        <h5 class="mb-1">{{ $course->title }}</h5>
        <p class="text-muted mb-2" style="font-size:13px;">{{ $sub->name ?? '' }} &middot; {{ $teacher->name ?? '' }}</p>
        @if($course->description)<p style="font-size:13px;">{{ \Illuminate\Support\Str::limit($course->description, 90) }}</p>@endif
        <p class="mb-3" style="font-size:13px;">
          <span class="badge bg-primary">{{ $topicCount }} {{ get_phrase('topics') }}</span>
          <span class="badge bg-primary">{{ $lessonCount }} {{ get_phrase('lessons') }}</span>
        </p>
        <div class="mt-auto">
          <a class="eBtn btn-primary" href="{{ route('student.addons.course.view', $course->id) }}">{{ get_phrase('Open course') }}</a>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12"><div class="eSection-wrap text-center">{{ get_phrase('No courses available for your class yet.') }}</div></div>
  @endforelse
</div>

@if(method_exists($courses,'links'))<div class="mt-2">{{ $courses->links() }}</div>@endif
@endsection
