@extends(auth()->user()->role_id == 4 ? 'accountant.navigation' : 'admin.navigation')

@section('content')
@php $cur = get_settings('system_currency') ?: 'USD'; @endphp
<style>
  .kpi{ display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:18px; }
  .kpi .c{ background:#fff; border:1px solid #eef0f4; border-radius:12px; padding:16px 18px; }
  .kpi .c .l{ font-size:12px; color:#8a92a5; text-transform:uppercase; letter-spacing:.04em; }
  .kpi .c .n{ font-size:22px; font-weight:700; margin-top:5px; }
  .kpi .c.net .n{ color:#00955f; } .kpi .c.out .n{ color:#f04b24; }
  @media(max-width:900px){ .kpi{ grid-template-columns:1fr 1fr; } }
  .catrow{ display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px dashed #eef0f4; font-size:14px; }
</style>
<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
      <div class="d-flex flex-column">
        <h4>{{ get_phrase('Finance dashboard') }}</h4>
        <ul class="d-flex align-items-center eBreadcrumb-2"><li><a href="#">{{ get_phrase('Finance') }}</a></li><li><a href="#">{{ get_phrase('Dashboard') }}</a></li></ul>
      </div>
      <a class="eBtn btn-secondary" href="{{ route('admin.finance.report.income') }}">{{ get_phrase('Reports') }}</a>
    </div>
  </div></div>
</div>

<div class="kpi">
  <div class="c"><div class="l">{{ get_phrase('Collected this month') }}</div><div class="n" style="color:#00955f;">{{ $cur }} {{ number_format($collectedMonth,0) }}</div></div>
  <div class="c out"><div class="l">{{ get_phrase('Outstanding fees') }}</div><div class="n">{{ $cur }} {{ number_format($outstanding,0) }}</div></div>
  <div class="c"><div class="l">{{ get_phrase('Total income') }}</div><div class="n">{{ $cur }} {{ number_format($income,0) }}</div></div>
  <div class="c net"><div class="l">{{ get_phrase('Net (income − expense)') }}</div><div class="n">{{ $cur }} {{ number_format($net,0) }}</div></div>
</div>

<div class="row">
  <div class="col-lg-4 mb-3"><div class="eSection-wrap h-100">
    <h6 class="mb-2 text-success">{{ get_phrase('Income by category') }}</h6>
    @forelse($incomeByCat as $c)
      <div class="catrow"><span>{{ $c->category ?: get_phrase('Uncategorised') }}</span><b>{{ $cur }} {{ number_format($c->t,2) }}</b></div>
    @empty <p class="text-muted mb-0">{{ get_phrase('No income yet.') }}</p> @endforelse
  </div></div>
  <div class="col-lg-4 mb-3"><div class="eSection-wrap h-100">
    <h6 class="mb-2" style="color:#f04b24;">{{ get_phrase('Expense by category') }}</h6>
    @forelse($expenseByCat as $c)
      <div class="catrow"><span>{{ $c->category ?: get_phrase('Uncategorised') }}</span><b>{{ $cur }} {{ number_format($c->t,2) }}</b></div>
    @empty <p class="text-muted mb-0">{{ get_phrase('No expenses yet.') }}</p> @endforelse
  </div></div>
  <div class="col-lg-4 mb-3"><div class="eSection-wrap h-100">
    <h6 class="mb-2">{{ get_phrase('Recent transactions') }}</h6>
    @forelse($recent as $t)
      <div class="catrow">
        <span><span class="badge {{ $t->type==='income'?'bg-success':'bg-danger' }}">{{ $t->type==='income'?'+':'−' }}</span> {{ \Illuminate\Support\Str::limit($t->description ?: $t->category, 28) }}</span>
        <b>{{ number_format($t->amount,0) }}</b>
      </div>
    @empty <p class="text-muted mb-0">{{ get_phrase('No activity yet.') }}</p> @endforelse
  </div></div>
</div>
@endsection
