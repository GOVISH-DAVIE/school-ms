<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { font-family: DejaVu Sans, sans-serif; }
  body { margin: 0; color: #2b2f3a; font-size: 12px; }
  .header { border-bottom: 3px solid #00955f; padding-bottom: 12px; margin-bottom: 18px; }
  .header table { width: 100%; }
  .header .logo { width: 90px; vertical-align: middle; }
  .header .org { font-size: 16px; font-weight: bold; color: #00955f; }
  .header .addr { font-size: 10px; color: #777; }
  .doc-title { text-align: right; }
  .doc-title .t { font-size: 15px; font-weight: bold; }
  .doc-title .s { font-size: 11px; color: #555; }
  .meta { background: #f5faf8; border: 1px solid #e4efe9; border-radius: 6px; padding: 10px 12px; margin-bottom: 16px; }
  .meta span { display: inline-block; margin-right: 22px; font-size: 11px; }
  .meta b { color: #00955f; }
  .topic { margin-bottom: 14px; }
  .topic .th { background: #00955f; color: #fff; padding: 7px 12px; font-weight: bold; font-size: 12px; border-radius: 4px; }
  .lesson { padding: 8px 12px 4px; border-left: 2px solid #d7e9e1; }
  .lesson .lt { font-weight: bold; font-size: 12px; margin: 6px 0 3px; color: #181c32; }
  .lesson .note { font-size: 11px; color: #444; line-height: 1.5; }
  .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #eee; padding-top: 6px; }
  .empty { color: #999; font-style: italic; padding: 10px; }
</style>
</head>
<body>
  <div class="header">
    <table>
      <tr>
        <td style="width: 55%;">
          @if($logoPath)<img class="logo" src="{{ $logoPath }}">@endif
          <div class="org">{{ $school->title ?? 'School' }}</div>
          <div class="addr">{{ $school->address ?? '' }}{{ ($school->phone ?? '') ? ' · '.$school->phone : '' }}</div>
        </td>
        <td class="doc-title" style="width: 45%;">
          <div class="t">COURSE SYLLABUS</div>
          <div class="s">{{ $subject->name ?? '' }}</div>
          <div class="s">Generated {{ date('d M Y') }}</div>
        </td>
      </tr>
    </table>
  </div>

  <div class="meta">
    <span><b>Subject:</b> {{ $subject->name ?? '-' }}</span>
    <span><b>Class:</b> {{ $class->name ?? '-' }}</span>
    <span><b>Title:</b> {{ $syllabus->title }}</span>
  </div>

  @if($course && $course->topics->count())
    @foreach($course->topics as $ti => $topic)
      <div class="topic">
        <div class="th">{{ $ti+1 }}. {{ $topic->title }}</div>
        @forelse($topic->lessons as $lesson)
          <div class="lesson">
            <div class="lt">{{ $lesson->title }}</div>
            <div class="note">{!! $lesson->content ?: '<i>No notes.</i>' !!}</div>
          </div>
        @empty
          <div class="empty">No lessons under this topic.</div>
        @endforelse
      </div>
    @endforeach
  @else
    <div class="empty">No detailed outline has been published for this subject. Please contact your instructor.</div>
  @endif

  <div class="footer">{{ $school->title ?? '' }} — Course Syllabus · {{ $subject->name ?? '' }}</div>
</body>
</html>
