@extends(auth()->user()->role_id == 4 ? 'accountant.navigation' : 'admin.navigation')

@section('content')
@php $cur = get_settings('system_currency') ?: 'USD'; @endphp
<style>
  .rcpt{ max-width:640px; margin:0 auto; background:#fff; border:1px solid #eef0f4; border-radius:12px; overflow:hidden; }
  .rcpt-head{ background:linear-gradient(135deg,#00955f,#007a4d); color:#fff; padding:22px 26px; }
  .rcpt-head h4{ color:#fff; margin:0; font-weight:700; }
  .rcpt-head p{ color:rgba(255,255,255,.9); margin:2px 0 0; font-size:13px; }
  .rcpt-body{ padding:24px 26px; }
  .rcpt-body .kv{ display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px dashed #eef0f4; font-size:14px; }
  .rcpt-body .kv .k{ color:#8a92a5; }
  .rcpt-amount{ text-align:center; margin:18px 0; }
  .rcpt-amount .n{ font-size:32px; font-weight:800; color:#00955f; }
  .rcpt-amount .l{ font-size:12px; color:#8a92a5; text-transform:uppercase; letter-spacing:.05em; }
  @media print{ .no-print{ display:none !important; } .mainSection-title{ display:none; } }
</style>

<div class="mainSection-title no-print">
  <div class="row"><div class="col-12">
    <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
      <h4>{{ get_phrase('Receipt') }} {{ $payment->receipt_no }}</h4>
      <div class="d-flex" style="gap:8px;">
        <a class="eBtn btn-primary" href="javascript:;" onclick="window.print()"><i class="bi bi-printer"></i> {{ get_phrase('Print') }}</a>
        <a class="eBtn btn-secondary" href="{{ route('admin.finance.invoice.show', $payment->invoice_id) }}">{{ get_phrase('Back to invoice') }}</a>
      </div>
    </div>
  </div></div>
</div>

<div class="rcpt">
  <div class="rcpt-head d-flex justify-content-between align-items-center">
    <div>
      <h4>{{ $school->title ?? 'School' }}</h4>
      <p>{{ $school->address ?? '' }}</p>
    </div>
    <div class="text-end">
      <div style="font-weight:700;">{{ get_phrase('RECEIPT') }}</div>
      <div style="font-size:13px; opacity:.9;">{{ $payment->receipt_no }}</div>
    </div>
  </div>
  <div class="rcpt-body">
    <div class="kv"><span class="k">{{ get_phrase('Received from') }}</span><span>{{ $student->name ?? '-' }}</span></div>
    <div class="kv"><span class="k">{{ get_phrase('Invoice') }}</span><span>{{ optional($invoice)->invoice_no }} — {{ optional($invoice)->title }}</span></div>
    <div class="kv"><span class="k">{{ get_phrase('Payment method') }}</span><span>{{ ucfirst($payment->method) }}</span></div>
    @if($payment->reference)<div class="kv"><span class="k">{{ get_phrase('Reference') }}</span><span>{{ $payment->reference }}</span></div>@endif
    <div class="kv"><span class="k">{{ get_phrase('Date') }}</span><span>{{ $payment->paid_on ? date('d M Y', $payment->paid_on) : '' }}</span></div>

    <div class="rcpt-amount">
      <div class="n">{{ $cur }} {{ number_format($payment->amount,2) }}</div>
      <div class="l">{{ get_phrase('Amount paid') }}</div>
    </div>

    @if($invoice)
      <div class="kv"><span class="k">{{ get_phrase('Invoice balance now') }}</span><span style="color:#f04b24; font-weight:600;">{{ $cur }} {{ number_format($invoice->balance,2) }}</span></div>
    @endif
    <p class="text-center text-muted mt-3" style="font-size:12px;">{{ get_phrase('Thank you for your payment.') }}</p>
  </div>
</div>
@endsection
