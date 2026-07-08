<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { font-family: DejaVu Sans, sans-serif; }
  body { margin:0; color:#2b2f3a; font-size:11px; }
  .header { border-bottom:3px solid #00955f; padding-bottom:12px; margin-bottom:16px; }
  .header table { width:100%; }
  .header .logo { width:80px; }
  .header .org { font-size:15px; font-weight:bold; color:#00955f; }
  .header .addr { font-size:9px; color:#777; }
  .doc { text-align:right; }
  .doc .t { font-size:14px; font-weight:bold; }
  .doc .s { font-size:10px; color:#555; }
  .meta { background:#f5faf8; border:1px solid #e4efe9; border-radius:5px; padding:8px 12px; margin-bottom:14px; font-size:11px; }
  .meta span { margin-right:20px; } .meta b { color:#00955f; }
  table.tr { width:100%; border-collapse:collapse; margin-bottom:14px; }
  table.tr th, table.tr td { border:1px solid #e0e6e2; padding:6px 8px; text-align:center; font-size:10px; }
  table.tr th { background:#00955f; color:#fff; }
  table.tr td.s, table.tr th.s { text-align:left; }
  table.tr tfoot td { font-weight:bold; background:#f5faf8; }
  .summary { width:100%; }
  .summary td { width:33%; text-align:center; background:#f5faf8; border:1px solid #e4efe9; padding:10px; }
  .summary .n { font-size:18px; font-weight:bold; color:#00955f; }
  .summary .l { font-size:10px; color:#666; }
  .miss { color:#c0392b; font-size:10px; margin-bottom:10px; }
  .sign { margin-top:40px; font-size:10px; }
  .sign td { width:50%; padding-top:26px; }
  .sign .line { border-top:1px solid #999; width:70%; padding-top:3px; }
  .footer { position:fixed; bottom:0; left:0; right:0; text-align:center; font-size:8px; color:#999; border-top:1px solid #eee; padding-top:5px; }
</style>
</head>
<body>
  <div class="header"><table><tr>
    <td style="width:58%;">
      @if($logoPath)<img class="logo" src="{{ $logoPath }}">@endif
      <div class="org">{{ $school->title ?? 'School' }}</div>
      <div class="addr">{{ $school->address ?? '' }}{{ ($school->phone ?? '') ? ' · '.$school->phone : '' }}</div>
    </td>
    <td class="doc" style="width:42%;">
      <div class="t">ACADEMIC TRANSCRIPT</div>
      <div class="s">{{ $sessionTitle ?? '' }}</div>
      <div class="s">Issued {{ date('d M Y') }}</div>
    </td>
  </tr></table></div>

  <div class="meta">
    <span><b>Student:</b> {{ $student->name }}</span>
    <span><b>Class:</b> {{ $class->name ?? '-' }}</span>
    <span><b>Session:</b> {{ $sessionTitle ?? '-' }}</span>
  </div>

  @if($missing > 0)<div class="miss">Note: {{ $missing }} subject(s) have missing marks — this transcript is provisional.</div>@endif

  <table class="tr">
    <thead><tr>
      <th style="width:26px;">#</th><th class="s">Subject</th>
      @foreach($exam_categories as $ec)<th>{{ $ec->name }}</th>@endforeach
      <th>Average</th><th>Grade</th><th>Points</th>
    </tr></thead>
    <tbody>
      @foreach($rows as $i => $r)
        <tr>
          <td>{{ $i+1 }}</td>
          <td class="s">{{ $r['subject']->name }}</td>
          @foreach($exam_categories as $ec)<td>{{ isset($r['vals'][$ec->id]) ? $r['vals'][$ec->id] : '-' }}</td>@endforeach
          <td>{{ $r['avg'] !== null ? $r['avg'] : '-' }}</td>
          <td>{{ $r['grade']->name ?? '-' }}</td>
          <td>{{ $r['grade']->grade_point ?? '-' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <table class="summary"><tr>
    <td><div class="n">{{ $overallAvg !== null ? $overallAvg.'%' : '-' }}</div><div class="l">OVERALL AVERAGE</div></td>
    <td><div class="n">{{ $gpa !== null ? number_format($gpa,2) : '-' }}</div><div class="l">GPA</div></td>
    <td><div class="n">{{ $overallGrade->name ?? '-' }}</div><div class="l">FINAL GRADE</div></td>
  </tr></table>

  <table class="sign"><tr>
    <td><div class="line">Class teacher</div></td>
    <td style="text-align:right;"><div class="line" style="margin-left:30%;">Principal / Registrar</div></td>
  </tr></table>

  <div class="footer">{{ $school->title ?? '' }} — Academic Transcript · {{ $student->name }} · Generated {{ date('d M Y') }}</div>
</body>
</html>
