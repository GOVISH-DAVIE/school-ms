@extends(auth()->user()->role_id == 4 ? 'accountant.navigation' : 'admin.navigation')

@section('content')
@php $cur = get_settings('system_currency') ?: 'USD'; @endphp
<div class="mainSection-title">
  <div class="row"><div class="col-12">
    <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
      <div class="d-flex flex-column">
        <h4>{{ get_phrase('New budget') }}</h4>
        <ul class="d-flex align-items-center eBreadcrumb-2"><li><a href="{{ route('admin.finance.budgets') }}">{{ get_phrase('Budgets') }}</a></li><li><a href="#">{{ get_phrase('New') }}</a></li></ul>
      </div>
      <a class="eBtn btn-secondary" href="{{ route('admin.finance.budgets') }}">{{ get_phrase('Back') }}</a>
    </div>
  </div></div>
</div>

<div class="row"><div class="col-lg-9"><div class="eSection-wrap">
  <form method="POST" action="{{ route('admin.finance.budget.store') }}">
    @csrf
    <div class="row">
      <div class="col-md-8 fpb-7">
        <label class="eForm-label">{{ get_phrase('Budget title') }}</label>
        <input type="text" name="title" class="form-control eForm-control" placeholder="{{ get_phrase('e.g. 2026/2027 Annual budget') }}" required>
      </div>
      <div class="col-md-4 fpb-7">
        <label class="eForm-label">{{ get_phrase('Note (optional)') }}</label>
        <input type="text" name="note" class="form-control eForm-control">
      </div>
    </div>

    <label class="eForm-label mt-2">{{ get_phrase('Budget lines') }}</label>
    <table class="table eTable eTable-2" id="lines">
      <thead><tr><th style="width:140px;">{{ get_phrase('Type') }}</th><th>{{ get_phrase('Category') }}</th><th style="width:200px;">{{ get_phrase('Planned') }} ({{ $cur }})</th><th style="width:50px;"></th></tr></thead>
      <tbody id="lines-body">
        <!-- rows injected -->
      </tbody>
    </table>
    <a href="javascript:;" class="eBtn btn-secondary" onclick="addLine()"><i class="bi bi-plus"></i> {{ get_phrase('Add line') }}</a>
    <div class="mt-3"><button class="btn-form" type="submit">{{ get_phrase('Create budget') }}</button></div>
  </form>
</div></div></div>

<script>
  "use strict";
  var cats = {!! json_encode($categories->values()) !!};
  function addLine(type, cat, amt){
    var opts = '';
    ['income','expense'].forEach(function(t){ opts += '<option value="'+t+'" '+((type===t)?'selected':'')+'>'+t.charAt(0).toUpperCase()+t.slice(1)+'</option>'; });
    var list = cats.map(function(c){ return '<option value="'+c+'">'; }).join('');
    var row = document.createElement('tr');
    row.innerHTML =
      '<td><select name="type[]" class="form-select eForm-select">'+opts+'</select></td>'+
      '<td><input name="category[]" class="form-control eForm-control" list="catlist" value="'+(cat||'')+'" placeholder="Tuition, Salaries, Utilities..."></td>'+
      '<td><input name="planned[]" type="number" step="0.01" min="0" class="form-control eForm-control" value="'+(amt||'')+'" placeholder="0.00"></td>'+
      '<td class="text-center"><a href="javascript:;" class="text-danger" onclick="this.closest(\'tr\').remove()"><i class="bi bi-x-circle"></i></a></td>';
    document.getElementById('lines-body').appendChild(row);
    if(!document.getElementById('catlist')){ var dl=document.createElement('datalist'); dl.id='catlist'; dl.innerHTML=list; document.body.appendChild(dl); }
  }
  // seed a few starter lines
  addLine('income','Student fees'); addLine('expense','Salaries'); addLine('expense','Utilities');
</script>
@endsection
