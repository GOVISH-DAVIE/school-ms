@extends(auth()->user()->role_id == 4 ? 'accountant.navigation' : 'admin.navigation')

@section('content')
@php $cur = get_settings('system_currency') ?: 'USD'; @endphp
<style>@media print{ .no-print{display:none!important;} .mainSection-title{display:none;} }</style>
<div class="mainSection-title no-print">
  <div class="row"><div class="col-12"><div class="d-flex flex-column">
    <h4>{{ get_phrase('Reports & statements') }}</h4>
    <ul class="d-flex align-items-center eBreadcrumb-2"><li><a href="#">{{ get_phrase('Finance') }}</a></li><li><a href="#">{{ get_phrase('Fee collection') }}</a></li></ul>
  </div></div></div>
</div>

<div class="no-print">@include('admin.finance._tabs')</div>

<div class="row"><div class="col-12"><div class="eSection-wrap">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0">{{ get_phrase('Fee collection by class') }}</h5>
    <button class="eBtn btn-secondary no-print" onclick="window.print()"><i class="bi bi-printer"></i> {{ get_phrase('Print') }}</button>
  </div>
  <div class="table-responsive">
    <table class="table eTable eTable-2">
      <thead><tr><th>{{ get_phrase('Class') }}</th><th class="text-end">{{ get_phrase('Invoices') }}</th><th class="text-end">{{ get_phrase('Billed') }}</th><th class="text-end">{{ get_phrase('Collected') }}</th><th class="text-end">{{ get_phrase('Outstanding') }}</th><th class="text-end">{{ get_phrase('Collected %') }}</th></tr></thead>
      <tbody>
        @forelse($rows as $r)
          @php $cls=\App\Models\Classes::find($r->class_id); $pct = $r->billed>0 ? round($r->collected*100/$r->billed) : 0; @endphp
          <tr>
            <td>{{ $cls->name ?? '-' }}</td>
            <td class="text-end">{{ $r->invoices }}</td>
            <td class="text-end">{{ $cur }} {{ number_format($r->billed,2) }}</td>
            <td class="text-end text-success">{{ $cur }} {{ number_format($r->collected,2) }}</td>
            <td class="text-end" style="color:#f04b24;">{{ $cur }} {{ number_format($r->outstanding,2) }}</td>
            <td class="text-end"><b>{{ $pct }}%</b></td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-muted">{{ get_phrase('No invoices yet.') }}</td></tr>
        @endforelse
      </tbody>
      <tfoot><tr>
        <th>{{ get_phrase('Total') }}</th><th></th>
        <th class="text-end">{{ $cur }} {{ number_format($totals['billed'],2) }}</th>
        <th class="text-end text-success">{{ $cur }} {{ number_format($totals['collected'],2) }}</th>
        <th class="text-end" style="color:#f04b24;">{{ $cur }} {{ number_format($totals['outstanding'],2) }}</th>
        <th class="text-end">{{ $totals['billed']>0 ? round($totals['collected']*100/$totals['billed']) : 0 }}%</th>
      </tr></tfoot>
    </table>
  </div>
</div></div></div>
@endsection
