@extends(auth()->user()->role_id == 4 ? 'accountant.navigation' : 'admin.navigation')

@section('content')
@php $cur = get_settings('system_currency') ?: 'USD'; $badge=['unpaid'=>'bg-danger','partial'=>'bg-primary','paid'=>'bg-success']; @endphp
<style>
  .st-hero{ background:linear-gradient(135deg,#00955f,#007a4d); color:#fff; border-radius:14px; padding:20px 24px; margin-bottom:18px;
    display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; box-shadow:0 10px 30px rgba(0,149,95,.16); }
  .st-hero h4{ color:#fff; margin:0; font-weight:700; }
  .st-hero p{ color:rgba(255,255,255,.9); margin:3px 0 0; font-size:14px; }
  .st-hero .box{ background:rgba(255,255,255,.15); border-radius:10px; padding:8px 16px; text-align:center; }
  .st-hero .box .n{ font-size:18px; font-weight:700; } .st-hero .box .l{ font-size:11px; opacity:.85; }
  @media print{ .no-print{ display:none !important; } }
</style>
<div class="mainSection-title no-print">
  <div class="row"><div class="col-12">
    <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
      <h4>{{ get_phrase('Student statement') }}</h4>
      <div class="d-flex" style="gap:8px;">
        <a class="eBtn btn-primary" href="javascript:;" onclick="window.print()"><i class="bi bi-printer"></i> {{ get_phrase('Print') }}</a>
        <a class="eBtn btn-secondary" href="{{ route('admin.finance.invoices') }}">{{ get_phrase('Back') }}</a>
      </div>
    </div>
  </div></div>
</div>

<div class="st-hero">
  <div>
    <h4>{{ $student->name }}</h4>
    <p>{{ $class->name ?? '' }} · {{ get_phrase('Financial statement') }}</p>
  </div>
  <div class="d-flex" style="gap:10px;">
    <div class="box"><div class="n">{{ $cur }} {{ number_format($totals['billed'],2) }}</div><div class="l">{{ get_phrase('Billed') }}</div></div>
    <div class="box"><div class="n">{{ $cur }} {{ number_format($totals['paid'],2) }}</div><div class="l">{{ get_phrase('Paid') }}</div></div>
    <div class="box"><div class="n">{{ $cur }} {{ number_format($totals['balance'],2) }}</div><div class="l">{{ get_phrase('Balance') }}</div></div>
  </div>
</div>

<div class="row">
  <div class="col-lg-7 mb-3"><div class="eSection-wrap">
    <h6 class="mb-2">{{ get_phrase('Invoices') }}</h6>
    <table class="table eTable eTable-2">
      <thead><tr><th>{{ get_phrase('Invoice') }}</th><th class="text-end">{{ get_phrase('Total') }}</th><th class="text-end">{{ get_phrase('Paid') }}</th><th class="text-end">{{ get_phrase('Balance') }}</th><th>{{ get_phrase('Status') }}</th></tr></thead>
      <tbody>
        @forelse($invoices as $inv)
          <tr>
            <td>{{ $inv->invoice_no }}<br><small class="text-muted">{{ $inv->title }}</small></td>
            <td class="text-end">{{ number_format($inv->total_amount,2) }}</td>
            <td class="text-end">{{ number_format($inv->paid_amount,2) }}</td>
            <td class="text-end">{{ number_format($inv->balance,2) }}</td>
            <td><span class="badge {{ $badge[$inv->status] ?? 'bg-secondary' }}">{{ ucfirst($inv->status) }}</span></td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-muted">{{ get_phrase('No invoices.') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div></div>

  <div class="col-lg-5 mb-3"><div class="eSection-wrap">
    <h6 class="mb-2">{{ get_phrase('Payments') }}</h6>
    <table class="table eTable eTable-2">
      <thead><tr><th>{{ get_phrase('Receipt') }}</th><th>{{ get_phrase('Date') }}</th><th class="text-end">{{ get_phrase('Amount') }}</th></tr></thead>
      <tbody>
        @forelse($payments as $p)
          <tr><td>{{ $p->receipt_no }}<br><small class="text-muted">{{ ucfirst($p->method) }}</small></td><td>{{ $p->paid_on ? date('d M y', $p->paid_on) : '' }}</td><td class="text-end">{{ number_format($p->amount,2) }}</td></tr>
        @empty
          <tr><td colspan="3" class="text-center text-muted">{{ get_phrase('No payments.') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div></div>
</div>
@endsection
