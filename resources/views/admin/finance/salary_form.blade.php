@extends(auth()->user()->role_id == 4 ? 'accountant.navigation' : 'admin.navigation')

@section('content')
@php
  $cur = get_settings('system_currency') ?: 'USD';
  $allow = $structure && is_array($structure->allowances) ? $structure->allowances : [];
  $deduct = $structure && is_array($structure->deductions) ? $structure->deductions : [];
@endphp
<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
      <div class="d-flex flex-column">
        <h4>{{ get_phrase('Salary structure') }} — {{ $staff->name }}</h4>
        <ul class="d-flex align-items-center eBreadcrumb-2"><li><a href="{{ route('admin.finance.payroll') }}">{{ get_phrase('Payroll') }}</a></li><li><a href="#">{{ $staff->name }}</a></li></ul>
      </div>
      <a class="eBtn btn-secondary" href="{{ route('admin.finance.payroll') }}">{{ get_phrase('Back') }}</a>
    </div>
  </div></div>
</div>

<div class="row"><div class="col-lg-8"><div class="eSection-wrap">
  <form method="POST" action="{{ route('admin.finance.salary.save', $staff->id) }}">
    @csrf
    <div class="fpb-7" style="max-width:300px;">
      <label class="eForm-label">{{ get_phrase('Basic salary') }} ({{ $cur }})</label>
      <input type="number" step="0.01" min="0" name="basic_salary" id="basic" class="form-control eForm-control" value="{{ $structure->basic_salary ?? '' }}" required>
    </div>

    <div class="row">
      <div class="col-md-6">
        <label class="eForm-label text-success">{{ get_phrase('Allowances') }}</label>
        <table class="table eTable eTable-2"><tbody id="allow-body"></tbody></table>
        <a href="javascript:;" class="eBtn btn-secondary" onclick="addRow('allow')"><i class="bi bi-plus"></i> {{ get_phrase('Add allowance') }}</a>
      </div>
      <div class="col-md-6">
        <label class="eForm-label" style="color:#f04b24;">{{ get_phrase('Deductions') }}</label>
        <table class="table eTable eTable-2"><tbody id="deduct-body"></tbody></table>
        <a href="javascript:;" class="eBtn btn-secondary" onclick="addRow('deduct')"><i class="bi bi-plus"></i> {{ get_phrase('Add deduction') }}</a>
      </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3 p-3" style="background:#f5faf8; border-radius:10px;">
      <b>{{ get_phrase('Net pay') }}</b>
      <b id="net" style="font-size:18px; color:#00955f;">{{ $cur }} 0.00</b>
    </div>
    <button class="btn-form mt-3" type="submit">{{ get_phrase('Save salary structure') }}</button>
  </form>
</div></div></div>

<script>
  "use strict";
  var CUR = '{{ $cur }}';
  function addRow(kind, name, amount){
    var row = document.createElement('tr');
    row.innerHTML =
      '<td><input name="'+kind+'_name[]" class="form-control eForm-control" value="'+(name||'')+'" placeholder="'+(kind==='allow'?'House, Transport...':'PAYE, NHIF, Loan...')+'"></td>'+
      '<td style="width:150px;"><input name="'+kind+'_amount[]" type="number" step="0.01" min="0" class="form-control eForm-control amt" value="'+(amount||'')+'" placeholder="0.00"></td>'+
      '<td style="width:40px;" class="text-center"><a href="javascript:;" class="text-danger" onclick="this.closest(\'tr\').remove();calcNet()"><i class="bi bi-x-circle"></i></a></td>';
    document.getElementById(kind+'-body').appendChild(row);
  }
  function calcNet(){
    var basic = parseFloat(document.getElementById('basic').value)||0, a=0, d=0;
    document.querySelectorAll('#allow-body .amt').forEach(function(i){ a+=parseFloat(i.value)||0; });
    document.querySelectorAll('#deduct-body .amt').forEach(function(i){ d+=parseFloat(i.value)||0; });
    document.getElementById('net').textContent = CUR+' '+(basic+a-d).toFixed(2);
  }
  document.addEventListener('input', calcNet);
  @foreach($allow as $a) addRow('allow', @json($a['name']), {{ $a['amount'] }}); @endforeach
  @foreach($deduct as $d) addRow('deduct', @json($d['name']), {{ $d['amount'] }}); @endforeach
  calcNet();
</script>
@endsection
