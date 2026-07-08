@extends(auth()->user()->role_id == 4 ? 'accountant.navigation' : 'admin.navigation')

@section('content')
@php $cur = get_settings('system_currency') ?: 'USD'; @endphp
<style>
  .fin-cards{ display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:18px; }
  .fin-card{ background:#fff; border:1px solid #eef0f4; border-radius:12px; padding:18px 20px; }
  .fin-card .l{ font-size:12px; color:#8a92a5; text-transform:uppercase; letter-spacing:.04em; }
  .fin-card .n{ font-size:24px; font-weight:700; margin-top:4px; }
  .fin-card.billed .n{ color:#181c32; } .fin-card.collected .n{ color:#00955f; } .fin-card.out .n{ color:#f04b24; }
  @media(max-width:800px){ .fin-cards{ grid-template-columns:1fr; } }
</style>
<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex flex-column">
      <h4>{{ get_phrase('Invoices & payments') }}</h4>
      <ul class="d-flex align-items-center eBreadcrumb-2">
        <li><a href="#">{{ get_phrase('Finance') }}</a></li>
        <li><a href="#">{{ get_phrase('Invoices') }}</a></li>
      </ul>
    </div>
  </div></div>
</div>

<div class="fin-cards">
  <div class="fin-card billed"><div class="l">{{ get_phrase('Total billed') }}</div><div class="n">{{ $cur }} {{ number_format($summary['billed'],2) }}</div></div>
  <div class="fin-card collected"><div class="l">{{ get_phrase('Collected') }}</div><div class="n">{{ $cur }} {{ number_format($summary['collected'],2) }}</div></div>
  <div class="fin-card out"><div class="l">{{ get_phrase('Outstanding') }}</div><div class="n">{{ $cur }} {{ number_format($summary['outstanding'],2) }}</div></div>
</div>

<div class="row"><div class="col-12"><div class="eSection-wrap">
  <form method="GET" action="{{ route('admin.finance.invoices') }}" class="row g-2 mb-3">
    <div class="col-md-3">
      <select name="class_id" class="form-select eForm-select">
        <option value="">{{ get_phrase('All classes') }}</option>
        @foreach($classes as $c)<option value="{{ $c->id }}" {{ (string)$class_id===(string)$c->id?'selected':'' }}>{{ $c->name }}</option>@endforeach
      </select>
    </div>
    <div class="col-md-3">
      <select name="status" class="form-select eForm-select">
        <option value="">{{ get_phrase('All statuses') }}</option>
        @foreach(['unpaid'=>'Unpaid','partial'=>'Partial','paid'=>'Paid'] as $k=>$v)
          <option value="{{ $k }}" {{ $status===$k?'selected':'' }}>{{ get_phrase($v) }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3"><input type="text" name="search" value="{{ $search }}" class="form-control eForm-control" placeholder="{{ get_phrase('Student or invoice no.') }}"></div>
    <div class="col-md-2"><button class="eBtn btn-primary w-100" type="submit">{{ get_phrase('Filter') }}</button></div>
  </form>

  <div class="table-responsive">
    <table class="table eTable eTable-2">
      <thead><tr>
        <th>{{ get_phrase('Invoice') }}</th><th>{{ get_phrase('Student') }}</th><th>{{ get_phrase('Class') }}</th>
        <th class="text-end">{{ get_phrase('Total') }}</th><th class="text-end">{{ get_phrase('Paid') }}</th><th class="text-end">{{ get_phrase('Balance') }}</th>
        <th>{{ get_phrase('Status') }}</th><th class="text-end">{{ get_phrase('Options') }}</th>
      </tr></thead>
      <tbody>
        @php $badge=['unpaid'=>'bg-danger','partial'=>'bg-primary','paid'=>'bg-success']; @endphp
        @forelse($invoices as $inv)
          @php $stu=\App\Models\User::find($inv->student_id); $cls=\App\Models\Classes::find($inv->class_id); @endphp
          <tr>
            <td>{{ $inv->invoice_no }}<br><small class="text-muted">{{ $inv->title }}</small></td>
            <td>{{ $stu->name ?? '-' }}</td>
            <td>{{ $cls->name ?? '-' }}</td>
            <td class="text-end">{{ $cur }} {{ number_format($inv->total_amount,2) }}</td>
            <td class="text-end">{{ $cur }} {{ number_format($inv->paid_amount,2) }}</td>
            <td class="text-end"><b>{{ $cur }} {{ number_format($inv->balance,2) }}</b></td>
            <td><span class="badge {{ $badge[$inv->status] ?? 'bg-secondary' }}">{{ ucfirst($inv->status) }}</span></td>
            <td class="text-end"><a class="eBtn btn-primary" href="{{ route('admin.finance.invoice.show', $inv->id) }}">{{ get_phrase('Open') }}</a></td>
          </tr>
        @empty
          <tr><td colspan="8" class="text-center text-muted">{{ get_phrase('No invoices. Create a fee structure and generate invoices.') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-3">{{ $invoices->links() }}</div>
</div></div></div>
@endsection
