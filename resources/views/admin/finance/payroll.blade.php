@extends(auth()->user()->role_id == 4 ? 'accountant.navigation' : 'admin.navigation')

@section('content')
@php $cur = get_settings('system_currency') ?: 'USD'; $roles=[2=>'Admin',3=>'Teacher',4=>'Accountant',5=>'Librarian']; @endphp
<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
      <div class="d-flex flex-column">
        <h4>{{ get_phrase('Payroll') }}</h4>
        <ul class="d-flex align-items-center eBreadcrumb-2"><li><a href="#">{{ get_phrase('Finance') }}</a></li><li><a href="#">{{ get_phrase('Payroll') }}</a></li></ul>
      </div>
      <a class="eBtn btn-primary" href="{{ route('admin.finance.payslips') }}"><i class="bi bi-receipt"></i> {{ get_phrase('Payslips') }}</a>
    </div>
  </div></div>
</div>

<div class="row"><div class="col-12"><div class="eSection-wrap">
  <h6 class="mb-3">{{ get_phrase('Staff salary structures') }}</h6>
  <div class="table-responsive">
    <table class="table eTable eTable-2">
      <thead><tr><th>{{ get_phrase('Staff') }}</th><th>{{ get_phrase('Role') }}</th><th class="text-end">{{ get_phrase('Basic') }}</th><th class="text-end">{{ get_phrase('Net pay') }}</th><th>{{ get_phrase('Status') }}</th><th class="text-end">{{ get_phrase('Options') }}</th></tr></thead>
      <tbody>
        @foreach($staff as $s)
          @php $st = $structures[$s->id] ?? null; @endphp
          <tr>
            <td>{{ $s->name }}<br><small class="text-muted">{{ $s->email }}</small></td>
            <td>{{ $roles[$s->role_id] ?? '—' }}</td>
            <td class="text-end">{{ $st ? $cur.' '.number_format($st->basic_salary,2) : '—' }}</td>
            <td class="text-end"><b>{{ $st ? $cur.' '.number_format($st->net_pay,2) : '—' }}</b></td>
            <td>@if($st)<span class="badge bg-success">{{ get_phrase('Set') }}</span>@else<span class="badge bg-secondary">{{ get_phrase('Not set') }}</span>@endif</td>
            <td class="text-end"><a class="eBtn btn-primary" href="{{ route('admin.finance.salary.form', $s->id) }}">{{ $st ? get_phrase('Edit') : get_phrase('Set salary') }}</a></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div></div></div>
@endsection
