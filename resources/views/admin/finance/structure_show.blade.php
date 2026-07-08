@extends(auth()->user()->role_id == 4 ? 'accountant.navigation' : 'admin.navigation')

@section('content')
@php $cur = get_settings('system_currency') ?: 'USD'; $total = $structure->items->sum('amount'); @endphp
<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
      <div class="d-flex flex-column">
        <h4>{{ $structure->title }}</h4>
        <ul class="d-flex align-items-center eBreadcrumb-2">
          <li><a href="{{ route('admin.finance.structures') }}">{{ get_phrase('Fee structures') }}</a></li>
          <li><a href="#">{{ $class->name ?? '' }}</a></li>
        </ul>
      </div>
      <a class="eBtn btn-secondary" href="{{ route('admin.finance.structures') }}">{{ get_phrase('Back') }}</a>
    </div>
  </div></div>
</div>

<div class="row">
  <div class="col-lg-7 mb-3"><div class="eSection-wrap">
    <h6 class="mb-3">{{ get_phrase('Fee items') }}</h6>
    <table class="table eTable eTable-2">
      <thead><tr><th>{{ get_phrase('Fee head') }}</th><th class="text-end">{{ get_phrase('Amount') }}</th></tr></thead>
      <tbody>
        @foreach($structure->items as $it)
          <tr><td>{{ optional($it->head)->name ?? 'Fee' }}</td><td class="text-end">{{ $cur }} {{ number_format($it->amount,2) }}</td></tr>
        @endforeach
      </tbody>
      <tfoot><tr><th>{{ get_phrase('Total per student') }}</th><th class="text-end">{{ $cur }} {{ number_format($total,2) }}</th></tr></tfoot>
    </table>
  </div></div>

  <div class="col-lg-5 mb-3"><div class="eSection-wrap">
    <h6 class="mb-3">{{ get_phrase('Generate invoices') }}</h6>
    <p class="mb-1">{{ get_phrase('Class') }}: <b>{{ $class->name ?? '-' }}</b></p>
    <p class="mb-1">{{ get_phrase('Students in class') }}: <b>{{ $studentCount }}</b></p>
    <p class="mb-3">{{ get_phrase('Already invoiced') }}: <b>{{ $invoiced }}</b></p>
    <a class="btn-form d-inline-block" href="{{ route('admin.finance.structure.generate', $structure->id) }}"
       onclick="return confirm('{{ get_phrase('Generate invoices for all students in this class?') }}')">
       <i class="bi bi-receipt"></i> {{ get_phrase('Generate invoices') }}</a>
    <p class="text-muted mt-2" style="font-size:12px;">{{ get_phrase('Each student gets one invoice for') }} {{ $cur }} {{ number_format($total,2) }}. {{ get_phrase('Students already invoiced are skipped.') }}</p>
  </div></div>
</div>
@endsection
