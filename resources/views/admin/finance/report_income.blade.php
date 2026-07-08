@extends(auth()->user()->role_id == 4 ? 'accountant.navigation' : 'admin.navigation')

@section('content')
@php $cur = get_settings('system_currency') ?: 'USD'; @endphp
<style>@media print{ .no-print{display:none!important;} .mainSection-title{display:none;} }</style>
<div class="mainSection-title no-print">
  <div class="row"><div class="col-12"><div class="d-flex flex-column">
    <h4>{{ get_phrase('Reports & statements') }}</h4>
    <ul class="d-flex align-items-center eBreadcrumb-2"><li><a href="#">{{ get_phrase('Finance') }}</a></li><li><a href="#">{{ get_phrase('Reports') }}</a></li></ul>
  </div></div></div>
</div>

<div class="no-print">@include('admin.finance._tabs')</div>

<div class="row"><div class="col-lg-9 offset-lg-0"><div class="eSection-wrap">
  <form method="GET" class="row g-2 mb-3 no-print" action="{{ route('admin.finance.report.income') }}">
    <div class="col-md-3"><label class="eForm-label">{{ get_phrase('From') }}</label><input type="date" name="from" value="{{ date('Y-m-d',$from) }}" class="form-control eForm-control"></div>
    <div class="col-md-3"><label class="eForm-label">{{ get_phrase('To') }}</label><input type="date" name="to" value="{{ date('Y-m-d',$to) }}" class="form-control eForm-control"></div>
    <div class="col-md-2 d-flex align-items-end"><button class="eBtn btn-primary w-100" type="submit">{{ get_phrase('Apply') }}</button></div>
    <div class="col-md-2 d-flex align-items-end"><button type="button" class="eBtn btn-secondary w-100" onclick="window.print()"><i class="bi bi-printer"></i> {{ get_phrase('Print') }}</button></div>
  </form>

  <h5 class="text-center mb-1">{{ get_phrase('Income Statement') }}</h5>
  <p class="text-center text-muted mb-4" style="font-size:13px;">{{ date('d M Y',$from) }} — {{ date('d M Y',$to) }}</p>

  <table class="table eTable eTable-2">
    <thead><tr><th style="background:#e8f5ef;">{{ get_phrase('Income') }}</th><th class="text-end" style="background:#e8f5ef;"></th></tr></thead>
    <tbody>
      @forelse($income as $c)<tr><td>{{ $c->category ?: get_phrase('Uncategorised') }}</td><td class="text-end">{{ $cur }} {{ number_format($c->t,2) }}</td></tr>@empty<tr><td colspan="2" class="text-muted">{{ get_phrase('No income in this period.') }}</td></tr>@endforelse
    </tbody>
    <tfoot><tr><th>{{ get_phrase('Total income') }}</th><th class="text-end text-success">{{ $cur }} {{ number_format($totalIncome,2) }}</th></tr></tfoot>
  </table>

  <table class="table eTable eTable-2 mt-3">
    <thead><tr><th style="background:#fdeee9;">{{ get_phrase('Expenses') }}</th><th class="text-end" style="background:#fdeee9;"></th></tr></thead>
    <tbody>
      @forelse($expense as $c)<tr><td>{{ $c->category ?: get_phrase('Uncategorised') }}</td><td class="text-end">{{ $cur }} {{ number_format($c->t,2) }}</td></tr>@empty<tr><td colspan="2" class="text-muted">{{ get_phrase('No expenses in this period.') }}</td></tr>@endforelse
    </tbody>
    <tfoot><tr><th>{{ get_phrase('Total expenses') }}</th><th class="text-end" style="color:#f04b24;">{{ $cur }} {{ number_format($totalExpense,2) }}</th></tr></tfoot>
  </table>

  <div class="d-flex justify-content-between align-items-center mt-3 p-3" style="background:{{ $net>=0 ? '#e8f5ef':'#fdeee9' }}; border-radius:10px;">
    <b style="font-size:16px;">{{ $net>=0 ? get_phrase('Net surplus') : get_phrase('Net deficit') }}</b>
    <b style="font-size:18px; color:{{ $net>=0 ? '#00955f':'#f04b24' }};">{{ $cur }} {{ number_format($net,2) }}</b>
  </div>
</div></div></div>
@endsection
