<form method="POST" class="d-block" action="{{ route('teacher.qbank.generate') }}">
  @csrf
  <div class="form-row">
    <div class="fpb-7">
      <label class="eForm-label">{{ get_phrase('Quiz title') }}</label>
      <input type="text" class="form-control eForm-control" name="title" placeholder="{{ get_phrase('e.g. Anatomy — Week 2 quiz') }}" required>
    </div>
    <div class="fpb-7">
      <label class="eForm-label">{{ get_phrase('Class') }}</label>
      <select class="form-select eForm-select" id="gen_class" name="class_id" onchange="genClassLookups(this.value)" required>
        <option value="">{{ get_phrase('Select a class') }}</option>
        @foreach($classes as $class)
          <option value="{{ $class->id }}">{{ $class->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="fpb-7">
      <label class="eForm-label">{{ get_phrase('Section') }}</label>
      <select class="form-select eForm-select" id="gen_section" name="section_id" required>
        <option value="">{{ get_phrase('Select a section') }}</option>
      </select>
    </div>
    <div class="fpb-7">
      <label class="eForm-label">{{ get_phrase('Subject (pull questions from)') }}</label>
      <select class="form-select eForm-select" id="gen_subject" name="subject_id" required>
        <option value="">{{ get_phrase('Select a subject') }}</option>
      </select>
    </div>
    <div class="fpb-7 d-flex" style="gap:10px;">
      <div style="flex:1;">
        <label class="eForm-label">{{ get_phrase('Difficulty') }}</label>
        <select class="form-select eForm-select" name="difficulty">
          <option value="">{{ get_phrase('Any') }}</option>
          @foreach(['easy','medium','hard'] as $d)<option value="{{ $d }}">{{ ucfirst($d) }}</option>@endforeach
        </select>
      </div>
      <div style="flex:1;">
        <label class="eForm-label">{{ get_phrase('Type') }}</label>
        <select class="form-select eForm-select" name="qtype">
          <option value="">{{ get_phrase('Any') }}</option>
          <option value="mcq">MCQ</option>
          <option value="truefalse">True/False</option>
          <option value="short">Short</option>
          <option value="essay">Essay</option>
        </select>
      </div>
    </div>
    <div class="fpb-7">
      <label class="eForm-label">{{ get_phrase('Topic filter (optional)') }}</label>
      <input type="text" class="form-control eForm-control" name="topic" placeholder="{{ get_phrase('Leave blank for all topics') }}">
    </div>
    <div class="fpb-7 d-flex" style="gap:10px;">
      <div style="flex:1;">
        <label class="eForm-label">{{ get_phrase('Number of questions') }}</label>
        <input type="number" min="1" class="form-control eForm-control" name="count" value="5" required>
      </div>
      <div style="flex:1;">
        <label class="eForm-label">{{ get_phrase('Time limit (minutes)') }}</label>
        <input type="number" min="1" class="form-control eForm-control" name="duration_minutes" placeholder="{{ get_phrase('optional') }}">
      </div>
    </div>
    <div class="fpb-7">
      <label class="eForm-label">{{ get_phrase('Deadline / closes at') }}</label>
      <input type="datetime-local" class="form-control eForm-control" name="deadline">
      <small class="text-muted" style="font-size:11px;">{{ get_phrase('Optional. A time limit starts a countdown when the student opens the exam; a deadline closes it for everyone at that moment. You can set either, both, or neither.') }}</small>
    </div>
    <div class="fpb-7 pt-2">
      <button class="btn-form" type="submit">{{ get_phrase('Generate quiz') }}</button>
    </div>
    <p class="text-muted mt-2" style="font-size:12px;">{{ get_phrase('Questions are drawn at random from the bank matching your filters.') }}</p>
  </div>
</form>

<script type="text/javascript">
  "use strict";
  function genClassLookups(classId) {
    $.ajax({ url: '{{ route('teacher.qbank.class_subjects') }}', data: { class_id: classId }, success: function (r) { $('#gen_subject').html(r); } });
    $.ajax({ url: '{{ route('teacher.qbank.class_sections') }}', data: { class_id: classId }, success: function (r) { $('#gen_section').html(r); } });
  }
</script>
