@extends(auth()->user()->role_id == 4 ? 'accountant.navigation' : 'admin.navigation')

@section('content')
@php $cur = get_settings('system_currency') ?: 'USD'; @endphp
<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
      <div class="d-flex flex-column">
        <h4>{{ get_phrase('New fee structure') }}</h4>
        <ul class="d-flex align-items-center eBreadcrumb-2">
          <li><a href="{{ route('admin.finance.structures') }}">{{ get_phrase('Fee structures') }}</a></li>
          <li><a href="#">{{ get_phrase('New') }}</a></li>
        </ul>
      </div>
      <a class="eBtn btn-secondary" href="{{ route('admin.finance.structures') }}">{{ get_phrase('Back') }}</a>
    </div>
  </div></div>
</div>

<div class="row"><div class="col-lg-8"><div class="eSection-wrap">
  @if($fee_heads->count() === 0)
    <div class="text-center text-muted py-4">
      {{ get_phrase('Add some fee heads first.') }}
      <a href="{{ route('admin.finance.fee_heads') }}">{{ get_phrase('Go to Fee heads') }}</a>
    </div>
  @else
  <form method="POST" action="{{ route('admin.finance.structure.store') }}">
    @csrf
    <div class="row">
      <div class="col-md-6 fpb-7">
        <label class="eForm-label">{{ get_phrase('Title') }}</label>
        <input type="text" name="title" class="form-control eForm-control" placeholder="{{ get_phrase('e.g. Term 1 — 2026/2027') }}" required>
      </div>
      <div class="col-md-3 fpb-7">
        <label class="eForm-label">{{ get_phrase('Class') }}</label>
        <select name="class_id" class="form-select eForm-select" required>
          <option value="">{{ get_phrase('Select a class') }}</option>
          @foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach
        </select>
      </div>
      <div class="col-md-3 fpb-7">
        <label class="eForm-label">{{ get_phrase('Due date') }}</label>
        <input type="date" name="due_date" class="form-control eForm-control">
      </div>
    </div>

    <label class="eForm-label mt-2">{{ get_phrase('Fee heads & amounts') }}</label>
    <table class="table eTable eTable-2">
      <thead><tr><th>{{ get_phrase('Fee head') }}</th><th style="width:220px;">{{ get_phrase('Amount') }} ({{ $cur }})</th></tr></thead>
      <tbody>
        @foreach($fee_heads as $h)
          <tr>
            <td>{{ $h->name }}</td>
            <td><input type="number" step="0.01" min="0" name="amount[{{ $h->id }}]" class="form-control eForm-control amt" placeholder="0.00"></td>
          </tr>
        @endforeach
      </tbody>
      <tfoot><tr><th class="text-end">{{ get_phrase('Total') }}</th><th id="struct_total">{{ $cur }} 0.00</th></tr></tfoot>
    </table>

    <button class="btn-form mt-2" type="submit">{{ get_phrase('Create structure') }}</button>
  </form>
  @endif
</div></div></div>

<script>
  "use strict";
  document.addEventListener('input', function(e){
    if(!e.target.classList.contains('amt')) return;
    var t = 0;
    document.querySelectorAll('.amt').forEach(function(i){ t += parseFloat(i.value)||0; });
    document.getElementById('struct_total').textContent = '{{ $cur }} ' + t.toFixed(2);
  });
</script>
@endsection
