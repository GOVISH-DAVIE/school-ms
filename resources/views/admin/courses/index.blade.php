@extends('admin.navigation')

@section('content')
<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex flex-column">
      <h4>{{ get_phrase('Courses') }}</h4>
      <ul class="d-flex align-items-center eBreadcrumb-2">
        <li><a href="#">{{ get_phrase('Home') }}</a></li>
        <li><a href="#">{{ get_phrase('Courses') }}</a></li>
      </ul>
    </div>
  </div></div>
</div>

<div class="row"><div class="col-12"><div class="eSection-wrap">
  <form method="GET" action="{{ route('admin.addons.courses') }}" class="row mb-3">
    <div class="col-md-4">
      <select name="class_id" class="form-select eForm-select">
        <option value="">{{ get_phrase('All classes') }}</option>
        @foreach($classes as $class)
          <option value="{{ $class->id }}" {{ (string)$class_id===(string)$class->id?'selected':'' }}>{{ $class->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2"><button class="eBtn btn-primary" type="submit">{{ get_phrase('Filter') }}</button></div>
  </form>

  <div class="table-responsive">
    <table class="table eTable eTable-2">
      <thead><tr>
        <th>{{ get_phrase('Course') }}</th>
        <th>{{ get_phrase('Teacher') }}</th>
        <th>{{ get_phrase('Class') }}</th>
        <th>{{ get_phrase('Subject') }}</th>
        <th>{{ get_phrase('Content') }}</th>
        <th>{{ get_phrase('Status') }}</th>
        <th>{{ get_phrase('Options') }}</th>
      </tr></thead>
      <tbody>
        @forelse($courses as $course)
          @php
            $teacher = \App\Models\User::find($course->teacher_id);
            $cls = \App\Models\Classes::find($course->class_id);
            $sub = \App\Models\Subject::find($course->subject_id);
            $t = \App\Models\CourseTopic::where('course_id',$course->id)->count();
            $l = \App\Models\CourseLesson::where('course_id',$course->id)->count();
          @endphp
          <tr>
            <td>{{ $course->title }}</td>
            <td>{{ $teacher->name ?? '-' }}</td>
            <td>{{ $cls->name ?? '-' }}</td>
            <td>{{ $sub->name ?? '-' }}</td>
            <td><span class="badge bg-primary">{{ $t }} {{ get_phrase('topics') }}</span> <span class="badge bg-primary">{{ $l }} {{ get_phrase('lessons') }}</span></td>
            <td><span class="badge {{ $course->status=='published'?'bg-success':'bg-secondary' }}">{{ ucfirst($course->status) }}</span></td>
            <td><a class="eBtn btn-secondary" href="{{ route('admin.addons.course.view', $course->id) }}">{{ get_phrase('View') }}</a></td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center">{{ get_phrase('No courses found.') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-3">{{ $courses->appends(request()->query())->links() }}</div>
</div></div></div>
@endsection
