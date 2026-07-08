@extends('teacher.navigation')

@section('content')
<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
      <div class="d-flex flex-column">
        <h4>{{ get_phrase('Grade quiz') }} — {{ $student->name ?? '' }}</h4>
        <ul class="d-flex align-items-center eBreadcrumb-2">
          <li><a href="{{ route('teacher.quiz.review', $quiz->id) }}">{{ $quiz->title }}</a></li>
          <li><a href="#">{{ get_phrase('Grade') }}</a></li>
        </ul>
      </div>
      <a class="eBtn btn-secondary" href="{{ route('teacher.quiz.review', $quiz->id) }}">{{ get_phrase('Back') }}</a>
    </div>
  </div></div>
</div>

<form method="POST" action="{{ route('teacher.quiz.grade', $submission->id) }}">
  @csrf
  <div class="row"><div class="col-lg-9">
    @foreach($links as $i => $link)
      @php
        $q = $link->question; if(!$q) continue;
        $ans = $answers[$q->id] ?? null;
        $isAuto = in_array($q->type, ['mcq','truefalse']) || ($q->type=='short' && trim((string)$q->correct_answer)!=='');
      @endphp
      <div class="eSection-wrap mb-3">
        <div class="d-flex justify-content-between">
          <h6 class="mb-2">{{ $i+1 }}. {{ $q->question }}</h6>
          <span class="badge bg-secondary" style="height:fit-content;">{{ $link->marks }} {{ get_phrase('marks') }}</span>
        </div>

        @if($q->type=='mcq')
          <ul class="mb-2" style="list-style:none; padding-left:0;">
            @foreach(($q->options ?? []) as $idx => $opt)
              @php $chosen = $ans && (string)$ans->answer===(string)$idx; $correct = (string)$q->correct_answer===(string)$idx; @endphp
              <li style="padding:3px 0; {{ $correct ? 'color:#00955f;font-weight:600;' : '' }}">
                {{ $chosen ? '▶' : '•' }} {{ $opt }}
                @if($correct) <span class="badge bg-success">{{ get_phrase('correct') }}</span>@endif
                @if($chosen && !$correct) <span class="badge bg-danger">{{ get_phrase('chosen') }}</span>@endif
              </li>
            @endforeach
          </ul>
        @elseif($q->type=='truefalse')
          <p class="mb-2">{{ get_phrase('Answer') }}: <b>{{ $ans ? ucfirst($ans->answer) : '—' }}</b> · {{ get_phrase('Correct') }}: <b>{{ ucfirst((string)$q->correct_answer) }}</b></p>
        @else
          <div class="p-2 mb-2" style="background:#f8f9fa; border-radius:6px;">{{ $ans && $ans->answer!=='' ? $ans->answer : get_phrase('(no answer)') }}</div>
          @if($q->type=='short' && trim((string)$q->correct_answer)!=='')
            <small class="text-muted">{{ get_phrase('Expected') }}: {{ $q->correct_answer }}</small>
          @endif
        @endif

        <div class="d-flex align-items-center mt-2" style="gap:10px;">
          @if($isAuto)
            <span class="badge {{ ($ans && $ans->is_correct) ? 'bg-success':'bg-danger' }}">
              {{ ($ans && $ans->is_correct) ? get_phrase('Correct') : get_phrase('Incorrect') }} — {{ $ans->awarded_marks ?? 0 }}/{{ $link->marks }}
            </span>
            <small class="text-muted">{{ get_phrase('Auto-graded') }}</small>
          @else
            <label class="eForm-label mb-0">{{ get_phrase('Award marks') }}:</label>
            <input type="number" name="awarded[{{ $q->id }}]" min="0" max="{{ $link->marks }}"
                   value="{{ $ans->awarded_marks ?? '' }}" class="form-control eForm-control" style="max-width:110px;">
            <small class="text-muted">/ {{ $link->marks }} · {{ get_phrase('Manual') }}</small>
          @endif
        </div>
      </div>
    @endforeach
  </div>

  <div class="col-lg-3">
    <div class="eSection-wrap" style="position:sticky; top:20px;">
      <h6 class="mb-2">{{ get_phrase('Summary') }}</h6>
      <p class="mb-1">{{ get_phrase('Auto score so far') }}: <b>{{ $submission->obtained_marks }}/{{ $quiz->total_marks }}</b></p>
      <div class="fpb-7 mt-2">
        <label class="eForm-label">{{ get_phrase('Feedback') }}</label>
        <textarea name="feedback" class="form-control eForm-control" rows="3">{{ $submission->feedback }}</textarea>
      </div>
      <button class="btn-form w-100 mt-2" type="submit">{{ get_phrase('Finalize & return') }}</button>
    </div>
  </div>
  </div>
</form>
@endsection
