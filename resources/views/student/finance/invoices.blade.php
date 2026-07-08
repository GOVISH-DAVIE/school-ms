@extends('student.navigation')

@section('content')
@php $cur = get_settings('system_currency') ?: 'USD'; $badge=['unpaid'=>'bg-danger','partial'=>'bg-primary','paid'=>'bg-success']; @endphp
<style>
  .fee-hero{ background:linear-gradient(135deg,#00955f,#007a4d); color:#fff; border-radius:14px; padding:20px 24px; margin-bottom:18px;
    display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; box-shadow:0 10px 30px rgba(0,149,95,.16); }
  .fee-hero h4{ color:#fff; margin:0; font-weight:700; }
  .fee-hero .box{ background:rgba(255,255,255,.15); border-radius:10px; padding:8px 18px; text-align:center; }
  .fee-hero .box .n{ font-size:20px; font-weight:700; } .fee-hero .box .l{ font-size:11px; opacity:.85; }
</style>
<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex flex-column">
      <h4>{{ get_phrase('My fees') }}</h4>
      <ul class="d-flex align-items-center eBreadcrumb-2"><li><a href="#">{{ get_phrase('Home') }}</a></li><li><a href="#">{{ get_phrase('Fees') }}</a></li></ul>
    </div>
  </div></div>
</div>

<div class="fee-hero">
  <div><h4>{{ get_phrase('Fee summary') }}</h4></div>
  <div class="d-flex" style="gap:10px;">
    <div class="box"><div class="n">{{ $cur }} {{ number_format($totals['billed'],2) }}</div><div class="l">{{ get_phrase('Billed') }}</div></div>
    <div class="box"><div class="n">{{ $cur }} {{ number_format($totals['paid'],2) }}</div><div class="l">{{ get_phrase('Paid') }}</div></div>
    <div class="box"><div class="n">{{ $cur }} {{ number_format($totals['balance'],2) }}</div><div class="l">{{ get_phrase('Balance due') }}</div></div>
  </div>
</div>

<div class="row"><div class="col-12"><div class="eSection-wrap">
  <div class="table-responsive">
    <table class="table eTable eTable-2">
      <thead><tr><th>{{ get_phrase('Invoice') }}</th><th class="text-end">{{ get_phrase('Total') }}</th><th class="text-end">{{ get_phrase('Paid') }}</th><th class="text-end">{{ get_phrase('Balance') }}</th><th>{{ get_phrase('Due') }}</th><th>{{ get_phrase('Status') }}</th><th class="text-end">{{ get_phrase('Options') }}</th></tr></thead>
      <tbody>
        @forelse($invoices as $inv)
          <tr>
            <td>{{ $inv->invoice_no }}<br><small class="text-muted">{{ $inv->title }}</small></td>
            <td class="text-end">{{ $cur }} {{ number_format($inv->total_amount,2) }}</td>
            <td class="text-end">{{ $cur }} {{ number_format($inv->paid_amount,2) }}</td>
            <td class="text-end"><b>{{ $cur }} {{ number_format($inv->balance,2) }}</b></td>
            <td>{{ $inv->due_date ? date('d M Y', $inv->due_date) : '—' }}</td>
            <td><span class="badge {{ $badge[$inv->status] ?? 'bg-secondary' }}">{{ ucfirst($inv->status) }}</span></td>
            <td class="text-end"><a class="eBtn btn-primary" href="{{ route('student.finance.invoice.show', $inv->id) }}">{{ get_phrase('View') }}</a></td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center text-muted">{{ get_phrase('No invoices yet.') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div></div></div>
@endsection
