<?php
use App\Models\Subject;
?>

@extends('student.navigation')

@section('content')
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Syllabus') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Academic') }}</a></li>
                        <li><a href="#">{{ get_phrase('Syllabus') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-8 offset-md-2">
        <div class="eSection-wrap pb-2">
            @if(count($syllabuses) > 0)
            <table id="basic-datatable" class="table eTable">
                <thead>
                    <tr>
                        <th>{{ get_phrase('Title') }}</th>
                        <th>{{ get_phrase('Subject') }}</th>
                        <th class="text-end">{{ get_phrase('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($syllabuses as $syllabus)
                        <tr>
                            <td>{{ $syllabus['title'] }}</td>
                            <td>
                                <?php $subjects = Subject::find($syllabus['subject_id']); ?>
                                {{ $subjects->name ?? '-' }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('student.syllabus.details', $syllabus['id']) }}" class="eBtn btn-primary"><i class="bi bi-list-columns-reverse"></i> {{ get_phrase('View details') }}</a>
                                <a href="{{ route('student.syllabus.pdf', $syllabus['id']) }}" class="eBtn btn-secondary"><i class="bi bi-file-earmark-pdf"></i> {{ get_phrase('Download PDF') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {!! $syllabuses->appends(request()->all())->links() !!}
            @else
            <div class="empty_box center">
                <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                <br>
                <span class="">{{ get_phrase('No data found') }}</span>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection