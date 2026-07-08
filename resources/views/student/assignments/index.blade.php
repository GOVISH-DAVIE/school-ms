@extends('student.navigation')

@section('content')
<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex flex-column">
      <h4>{{ get_phrase('My assignments') }}</h4>
      <ul class="d-flex align-items-center eBreadcrumb-2">
        <li><a href="#">{{ get_phrase('Home') }}</a></li>
        <li><a href="#">{{ get_phrase('My assignments') }}</a></li>
      </ul>
    </div>
  </div></div>
</div>

<div class="row">
  <div class="col-12"><div class="eSection-wrap">

    <ul class="nav eTab-nav mb-3" style="gap:8px;">
      <li><a class="eBtn {{ $type=='active'?'btn-primary':'btn-secondary' }}" href="{{ route('student.assignment_home', ['type'=>'active']) }}">{{ get_phrase('To do') }}</a></li>
      <li><a class="eBtn {{ $type=='submitted'?'btn-primary':'btn-secondary' }}" href="{{ route('student.assignment_home', ['type'=>'submitted']) }}">{{ get_phrase('Submitted') }}</a></li>
      <li><a class="eBtn {{ $type=='all'?'btn-primary':'btn-secondary' }}" href="{{ route('student.assignment_home', ['type'=>'all']) }}">{{ get_phrase('All') }}</a></li>
    </ul>

    <div class="table-responsive">
      <table class="table eTable eTable-2">
        <thead>
          <tr>
            <th>{{ get_phrase('Title') }}</th>
            <th>{{ get_phrase('Subject') }}</th>
            <th>{{ get_phrase('Deadline') }}</th>
            <th>{{ get_phrase('Status') }}</th>
            <th>{{ get_phrase('Action') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($assignments as $assignment)
            @php
              $sub = \App\Models\Subject::find($assignment->subject_id);
              $sm = $submissions[$assignment->id] ?? null;
            @endphp
            <tr>
              <td>{{ $assignment->title }}</td>
              <td>{{ $sub->name ?? '-' }}</td>
              <td>{{ $assignment->deadline ? date('d M Y', $assignment->deadline) : '—' }}</td>
              <td>
                @if(!$sm)<span class="badge bg-secondary">{{ get_phrase('Pending') }}</span>
                @elseif($sm->status=='returned')<span class="badge bg-success">{{ get_phrase('Graded') }}: {{ $sm->obtained_marks }}/{{ $assignment->total_marks }}</span>
                @else<span class="badge bg-primary">{{ get_phrase('Submitted') }}</span>@endif
              </td>
              <td>
                <a class="eBtn btn-primary" href="{{ route('student.assignment.show', $assignment->id) }}">
                  {{ $sm ? get_phrase('View') : get_phrase('Submit') }}
                </a>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center">{{ get_phrase('Nothing here.') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if(method_exists($assignments,'links'))<div class="mt-3">{{ $assignments->links() }}</div>@endif
  </div></div>
</div>
@endsection
