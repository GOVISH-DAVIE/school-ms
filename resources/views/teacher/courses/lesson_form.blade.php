@php $isEdit = $lesson !== null; @endphp
<form method="POST" class="d-block"
      action="{{ $isEdit ? route('teacher.addons.course.lesson.update', $lesson->id) : route('teacher.addons.course.lesson.store') }}">
  @csrf
  @unless($isEdit)
    <input type="hidden" name="topic_id" value="{{ $topic->id }}">
  @endunless

  <div class="form-row">
    <div class="fpb-7">
      <label class="eForm-label" for="lesson_title">{{ get_phrase('Lesson title') }}</label>
      <input type="text" class="form-control eForm-control" id="lesson_title" name="title"
             value="{{ $isEdit ? $lesson->title : '' }}" placeholder="{{ get_phrase('e.g. The skeletal system') }}" required>
    </div>

    <div class="fpb-7">
      <label class="eForm-label" for="lesson_content">{{ get_phrase('Lesson content') }}</label>
      <textarea id="lesson_content" name="content" class="form-control">{!! $isEdit ? $lesson->content : '' !!}</textarea>
    </div>

    <div class="fpb-7 pt-2">
      <button class="btn-form" type="submit">{{ $isEdit ? get_phrase('Update lesson') : get_phrase('Add lesson') }}</button>
    </div>
  </div>
</form>

<script type="text/javascript">
  "use strict";
  $(function () {
    if ($.fn.summernote) {
      $('#lesson_content').summernote({
        height: 220,
        toolbar: [
          ['style', ['bold', 'italic', 'underline', 'clear']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['insert', ['link']],
          ['view', ['codeview']]
        ]
      });
    }
  });
</script>
