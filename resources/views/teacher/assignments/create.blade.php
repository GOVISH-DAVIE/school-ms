<form method="POST" class="d-block" action="{{ route('teacher.assignment.store') }}" enctype="multipart/form-data">
  @csrf
  <div class="form-row">
    <div class="fpb-7">
      <label class="eForm-label" for="title">{{ get_phrase('Title') }}</label>
      <input type="text" class="form-control eForm-control" id="title" name="title" placeholder="{{ get_phrase('Assignment title') }}" required>
    </div>

    <div class="fpb-7">
      <label class="eForm-label" for="class_id_on_create">{{ get_phrase('Class') }}</label>
      <select class="form-select eForm-select" id="class_id_on_create" name="class_id" onchange="classWiseSectionSubject(this.value)" required>
        <option value="">{{ get_phrase('Select a class') }}</option>
        @foreach($classes as $class)
          <option value="{{ $class->id }}">{{ $class->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="fpb-7">
      <label class="eForm-label" for="section_id_on_create">{{ get_phrase('Section') }}</label>
      <select class="form-select eForm-select" id="section_id_on_create" name="section_id" required>
        <option value="">{{ get_phrase('Select a section') }}</option>
      </select>
    </div>

    <div class="fpb-7">
      <label class="eForm-label" for="subject_id_on_create">{{ get_phrase('Subject') }}</label>
      <select class="form-select eForm-select" id="subject_id_on_create" name="subject_id" required>
        <option value="">{{ get_phrase('Select a subject') }}</option>
      </select>
    </div>

    <div class="fpb-7">
      <label class="eForm-label" for="description">{{ get_phrase('Instructions') }}</label>
      <textarea class="form-control eForm-control" id="description" name="description" rows="3" placeholder="{{ get_phrase('What should students do?') }}"></textarea>
    </div>

    <div class="fpb-7">
      <label class="eForm-label" for="total_marks">{{ get_phrase('Total marks') }}</label>
      <input type="number" min="1" class="form-control eForm-control" id="total_marks" name="total_marks" value="100" required>
    </div>

    <div class="fpb-7">
      <label class="eForm-label" for="deadline">{{ get_phrase('Deadline') }}</label>
      <input type="date" class="form-control eForm-control" id="deadline" name="deadline">
    </div>

    <div class="fpb-7">
      <label class="eForm-label" for="attachment">{{ get_phrase('Attachment (optional)') }}</label>
      <div class="custom-file-upload">
        <input type="file" class="form-control eForm-control-file" id="attachment" name="attachment">
      </div>
    </div>

    <div class="fpb-7">
      <label class="eForm-label" for="status">{{ get_phrase('Status') }}</label>
      <select class="form-select eForm-select" id="status" name="status">
        <option value="published">{{ get_phrase('Publish now') }}</option>
        <option value="draft">{{ get_phrase('Save as draft') }}</option>
      </select>
    </div>

    <div class="fpb-7 pt-2">
      <button class="btn-form" type="submit">{{ get_phrase('Create assignment') }}</button>
    </div>
  </div>
</form>

<script type="text/javascript">
  "use strict";
  function classWiseSectionSubject(classId) {
    $.ajax({
      url: '{{ route('teacher.assignment.class_sections') }}',
      data: { class_id: classId },
      success: function (response) { $('#section_id_on_create').html(response); }
    });
    $.ajax({
      url: '{{ route('teacher.assignment.class_subjects') }}',
      data: { class_id: classId },
      success: function (response) { $('#subject_id_on_create').html(response); }
    });
  }
</script>
