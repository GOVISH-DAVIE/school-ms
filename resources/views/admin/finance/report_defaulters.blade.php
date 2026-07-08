@extends(auth()->user()->role_id == 4 ? 'accountant.navigation' : 'admin.navigation')

@section('content')
@php $cur = get_settings('system_currency') ?: 'USD'; @endphp
<style>@media print{ .no-print{display:none!important;} .mainSection-title{display:none;} }</style>
<div class="mainSection-title no-print">
  <div class="row"><div class="col-12"><div class="d-flex flex-column">
    <h4>{{ get_phrase('Reports & statements') }}</h4>
    <ul class="d-flex align-items-center eBreadcrumb-2"><li><a href="#">{{ get_phrase('Finance') }}</a></li><li><a href="#">{{ get_phrase('Defaulters') }}</a></li></ul>
  </div></div></div>
</div>

<div class="no-print">@include('admin.finance._tabs')</div>

<div class="row"><div class="col-12"><div class="eSection-wrap">
  <div class="d-flex justify-content-between align-items-center flex-wrap mb-3" style="gap:10px;">
    <h5 class="mb-0">{{ get_phrase('Outstanding balances') }} · <span style="color:#f04b24;">{{ $cur }} {{ number_format($totalOutstanding,2) }}</span></h5>
    <form method="GET" action="{{ route('admin.finance.report.defaulters') }}" class="d-flex no-print" style="gap:8px;">
      <select name="class_id" class="form-select eForm-select">
        <option value="">{{ get_phrase('All classes') }}</option>
        @foreach($classes as $c)<option value="{{ $c->id }}" {{ (string)$class_id===(string)$c->id?'selected':'' }}>{{ $c->name }}</option>@endforeach
      </select>
      <button class="eBtn btn-primary" type="submit">{{ get_phrase('Filter') }}</button>
      <button type="button" class="eBtn btn-secondary" onclick="window.print()"><i class="bi bi-printer"></i></button>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table eTable eTable-2">
      <thead><tr><th>{{ get_phrase('Student') }}</th><th>{{ get_phrase('Class') }}</th><th>{{ get_phrase('Invoice') }}</th><th class="text-end">{{ get_phrase('Total') }}</th><th class="text-end">{{ get_phrase('Paid') }}</th><th class="text-end">{{ get_phrase('Balance') }}</th><th>{{ get_phrase('Due') }}</th></tr></thead>
      <tbody>
        @forelse($rows as $inv)
          @php $stu=\App\Models\User::find($inv->student_id); $cls=\App\Models\Classes::find($inv->class_id); @endphp
          <tr>
            <td>{{ $stu->name ?? '-' }}<br><small class="text-muted">{{ optional($stu)->email }}</small></td>
            <td>{{ $cls->name ?? '-' }}</td>
            <td>{{ $inv->invoice_no }}</td>
            <td class="text-end">{{ number_format($inv->total_amount,2) }}</td>
            <td class="text-end">{{ number_format($inv->paid_amount,2) }}</td>
            <td class="text-end"><b style="color:#f04b24;">{{ $cur }} {{ number_format($inv->balance,2) }}</b></td>
            <td>{{ $inv->due_date ? date('d M Y',$inv->due_date) : '—' }}</td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center text-success py-3"><i class="bi bi-check-circle"></i> {{ get_phrase('No outstanding balances. All fees cleared.') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div></div></div>
@endsection
