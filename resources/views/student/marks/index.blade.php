@extends('student.navigation')

@section('content')
@php
  // grade lookup helper
  $gradeFor = function ($avg) use ($grades) {
      if ($avg === null) return null;
      foreach ($grades as $g) {
          if ($avg >= $g->mark_from && $avg <= $g->mark_upto) return $g;
      }
      return null;
  };
  $gradeColor = function ($name) {
      $n = strtoupper(trim((string)$name));
      if (in_array($n, ['A+','A'])) return '#00955f';
      if (in_array($n, ['B','C'])) return '#2f6fb0';
      if ($n === 'D') return '#e0a800';
      return '#f04b24';
  };

  // overall (average of every recorded subject-mark across categories)
  $allMarks = [];
  foreach ($marksMap as $catMarks) {
      foreach ($catMarks as $m) { if (is_numeric($m)) $allMarks[] = (float)$m; }
  }
  $overallAvg = count($allMarks) ? round(array_sum($allMarks) / count($allMarks), 1) : null;
  $overallGrade = $gradeFor($overallAvg);
@endphp

<style>
  .mk-hero{ background:linear-gradient(135deg,#00955f,#007a4d); color:#fff; border-radius:14px;
    padding:20px 24px; margin-bottom:18px; box-shadow:0 10px 30px rgba(0,149,95,.16);
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px; }
  .mk-hero h4{ color:#fff; font-weight:700; margin:0; }
  .mk-hero p{ color:rgba(255,255,255,.9); margin:3px 0 0; font-size:14px; }
  .mk-hero .score{ text-align:center; background:rgba(255,255,255,.15); border-radius:12px; padding:10px 20px; }
  .mk-hero .score .n{ font-size:26px; font-weight:700; line-height:1; }
  .mk-hero .score .l{ font-size:12px; opacity:.85; }
  table.mk{ width:100%; border-collapse:separate; border-spacing:0; }
  table.mk th, table.mk td{ padding:12px 14px; border-bottom:1px solid #eef0f4; text-align:center; }
  table.mk th{ background:#f7fbf9; color:#181c32; font-weight:600; font-size:13px; }
  table.mk th.subj, table.mk td.subj{ text-align:left; }
  table.mk tbody tr:hover{ background:#fafcfb; }
  table.mk tbody tr:last-child td{ border-bottom:none; }
  .mk-grade{ display:inline-block; min-width:34px; padding:3px 9px; border-radius:6px; color:#fff; font-weight:700; font-size:12px; }
  .mk-empty{ text-align:center; padding:50px 20px; color:#9aa1b0; }
  .mk-empty i{ font-size:38px; color:#d3d8e0; }
</style>

<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
      <div class="d-flex flex-column">
        <h4>{{ get_phrase('View Marks') }}</h4>
        <ul class="d-flex align-items-center eBreadcrumb-2">
          <li><a href="#">{{ get_phrase('Home') }}</a></li>
          <li><a href="#">{{ get_phrase('Examination') }}</a></li>
          <li><a href="#">{{ get_phrase('Marks') }}</a></li>
        </ul>
      </div>
    </div>
  </div></div>
</div>

@if($exam_categories->count() === 0 || count($marksMap) === 0)
  <div class="eSection-wrap"><div class="mk-empty">
    <i class="bi bi-clipboard-data"></i>
    <p class="mb-0 mt-2">{{ get_phrase('No exam results have been published yet.') }}</p>
  </div></div>
@else
  <div class="mk-hero">
    <div>
      <h4>{{ get_phrase('My results') }}</h4>
      <p>{{ $student_details['name'] ?? '' }} · {{ $exam_categories->count() }} {{ get_phrase('exams') }} · {{ $subjects->count() }} {{ get_phrase('subjects') }}</p>
    </div>
    @if($overallAvg !== null)
      <div class="d-flex align-items-center" style="gap:12px;">
        <div class="score"><div class="n">{{ $overallAvg }}%</div><div class="l">{{ get_phrase('Overall average') }}</div></div>
        @if($overallGrade)
          <div class="score"><div class="n">{{ $overallGrade->name }}</div><div class="l">{{ get_phrase('Grade') }} · {{ $overallGrade->grade_point }}</div></div>
        @endif
      </div>
    @endif
  </div>

  <div class="row"><div class="col-12"><div class="eSection-wrap">
    <div class="d-flex justify-content-end mb-2">
      <button class="eBtn btn-secondary" onclick="generatePDF()"><i class="bi bi-download"></i> {{ get_phrase('Export PDF') }}</button>
    </div>

    <div class="table-responsive" id="mark_report">
      <table class="mk">
        <thead>
          <tr>
            <th style="width:44px;">#</th>
            <th class="subj">{{ get_phrase('Subject') }}</th>
            @foreach($exam_categories as $exam_category)
              <th>{{ $exam_category->name }}</th>
            @endforeach
            <th>{{ get_phrase('Average') }}</th>
            <th>{{ get_phrase('Grade') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($subjects as $i => $subject)
            @php
              $vals = [];
              foreach ($exam_categories as $ec) {
                  $m = $marksMap[$ec->id][$subject->id] ?? null;
                  if (is_numeric($m)) $vals[$ec->id] = (float)$m;
              }
              $subAvg = count($vals) ? round(array_sum($vals) / count($vals), 1) : null;
              $subGrade = $gradeFor($subAvg);
            @endphp
            <tr>
              <td>{{ $i + 1 }}</td>
              <td class="subj">{{ $subject->name }}</td>
              @foreach($exam_categories as $ec)
                <td>{{ $vals[$ec->id] ?? '—' }}</td>
              @endforeach
              <td><b>{{ $subAvg !== null ? $subAvg : '—' }}</b></td>
              <td>
                @if($subGrade)
                  <span class="mk-grade" style="background:{{ $gradeColor($subGrade->name) }};">{{ $subGrade->name }}</span>
                @else — @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div></div></div>
@endif
@endsection

<script type="text/javascript">
  "use strict";
  function generatePDF() {
    if (typeof html2pdf === 'undefined') { window.print(); return; }
    var element = document.getElementById('mark_report');
    html2pdf().set({ margin: 8, filename: 'Mark report.pdf', image: { type: 'jpeg', quality: 0.98 }, html2canvas: { scale: 2 } }).from(element).save();
  }
</script>
