@extends(auth()->user()->role_id == 7 ? 'student.navigation' : 'admin.navigation')

@section('content')
@php
  $isStudent = auth()->user()->role_id == 7;
  $pdfUrl  = $isStudent ? route('student.transcript.pdf') : route('admin.transcript.pdf', $student->id);
  $gc = function($name){ $n=strtoupper(trim((string)$name)); if(in_array($n,['A+','A']))return '#00955f'; if(in_array($n,['B','C']))return '#2f6fb0'; if($n==='D')return '#e0a800'; return '#f04b24'; };
@endphp
<style>
  .tr-hero{ background:linear-gradient(135deg,#00955f,#007a4d); color:#fff; border-radius:14px; padding:22px 26px; margin-bottom:18px;
    display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; box-shadow:0 10px 30px rgba(0,149,95,.16); }
  .tr-hero h4{ color:#fff; margin:0; font-weight:700; } .tr-hero p{ color:rgba(255,255,255,.9); margin:3px 0 0; font-size:14px; }
  .tr-hero .box{ background:rgba(255,255,255,.15); border-radius:10px; padding:8px 18px; text-align:center; }
  .tr-hero .box .n{ font-size:20px; font-weight:700; } .tr-hero .box .l{ font-size:11px; opacity:.85; }
  .tr-grade{ display:inline-block; min-width:32px; padding:3px 9px; border-radius:6px; color:#fff; font-weight:700; font-size:12px; }
  .tr-miss{ background:#fdeee9; border:1px solid #f7c9bd; color:#c0392b; border-radius:8px; padding:10px 14px; margin-bottom:14px; font-size:13px; }
  table.tr th, table.tr td{ text-align:center; }
  table.tr th.s, table.tr td.s{ text-align:left; }
</style>

<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
      <div class="d-flex flex-column">
        <h4>{{ get_phrase('Academic transcript') }}</h4>
        <ul class="d-flex align-items-center eBreadcrumb-2"><li><a href="#">{{ get_phrase('Examination') }}</a></li><li><a href="#">{{ get_phrase('Transcript') }}</a></li></ul>
      </div>
      <div class="d-flex" style="gap:8px;">
        <a class="eBtn btn-primary" href="{{ $pdfUrl }}"><i class="bi bi-file-earmark-pdf"></i> {{ get_phrase('Download PDF') }}</a>
        <a class="eBtn btn-secondary" href="javascript:;" onclick="window.print()"><i class="bi bi-printer"></i> {{ get_phrase('Print') }}</a>
      </div>
    </div>
  </div></div>
</div>

<div class="tr-hero">
  <div>
    <h4>{{ $student->name }}</h4>
    <p>{{ $class->name ?? '' }}{{ $sessionTitle ? ' · '.$sessionTitle : '' }} · {{ get_phrase('Academic transcript') }}</p>
  </div>
  <div class="d-flex" style="gap:10px;">
    <div class="box"><div class="n">{{ $overallAvg !== null ? $overallAvg.'%' : '—' }}</div><div class="l">{{ get_phrase('Average') }}</div></div>
    <div class="box"><div class="n">{{ $gpa !== null ? number_format($gpa,2) : '—' }}</div><div class="l">{{ get_phrase('GPA') }}</div></div>
    <div class="box"><div class="n">{{ $overallGrade->name ?? '—' }}</div><div class="l">{{ get_phrase('Grade') }}</div></div>
  </div>
</div>

<div class="row"><div class="col-12"><div class="eSection-wrap">
  @if($exam_categories->count() === 0)
    <div class="text-center text-muted py-4">{{ get_phrase('No exams have been set up yet.') }}</div>
  @else
    @if($missing > 0)
      <div class="tr-miss"><i class="bi bi-exclamation-triangle"></i> {{ $missing }} {{ get_phrase('subject(s) have missing or incomplete marks — the transcript is provisional.') }}</div>
    @endif
    <div class="table-responsive">
      <table class="table eTable eTable-2 tr">
        <thead><tr>
          <th style="width:44px;">#</th><th class="s">{{ get_phrase('Subject') }}</th>
          @foreach($exam_categories as $ec)<th>{{ $ec->name }}</th>@endforeach
          <th>{{ get_phrase('Average') }}</th><th>{{ get_phrase('Grade') }}</th>
        </tr></thead>
        <tbody>
          @foreach($rows as $i => $r)
            <tr>
              <td>{{ $i+1 }}</td>
              <td class="s">{{ $r['subject']->name }}</td>
              @foreach($exam_categories as $ec)
                <td>{{ isset($r['vals'][$ec->id]) ? $r['vals'][$ec->id] : '—' }}</td>
              @endforeach
              <td><b>{{ $r['avg'] !== null ? $r['avg'] : '—' }}</b></td>
              <td>@if($r['grade'])<span class="tr-grade" style="background:{{ $gc($r['grade']->name) }};">{{ $r['grade']->name }}</span>@else — @endif</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div></div></div>
@endsection
