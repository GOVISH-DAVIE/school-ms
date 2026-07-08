@extends(auth()->user()->role_id == 4 ? 'accountant.navigation' : 'admin.navigation')

@section('content')
@php
  $cur = get_settings('system_currency') ?: 'USD';
  $balance = $funded - $spent;
  $pct = $project->budget_amount>0 ? min(100, round($spent*100/$project->budget_amount)) : 0;
  $sc = ['planning'=>'bg-secondary','ongoing'=>'bg-primary','completed'=>'bg-success'];
@endphp
<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
      <div class="d-flex flex-column">
        <h4>{{ $project->name }} <span class="badge {{ $sc[$project->status] ?? 'bg-secondary' }}">{{ ucfirst($project->status) }}</span></h4>
        <ul class="d-flex align-items-center eBreadcrumb-2"><li><a href="{{ route('admin.finance.projects') }}">{{ get_phrase('Projects') }}</a></li><li><a href="#">{{ get_phrase('Detail') }}</a></li></ul>
      </div>
      <a class="eBtn btn-secondary" href="{{ route('admin.finance.projects') }}">{{ get_phrase('Back') }}</a>
    </div>
  </div></div>
</div>

<div class="row">
  <div class="col-lg-8 mb-3">
    <div class="eSection-wrap mb-3">
      @if($project->description)<p>{{ $project->description }}</p>@endif
      <div class="row text-center">
        <div class="col"><div class="text-muted" style="font-size:12px;">{{ get_phrase('Budget') }}</div><div style="font-size:18px;font-weight:700;">{{ $cur }} {{ number_format($project->budget_amount,2) }}</div></div>
        <div class="col"><div class="text-muted" style="font-size:12px;">{{ get_phrase('Funded') }}</div><div style="font-size:18px;font-weight:700;color:#00955f;">{{ $cur }} {{ number_format($funded,2) }}</div></div>
        <div class="col"><div class="text-muted" style="font-size:12px;">{{ get_phrase('Spent') }}</div><div style="font-size:18px;font-weight:700;color:#f04b24;">{{ $cur }} {{ number_format($spent,2) }}</div></div>
        <div class="col"><div class="text-muted" style="font-size:12px;">{{ get_phrase('Cash balance') }}</div><div style="font-size:18px;font-weight:700;">{{ $cur }} {{ number_format($balance,2) }}</div></div>
      </div>
      <div style="height:9px;border-radius:5px;background:#eef0f4;overflow:hidden;margin-top:14px;">
        <span style="display:block;height:100%;width:{{ $pct }}%;background:{{ $spent>$project->budget_amount ? '#f04b24':'#00955f' }};"></span>
      </div>
      <small class="text-muted">{{ $pct }}% {{ get_phrase('of budget spent') }} @if($project->start_date) · {{ date('d M Y',$project->start_date) }}@endif @if($project->end_date) → {{ date('d M Y',$project->end_date) }}@endif</small>
    </div>

    <div class="eSection-wrap">
      <h6 class="mb-2">{{ get_phrase('Transactions') }}</h6>
      <div class="table-responsive">
        <table class="table eTable eTable-2">
          <thead><tr><th>{{ get_phrase('Date') }}</th><th>{{ get_phrase('Type') }}</th><th>{{ get_phrase('Description') }}</th><th class="text-end">{{ get_phrase('Amount') }}</th></tr></thead>
          <tbody>
            @forelse($txns as $t)
              <tr>
                <td>{{ $t->txn_date ? date('d M Y',$t->txn_date) : '' }}</td>
                <td><span class="badge {{ $t->type==='funding'?'bg-success':'bg-danger' }}">{{ ucfirst($t->type) }}</span></td>
                <td>{{ $t->description ?: '—' }}</td>
                <td class="text-end" style="color:{{ $t->type==='funding'?'#00955f':'#f04b24' }};">{{ $t->type==='funding'?'+':'-' }} {{ $cur }} {{ number_format($t->amount,2) }}</td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-center text-muted">{{ get_phrase('No transactions yet.') }}</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="eSection-wrap" style="position:sticky; top:20px;">
      <h6 class="mb-3">{{ get_phrase('Record transaction') }}</h6>
      <form method="POST" action="{{ route('admin.finance.project.txn', $project->id) }}">
        @csrf
        <div class="fpb-7"><label class="eForm-label">{{ get_phrase('Type') }}</label>
          <select name="type" class="form-select eForm-select">
            <option value="funding">{{ get_phrase('Funding received') }}</option>
            <option value="expense">{{ get_phrase('Project expense') }}</option>
          </select></div>
        <div class="fpb-7"><label class="eForm-label">{{ get_phrase('Amount') }} ({{ $cur }})</label>
          <input type="number" step="0.01" min="0.01" name="amount" class="form-control eForm-control" required></div>
        <div class="fpb-7"><label class="eForm-label">{{ get_phrase('Date') }}</label>
          <input type="date" name="txn_date" class="form-control eForm-control" value="{{ date('Y-m-d') }}"></div>
        <div class="fpb-7"><label class="eForm-label">{{ get_phrase('Description') }}</label>
          <input type="text" name="description" class="form-control eForm-control" placeholder="{{ get_phrase('Grant, contractor payment, etc.') }}"></div>
        <button class="btn-form w-100 mt-2" type="submit">{{ get_phrase('Record') }}</button>
      </form>
      <p class="text-muted mt-2" style="font-size:12px;">{{ get_phrase('Posts to the finance ledger so it appears in income/expense reports.') }}</p>
    </div>
  </div>
</div>
@endsection
