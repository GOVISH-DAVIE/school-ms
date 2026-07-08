@extends('student.navigation')

@section('content')
@php $cur = get_settings('system_currency') ?: 'USD'; $badge=['unpaid'=>'bg-danger','partial'=>'bg-primary','paid'=>'bg-success']; @endphp
<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
      <div class="d-flex flex-column">
        <h4>{{ $invoice->invoice_no }} <span class="badge {{ $badge[$invoice->status] ?? 'bg-secondary' }}">{{ ucfirst($invoice->status) }}</span></h4>
        <ul class="d-flex align-items-center eBreadcrumb-2"><li><a href="{{ route('student.finance.invoices') }}">{{ get_phrase('My fees') }}</a></li><li><a href="#">{{ $invoice->title }}</a></li></ul>
      </div>
      <a class="eBtn btn-secondary" href="{{ route('student.finance.invoices') }}">{{ get_phrase('Back') }}</a>
    </div>
  </div></div>
</div>

<div class="row"><div class="col-lg-8 offset-lg-2">
  <div class="eSection-wrap mb-3">
    <table class="table eTable eTable-2">
      <thead><tr><th>{{ get_phrase('Item') }}</th><th class="text-end">{{ get_phrase('Amount') }}</th></tr></thead>
      <tbody>
        @foreach($invoice->items as $it)<tr><td>{{ $it->title }}</td><td class="text-end">{{ $cur }} {{ number_format($it->amount,2) }}</td></tr>@endforeach
      </tbody>
      <tfoot>
        <tr><th>{{ get_phrase('Total') }}</th><th class="text-end">{{ $cur }} {{ number_format($invoice->total_amount + $invoice->fine - $invoice->discount,2) }}</th></tr>
        <tr><td>{{ get_phrase('Paid') }}</td><td class="text-end text-success">{{ $cur }} {{ number_format($invoice->paid_amount,2) }}</td></tr>
        <tr><th>{{ get_phrase('Balance due') }}</th><th class="text-end" style="color:#f04b24;">{{ $cur }} {{ number_format($invoice->balance,2) }}</th></tr>
      </tfoot>
    </table>
    @if($invoice->balance > 0)
      <p class="text-muted mb-0" style="font-size:13px;"><i class="bi bi-info-circle"></i> {{ get_phrase('Please clear the balance by the due date') }}{{ $invoice->due_date ? ' ('.date('d M Y', $invoice->due_date).')' : '' }}. {{ get_phrase('Pay at the accounts office or online where available.') }}</p>
    @endif
  </div>

  <div class="eSection-wrap">
    <h6 class="mb-2">{{ get_phrase('Payment history') }}</h6>
    @forelse($invoice->payments as $p)
      <div class="d-flex justify-content-between py-2" style="border-bottom:1px dashed #eef0f4;">
        <div><b>{{ $cur }} {{ number_format($p->amount,2) }}</b> · <span class="text-muted">{{ ucfirst($p->method) }}</span>
          <small class="text-muted d-block">{{ $p->receipt_no }}</small></div>
        <span class="text-muted">{{ $p->paid_on ? date('d M Y', $p->paid_on) : '' }}</span>
      </div>
    @empty
      <p class="text-muted mb-0">{{ get_phrase('No payments recorded yet.') }}</p>
    @endforelse
  </div>
</div></div>
@endsection
