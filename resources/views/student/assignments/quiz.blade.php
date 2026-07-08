@extends('student.navigation')

@section('content')
@php
  $sub = \App\Models\Subject::find($quiz->subject_id);
  $done = $submission !== null;
  $returned = $submission && $submission->status === 'returned';
@endphp
<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
      <div class="d-flex flex-column">
        <h4>{{ $quiz->title }} <span class="badge bg-primary">{{ get_phrase('Quiz') }}</span></h4>
        <ul class="d-flex align-items-center eBreadcrumb-2">
          <li><a href="{{ route('student.assignment_home', ['type'=>'active']) }}">{{ get_phrase('My assignments') }}</a></li>
          <li><a href="#">{{ get_phrase('Quiz') }}</a></li>
        </ul>
      </div>
      <a class="eBtn btn-secondary" href="{{ route('student.assignment_home', ['type'=>'active']) }}">{{ get_phrase('Back') }}</a>
    </div>
  </div></div>
</div>

<div class="row"><div class="col-lg-9 offset-lg-0">

  <div class="eSection-wrap mb-3">
    <p class="mb-0"><b>{{ get_phrase('Subject') }}:</b> {{ $sub->name ?? '' }} &nbsp;|&nbsp;
       <b>{{ get_phrase('Questions') }}:</b> {{ $links->count() }} &nbsp;|&nbsp;
       <b>{{ get_phrase('Total marks') }}:</b> {{ $quiz->total_marks }} &nbsp;|&nbsp;
       <b>{{ get_phrase('Time limit') }}:</b> {{ $quiz->duration_minutes ? $quiz->duration_minutes.' '.get_phrase('min') : '—' }} &nbsp;|&nbsp;
       <b>{{ get_phrase('Deadline') }}:</b> {{ $quiz->deadline ? date('d M Y, g:i A', $quiz->deadline) : '—' }}</p>
  </div>

  @if($done)
    {{-- ===== result / submitted state ===== --}}
    <div class="eSection-wrap mb-3">
      @if($returned)
        <h5 class="mb-2">{{ get_phrase('Your result') }}
          <span class="badge bg-success" style="font-size:14px;">{{ $submission->obtained_marks }}/{{ $quiz->total_marks }}</span></h5>
        @if($submission->feedback)<p class="text-muted">{{ $submission->feedback }}</p>@endif
      @else
        <h5 class="mb-2">{{ get_phrase('Submitted') }} <span class="badge bg-primary">{{ get_phrase('Awaiting teacher review') }}</span></h5>
        <p class="text-muted">{{ get_phrase('Auto-graded score so far') }}: <b>{{ $submission->obtained_marks }}/{{ $quiz->total_marks }}</b></p>
      @endif
    </div>

    @foreach($links as $i => $link)
      @php $q=$link->question; if(!$q) continue; $ans=$answers[$q->id]??null; @endphp
      <div class="eSection-wrap mb-2">
        <h6 class="mb-2">{{ $i+1 }}. {{ $q->question }}</h6>
        @if($q->type=='mcq')
          @foreach(($q->options ?? []) as $idx=>$opt)
            @php $chosen=$ans && (string)$ans->answer===(string)$idx; $correct=$returned && (string)$q->correct_answer===(string)$idx; @endphp
            <div style="{{ $correct?'color:#00955f;font-weight:600;':'' }}">
              {{ $chosen?'▶':'•' }} {{ $opt }}
              @if($returned && $correct)<span class="badge bg-success">{{ get_phrase('correct') }}</span>@endif
            </div>
          @endforeach
        @elseif($q->type=='truefalse')
          <p class="mb-0">{{ get_phrase('Your answer') }}: <b>{{ $ans ? ucfirst($ans->answer) : '—' }}</b>
            @if($returned) · {{ get_phrase('Correct') }}: <b>{{ ucfirst((string)$q->correct_answer) }}</b>@endif</p>
        @else
          <div class="p-2" style="background:#f8f9fa;border-radius:6px;">{{ $ans && $ans->answer!=='' ? $ans->answer : get_phrase('(no answer)') }}</div>
        @endif
        @if($returned)
          <small class="badge {{ ($ans && $ans->is_correct) ? 'bg-success' : ($ans && $ans->awarded_marks>0 ? 'bg-success':'bg-secondary') }} mt-2 d-inline-block">
            {{ $ans->awarded_marks ?? 0 }}/{{ $link->marks }}
          </small>
        @endif
      </div>
    @endforeach

  @elseif($closed)
    {{-- ===== deadline passed, never attempted ===== --}}
    <div class="eSection-wrap mb-3 text-center" style="padding:40px 20px;">
      <h5 class="mb-2" style="color:#c0392b;">{{ get_phrase('This exam is closed') }}</h5>
      <p class="text-muted mb-0">{{ get_phrase('The deadline passed on') }} {{ date('d M Y, g:i A', $quiz->deadline) }}. {{ get_phrase('You can no longer take it.') }}</p>
    </div>

  @else
    {{-- ===== answer form ===== --}}
    @if($remaining !== null)
      <div id="quizTimerBar" class="eSection-wrap mb-3 d-flex justify-content-between align-items-center"
           style="border-left:4px solid #00955f;">
        <span><b>{{ get_phrase('Time remaining') }}</b></span>
        <span id="quizTimer" style="font-size:22px;font-weight:700;font-variant-numeric:tabular-nums;color:#00955f;">--:--</span>
      </div>
    @endif
    <form id="quizForm" method="POST" action="{{ route('student.quiz.submit', $quiz->id) }}"
          onsubmit="return quizFormSubmit(this);">
      @csrf
      @foreach($links as $i => $link)
        @php $q=$link->question; if(!$q) continue; @endphp
        <div class="eSection-wrap mb-2">
          <div class="d-flex justify-content-between">
            <h6 class="mb-2">{{ $i+1 }}. {{ $q->question }}</h6>
            <span class="badge bg-secondary" style="height:fit-content;">{{ $link->marks }} {{ get_phrase('marks') }}</span>
          </div>

          @if($q->type=='mcq')
            @foreach(($q->options ?? []) as $idx=>$opt)
              <label class="d-block py-1" style="cursor:pointer;">
                <input type="radio" name="answers[{{ $q->id }}]" value="{{ $idx }}"> {{ $opt }}
              </label>
            @endforeach
          @elseif($q->type=='truefalse')
            <label class="d-block py-1"><input type="radio" name="answers[{{ $q->id }}]" value="true"> {{ get_phrase('True') }}</label>
            <label class="d-block py-1"><input type="radio" name="answers[{{ $q->id }}]" value="false"> {{ get_phrase('False') }}</label>
          @elseif($q->type=='short')
            <input type="text" class="form-control eForm-control" name="answers[{{ $q->id }}]" placeholder="{{ get_phrase('Your answer') }}">
          @else
            <textarea class="form-control eForm-control" name="answers[{{ $q->id }}]" rows="3" placeholder="{{ get_phrase('Write your answer...') }}"></textarea>
          @endif
        </div>
      @endforeach
      <button class="btn-form" type="submit">{{ get_phrase('Submit quiz') }}</button>
    </form>
  @endif

</div></div>

@if(!$done && !$closed)
<script type="text/javascript">
  "use strict";
  var quizAutoSubmit = false;

  // Confirm on manual submit; skip the prompt when the timer auto-submits.
  function quizFormSubmit(form) {
    if (quizAutoSubmit) return true;
    return confirm('{{ get_phrase('Submit your quiz? You cannot change answers after submitting.') }}');
  }

  @if($remaining !== null)
  (function () {
    var remaining = {{ (int) $remaining }}; // seconds, server-anchored
    var elTimer = document.getElementById('quizTimer');
    var elBar   = document.getElementById('quizTimerBar');

    function render() {
      var m = Math.floor(remaining / 60), s = remaining % 60;
      elTimer.textContent = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
      // Warn in the last minute.
      if (remaining <= 60 && elBar) {
        elBar.style.borderLeftColor = '#c0392b';
        elTimer.style.color = '#c0392b';
      }
    }

    render();
    var iv = setInterval(function () {
      remaining -= 1;
      if (remaining <= 0) {
        clearInterval(iv);
        remaining = 0; render();
        quizAutoSubmit = true;
        elTimer.textContent = "{{ get_phrase('Time up — submitting…') }}";
        document.getElementById('quizForm').submit();
        return;
      }
      render();
    }, 1000);
  })();
  @endif
</script>
@endif
@endsection
