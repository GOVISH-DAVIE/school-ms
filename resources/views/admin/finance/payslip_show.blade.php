@extends(auth()->user()->role_id == 4 ? 'accountant.navigation' : 'admin.navigation')

@section('content')
@php
  $cur = get_settings('system_currency') ?: 'USD';
  $allow = is_array($slip->allowances) ? $slip->allowances : [];
  $deduct = is_array($slip->deductions) ? $slip->deductions : [];
@endphp
<style>
  .slip{ max-width:680px; margin:0 auto; background:#fff; border:1px solid #eef0f4; border-radius:12px; overflow:hidden; }
  .slip-head{ background:linear-gradient(135deg,#00955f,#007a4d); color:#fff; padding:20px 26px; display:flex; justify-content:space-between; align-items:center; }
  .slip-head h4{ color:#fff; margin:0; font-weight:700; } .slip-head p{ margin:2px 0 0; font-size:13px; opacity:.9; }
  .slip-body{ padding:22px 26px; }
  .slip-body .who{ display:flex; justify-content:space-between; border-bottom:1px dashed #eef0f4; padding-bottom:12px; margin-bottom:12px; }
  .slip-body table{ width:100%; }
  .slip-body td{ padding:6px 0; font-size:14px; }
  .slip-body .sec{ font-weight:700; color:#00955f; padding-top:8px; }
  .slip-body .sec.d{ color:#f04b24; }
  .slip-net{ display:flex; justify-content:space-between; background:#f5faf8; border-radius:8px; padding:12px 16px; margin-top:14px; }
  .slip-net b:last-child{ color:#00955f; font-size:20px; }
  @media print{ .no-print{display:none!important;} .mainSection-title{display:none;} }
</style>
<div class="mainSection-title no-print">
  <div class="row"><div class="col-12"><div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
    <h4>{{ get_phrase('Payslip') }} · {{ $slip->month }}</h4>
    <div class="d-flex" style="gap:8px;">
      <a class="eBtn btn-primary" href="javascript:;" onclick="window.print()"><i class="bi bi-printer"></i> {{ get_phrase('Print') }}</a>
      <a class="eBtn btn-secondary" href="{{ route('admin.finance.payslips', ['month'=>$slip->month]) }}">{{ get_phrase('Back') }}</a>
    </div>
  </div></div></div>
</div>

<div class="slip">
  <div class="slip-head">
    <div><h4>{{ $school->title ?? 'School' }}</h4><p>{{ $school->address ?? '' }}</p></div>
    <div class="text-end"><div style="font-weight:700;">{{ get_phrase('PAYSLIP') }}</div><div style="font-size:13px;">{{ $slip->month }}</div></div>
  </div>
  <div class="slip-body">
    <div class="who">
      <div><b>{{ optional($staff)->name }}</b><br><small class="text-muted">{{ optional($staff)->email }}</small></div>
      <div class="text-end"><span class="badge {{ $slip->status==='paid'?'bg-success':'bg-secondary' }}">{{ ucfirst($slip->status) }}</span>
        @if($slip->paid_on)<br><small class="text-muted">{{ date('d M Y',$slip->paid_on) }}</small>@endif</div>
    </div>
    <table>
      <tr><td>{{ get_phrase('Basic salary') }}</td><td class="text-end">{{ $cur }} {{ number_format($slip->basic,2) }}</td></tr>
      @if(count($allow))<tr><td class="sec" colspan="2">{{ get_phrase('Allowances') }}</td></tr>
        @foreach($allow as $a)<tr><td style="padding-left:14px;">{{ $a['name'] }}</td><td class="text-end text-success">+ {{ number_format($a['amount'],2) }}</td></tr>@endforeach
      @endif
      @if(count($deduct))<tr><td class="sec d" colspan="2">{{ get_phrase('Deductions') }}</td></tr>
        @foreach($deduct as $d)<tr><td style="padding-left:14px;">{{ $d['name'] }}</td><td class="text-end" style="color:#f04b24;">− {{ number_format($d['amount'],2) }}</td></tr>@endforeach
      @endif
    </table>
    <div class="slip-net"><b>{{ get_phrase('NET PAY') }}</b><b>{{ $cur }} {{ number_format($slip->net_pay,2) }}</b></div>
  </div>
</div>
@endsection
