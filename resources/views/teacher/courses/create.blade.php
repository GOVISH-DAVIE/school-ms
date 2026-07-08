<form method="POST" class="d-block" action="{{ route('teacher.addons.course.store') }}" enctype="multipart/form-data">
  @csrf
  <div class="form-row">
    <div class="fpb-7">
      <label class="eForm-label" for="title">{{ get_phrase('Course title') }}</label>
      <input type="text" class="form-control eForm-control" id="title" name="title" placeholder="{{ get_phrase('e.g. Foundations of Anatomy') }}" required>
    </div>

    <div class="fpb-7">
      <label class="eForm-label" for="course_class">{{ get_phrase('Class') }}</label>
      <select class="form-select eForm-select" id="course_class" name="class_id" onchange="courseClassSubjects(this.value)" required>
        <option value="">{{ get_phrase('Select a class') }}</option>
        @foreach($classes as $class)
          <option value="{{ $class->id }}">{{ $class->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="fpb-7">
      <label class="eForm-label" for="course_subject">{{ get_phrase('Subject') }}</label>
      <select class="form-select eForm-select" id="course_subject" name="subject_id" required>
        <option value="">{{ get_phrase('Select a subject') }}</option>
      </select>
    </div>

    <div class="fpb-7">
      <label class="eForm-label" for="description">{{ get_phrase('Description') }}</label>
      <textarea class="form-control eForm-control" name="description" rows="3" placeholder="{{ get_phrase('What is this course about?') }}"></textarea>
    </div>

    <div class="fpb-7">
      <label class="eForm-label" for="thumbnail">{{ get_phrase('Thumbnail (optional)') }}</label>
      <input type="file" class="form-control eForm-control-file" name="thumbnail" accept="image/*">
    </div>

    <div class="fpb-7">
      <label class="eForm-label" for="status">{{ get_phrase('Status') }}</label>
      <select class="form-select eForm-select" name="status">
        <option value="published">{{ get_phrase('Publish now') }}</option>
        <option value="draft">{{ get_phrase('Save as draft') }}</option>
      </select>
    </div>

    <div class="fpb-7 pt-2">
      <button class="btn-form" type="submit">{{ get_phrase('Create course') }}</button>
    </div>
  </div>
</form>

<script type="text/javascript">
  "use strict";
  function courseClassSubjects(classId) {
    $.ajax({
      url: '{{ route('teacher.addons.course.class_subjects') }}',
      data: { class_id: classId },
      success: function (response) { $('#course_subject').html(response); }
    });
  }
</script>
