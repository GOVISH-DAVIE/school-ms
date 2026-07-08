@extends(auth()->user()->role_id == 4 ? 'accountant.navigation' : 'admin.navigation')

@section('content')
@php
  $cur = get_settings('system_currency') ?: 'USD';
  $incPlan = collect($rows)->where('item.type','income')->sum(fn($r)=>(float)$r['item']->planned_amount);
  $incAct  = collect($rows)->where('item.type','income')->sum('actual');
  $expPlan = collect($rows)->where('item.type','expense')->sum(fn($r)=>(float)$r['item']->planned_amount);
  $expAct  = collect($rows)->where('item.type','expense')->sum('actual');
@endphp
<style>
  .bg-hero{ background:linear-gradient(135deg,#00955f,#007a4d); color:#fff; border-radius:14px; padding:18px 22px; margin-bottom:18px; display:flex; justify-content:space-between; flex-wrap:wrap; gap:12px; box-shadow:0 10px 30px rgba(0,149,95,.16); }
  .bg-hero h4{ color:#fff; margin:0; font-weight:700; }
  .bg-hero .box{ background:rgba(255,255,255,.15); border-radius:10px; padding:8px 16px; text-align:center; }
  .bg-hero .box .n{ font-size:17px; font-weight:700; } .bg-hero .box .l{ font-size:11px; opacity:.85; }
  .bar{ height:7px; border-radius:5px; background:#eef0f4; overflow:hidden; margin-top:4px; }
  .bar > span{ display:block; height:100%; }
</style>
<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
      <h4>{{ $budget->title }}</h4>
      <a class="eBtn btn-secondary" href="{{ route('admin.finance.budgets') }}">{{ get_phrase('Back') }}</a>
    </div>
  </div></div>
</div>

<div class="bg-hero">
  <div><h4>{{ get_phrase('Budget vs actual') }}</h4><div style="opacity:.9;font-size:13px;">{{ $budget->note }}</div></div>
  <div class="d-flex" style="gap:10px;">
    <div class="box"><div class="n">{{ $cur }} {{ number_format($incAct,0) }} / {{ number_format($incPlan,0) }}</div><div class="l">{{ get_phrase('Income (actual/plan)') }}</div></div>
    <div class="box"><div class="n">{{ $cur }} {{ number_format($expAct,0) }} / {{ number_format($expPlan,0) }}</div><div class="l">{{ get_phrase('Expense (actual/plan)') }}</div></div>
  </div>
</div>

<div class="row"><div class="col-12"><div class="eSection-wrap">
  <div class="table-responsive">
    <table class="table eTable eTable-2">
      <thead><tr><th>{{ get_phrase('Type') }}</th><th>{{ get_phrase('Category') }}</th><th class="text-end">{{ get_phrase('Planned') }}</th><th class="text-end">{{ get_phrase('Actual') }}</th><th class="text-end">{{ get_phrase('Variance') }}</th><th style="width:160px;">{{ get_phrase('Used') }}</th></tr></thead>
      <tbody>
        @forelse($rows as $r)
          @php
            $planned = (float)$r['item']->planned_amount; $actual = $r['actual'];
            $pct = $planned>0 ? min(100, round($actual*100/$planned)) : 0;
            $over = $r['item']->type==='expense' ? $actual > $planned : false;
          @endphp
          <tr>
            <td><span class="badge {{ $r['item']->type==='income'?'bg-success':'bg-danger' }}">{{ ucfirst($r['item']->type) }}</span></td>
            <td>{{ $r['item']->category }}</td>
            <td class="text-end">{{ $cur }} {{ number_format($planned,2) }}</td>
            <td class="text-end">{{ $cur }} {{ number_format($actual,2) }}</td>
            <td class="text-end" style="color:{{ $r['variance']<0 ? '#f04b24':'#00955f' }};">{{ $cur }} {{ number_format($r['variance'],2) }}</td>
            <td><div class="bar"><span style="width:{{ $pct }}%; background:{{ $over ? '#f04b24' : '#00955f' }};"></span></div><small class="text-muted">{{ $pct }}%</small></td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-muted">{{ get_phrase('No budget lines.') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <p class="text-muted mt-2" style="font-size:12px;">{{ get_phrase('Actuals are pulled live from the finance ledger, matched by category for this session.') }}</p>
</div></div></div>
@endsection
