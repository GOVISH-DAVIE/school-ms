@extends('admin.navigation')

@section('content')
<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex flex-column">
      <h4>{{ get_phrase('Assignments') }}</h4>
      <ul class="d-flex align-items-center eBreadcrumb-2">
        <li><a href="#">{{ get_phrase('Home') }}</a></li>
        <li><a href="#">{{ get_phrase('Assignments') }}</a></li>
      </ul>
    </div>
  </div></div>
</div>

<div class="row">
  <div class="col-12"><div class="eSection-wrap">

    <form method="GET" action="{{ route('admin.assignment_home', ['type'=>'published']) }}" class="row mb-3">
      <div class="col-md-4">
        <select name="class_id" id="class_id" class="form-select eForm-select" onchange="classWiseSection(this.value)">
          <option value="">{{ get_phrase('All classes') }}</option>
          @foreach($classes as $class)
            <option value="{{ $class->id }}" {{ (string)$class_id===(string)$class->id?'selected':'' }}>{{ $class->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <select name="section_id" id="section_id" class="form-select eForm-select">
          <option value="">{{ get_phrase('All sections') }}</option>
        </select>
      </div>
      <div class="col-md-2">
        <button class="eBtn btn-primary" type="submit">{{ get_phrase('Filter') }}</button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table eTable eTable-2">
        <thead>
          <tr>
            <th>{{ get_phrase('Title') }}</th>
            <th>{{ get_phrase('Teacher') }}</th>
            <th>{{ get_phrase('Class / Section') }}</th>
            <th>{{ get_phrase('Subject') }}</th>
            <th>{{ get_phrase('Deadline') }}</th>
            <th>{{ get_phrase('Submissions') }}</th>
            <th>{{ get_phrase('Options') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($assignments as $assignment)
            @php
              $roster = \App\Models\Enrollment::where('class_id',$assignment->class_id)->where('section_id',$assignment->section_id)->where('school_id',$assignment->school_id)->count();
              $submitted = \App\Models\AssignmentSubmission::where('assignment_id',$assignment->id)->count();
              $teacher = \App\Models\User::find($assignment->teacher_id);
              $cls = \App\Models\Classes::find($assignment->class_id);
              $sec = \App\Models\Section::find($assignment->section_id);
              $sub = \App\Models\Subject::find($assignment->subject_id);
            @endphp
            <tr>
              <td>{{ $assignment->title }}</td>
              <td>{{ $teacher->name ?? '-' }}</td>
              <td>{{ $cls->name ?? '-' }} / {{ $sec->name ?? '-' }}</td>
              <td>{{ $sub->name ?? '-' }}</td>
              <td>{{ $assignment->deadline ? date('d M Y', $assignment->deadline) : '—' }}</td>
              <td><span class="badge bg-primary">{{ $submitted }}/{{ $roster }}</span></td>
              <td><a class="eBtn btn-secondary" href="{{ route('admin.assignment.show', $assignment->id) }}">{{ get_phrase('View') }}</a></td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center">{{ get_phrase('No assignments found.') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-3">{{ $assignments->appends(request()->query())->links() }}</div>
  </div></div>
</div>

<script type="text/javascript">
  "use strict";
  function classWiseSection(classId){
    if(!classId){ $('#section_id').html('<option value="">{{ get_phrase('All sections') }}</option>'); return; }
    $.ajax({
      url: '{{ route('admin.assignment.sections') }}',
      data: { class_id: classId },
      success: function(r){ $('#section_id').html(r); }
    });
  }
</script>
@endsection
