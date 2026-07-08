@extends(auth()->user()->role_id == 4 ? 'accountant.navigation' : 'admin.navigation')

@section('content')
@php $cur = get_settings('system_currency') ?: 'USD'; $running = 0; @endphp
<style>@media print{ .no-print{display:none!important;} .mainSection-title{display:none;} }</style>
<div class="mainSection-title no-print">
  <div class="row"><div class="col-12"><div class="d-flex flex-column">
    <h4>{{ get_phrase('Reports & statements') }}</h4>
    <ul class="d-flex align-items-center eBreadcrumb-2"><li><a href="#">{{ get_phrase('Finance') }}</a></li><li><a href="#">{{ get_phrase('Cash daybook') }}</a></li></ul>
  </div></div></div>
</div>

<div class="no-print">@include('admin.finance._tabs')</div>

<div class="row"><div class="col-12"><div class="eSection-wrap">
  <form method="GET" class="row g-2 mb-3 no-print" action="{{ route('admin.finance.report.daybook') }}">
    <div class="col-md-3"><label class="eForm-label">{{ get_phrase('From') }}</label><input type="date" name="from" value="{{ date('Y-m-d',$from) }}" class="form-control eForm-control"></div>
    <div class="col-md-3"><label class="eForm-label">{{ get_phrase('To') }}</label><input type="date" name="to" value="{{ date('Y-m-d',$to) }}" class="form-control eForm-control"></div>
    <div class="col-md-2 d-flex align-items-end"><button class="eBtn btn-primary w-100" type="submit">{{ get_phrase('Apply') }}</button></div>
    <div class="col-md-2 d-flex align-items-end"><button type="button" class="eBtn btn-secondary w-100" onclick="window.print()"><i class="bi bi-printer"></i> {{ get_phrase('Print') }}</button></div>
  </form>

  <div class="d-flex justify-content-between mb-2" style="font-size:14px;">
    <span>{{ get_phrase('Cash daybook') }} · {{ date('d M Y',$from) }} — {{ date('d M Y',$to) }}</span>
    <span>{{ get_phrase('In') }}: <b class="text-success">{{ $cur }} {{ number_format($income,2) }}</b> · {{ get_phrase('Out') }}: <b style="color:#f04b24;">{{ $cur }} {{ number_format($expense,2) }}</b> · {{ get_phrase('Net') }}: <b>{{ $cur }} {{ number_format($income-$expense,2) }}</b></span>
  </div>
  <div class="table-responsive">
    <table class="table eTable eTable-2">
      <thead><tr><th>{{ get_phrase('Date') }}</th><th>{{ get_phrase('Description') }}</th><th>{{ get_phrase('Category') }}</th><th class="text-end">{{ get_phrase('In') }}</th><th class="text-end">{{ get_phrase('Out') }}</th><th class="text-end">{{ get_phrase('Balance') }}</th></tr></thead>
      <tbody>
        @forelse($txns as $t)
          @php $running += $t->type==='income' ? $t->amount : -$t->amount; @endphp
          <tr>
            <td>{{ $t->txn_date ? date('d M Y',$t->txn_date) : '' }}</td>
            <td>{{ $t->description ?: '—' }}</td>
            <td><small class="text-muted">{{ $t->category }}</small></td>
            <td class="text-end text-success">{{ $t->type==='income' ? $cur.' '.number_format($t->amount,2) : '' }}</td>
            <td class="text-end" style="color:#f04b24;">{{ $t->type==='expense' ? $cur.' '.number_format($t->amount,2) : '' }}</td>
            <td class="text-end"><b>{{ number_format($running,2) }}</b></td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-muted">{{ get_phrase('No transactions in this period.') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div></div></div>
@endsection
