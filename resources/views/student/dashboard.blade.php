@extends('student.navigation')
@section('content')

       <!-- Mani section header and breadcrumb -->
       <div class="mainSection-title">
       <div class="row">
         <div class="col-12">
           <div
             class="d-flex justify-content-between align-items-center flex-wrap gr-15"
           >
             <div class="d-flex flex-column">
               <h4>{{ get_phrase('Dashboard') }}</h4>
               <ul class="d-flex align-items-center eBreadcrumb-2">
                 <li><a href="#">{{ get_phrase('Home') }}</a></li>
                 <li><a href="#">{{ get_phrase('Dashboard') }}</a></li>
               </ul>
             </div>
           </div>
         </div>
       </div>
       </div>

       <!-- Welcome -->
       <div class="row mb-2">
         <div class="col-12">
           <div class="eSection-dashboardItems">
             <div class="dashboard_ShortListItem">
               <h4 class="text-dark">{{ auth()->user()->name }}</h4>
               <p>{{ get_phrase('Welcome, to') }} {{ DB::table('schools')->where('id', auth()->user()->school_id)->value('title') }}</p>
             </div>
           </div>
         </div>
       </div>

       <!-- Planner: calendar + to-do -->
       <div class="row mb-2">
         <div class="col-lg-8 mb-3">
           <div class="eSection-wrap">
             <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap" style="gap:8px;">
               <h5 class="mb-0">{{ get_phrase('My week') }}</h5>
               <div style="font-size:12px; color:#6c7385; display:flex; gap:14px;">
                 <span><span style="display:inline-block;width:10px;height:10px;border-radius:3px;background:#00955f;"></span> {{ get_phrase('Classes') }}</span>
                 <span><span style="display:inline-block;width:10px;height:10px;border-radius:3px;background:#f04b24;"></span> {{ get_phrase('Due') }}</span>
               </div>
             </div>
             <div id="student-calendar"></div>
           </div>
         </div>
         <div class="col-lg-4 mb-3">
           <div class="eSection-wrap h-100">
             <h5 class="mb-3">{{ get_phrase('What you need to do') }}</h5>
             @forelse(($todo ?? collect()) as $a)
               @php $planSub = \App\Models\Subject::find($a->subject_id); @endphp
               <a href="{{ route('student.assignment.show', $a->id) }}"
                  class="d-flex justify-content-between align-items-start py-2"
                  style="border-bottom:1px dashed #eee; text-decoration:none; gap:8px;">
                 <div>
                   <div style="font-weight:600; color:#181c32;">{{ $a->title }}</div>
                   <small class="text-muted">{{ $planSub->name ?? get_phrase('Assignment') }}</small>
                 </div>
                 <span class="badge bg-danger" style="white-space:nowrap;">{{ $a->deadline ? date('d M', $a->deadline) : '—' }}</span>
               </a>
             @empty
               <div class="text-center text-muted py-4">
                 <i class="bi bi-check2-circle" style="font-size:28px;"></i>
                 <p class="mb-0 mt-2">{{ get_phrase('You are all caught up!') }}</p>
               </div>
             @endforelse
           </div>
         </div>
       </div>

       <script type="text/javascript">
         "use strict";
         document.addEventListener('DOMContentLoaded', function () {
           var el = document.getElementById('student-calendar');
           if (el && window.FullCalendar) {
             var routineEvents = {!! json_encode($routineEvents ?? []) !!};
             var routineShown = false;

             var calendar = new FullCalendar.Calendar(el, {
               initialView: 'timeGridWeek',
               initialDate: '{{ date('Y-m-d') }}',
               height: 600,
               nowIndicator: true,
               allDaySlot: true,
               allDayText: '{{ get_phrase('Due') }}',
               slotMinTime: '07:00:00',
               slotMaxTime: '15:00:00',
               weekends: true,
               navLinks: true, // click day number/name -> day view
               headerToolbar: { left: 'prev,next today', center: 'title', right: 'timeGridDay,timeGridWeek,dayGridMonth,listWeek' },
               buttonText: { today: '{{ get_phrase('Today') }}', day: '{{ get_phrase('Day') }}', week: '{{ get_phrase('Week') }}', month: '{{ get_phrase('Month') }}', list: '{{ get_phrase('List') }}' },
               dayMaxEvents: true,
               // click a day cell -> open that day's planner
               dateClick: function (info) {
                 calendar.changeView('timeGridDay', info.dateStr);
               },
               // show the recurring class routine only in day/week views; keep month uncluttered (deadlines only)
               datesSet: function (info) {
                 var isMonth = info.view.type === 'dayGridMonth';
                 if (!isMonth && !routineShown) {
                   routineEvents.forEach(function (ev) { calendar.addEvent(ev); });
                   routineShown = true;
                 } else if (isMonth && routineShown) {
                   calendar.getEvents().forEach(function (e) {
                     if (e.extendedProps && e.extendedProps.isRoutine) e.remove();
                   });
                   routineShown = false;
                 }
               },
               events: {!! json_encode($events ?? []) !!}
             });
             calendar.render();
           }
         });
       </script>

       <!-- Start Alerts -->
       <div class="row">
       <div class="col-12">
         <div class="eSection-dashboardItems">
           <div class="row flex-wrap">
             <!-- Dashboard Short Details (student-relevant) -->
             <div class="col-lg-6">
               <div class="dashboard_ShortListItems">
                 <div class="row">
                   <div class="col-md-6">
                     <div class="dashboard_ShortListItem">
                       <div class="dsHeader d-flex justify-content-between align-items-center">
                         <h5 class="title">{{ get_phrase('My courses') }}</h5>
                         <a href="{{ route('student.addons.courses') }}" class="ds_link"><i class="bi bi-arrow-right"></i></a>
                       </div>
                       <div class="dsBody d-flex justify-content-between align-items-center">
                         <div class="ds_item_details">
                           <h4 class="total_no">{{ $stats['courses'] ?? 0 }}</h4>
                           <p class="total_info">{{ get_phrase('Enrolled courses') }}</p>
                         </div>
                         <div class="ds_item_icon"><i class="bi bi-mortarboard" style="font-size:34px;color:#00955f;"></i></div>
                       </div>
                     </div>
                   </div>
                   <div class="col-md-6">
                     <div class="dashboard_ShortListItem">
                       <div class="dsHeader d-flex justify-content-between align-items-center">
                         <h5 class="title">{{ get_phrase('Assignments due') }}</h5>
                         <a href="{{ route('student.assignment_home', ['type'=>'active']) }}" class="ds_link"><i class="bi bi-arrow-right"></i></a>
                       </div>
                       <div class="dsBody d-flex justify-content-between align-items-center">
                         <div class="ds_item_details">
                           <h4 class="total_no">{{ $stats['pending'] ?? 0 }}</h4>
                           <p class="total_info">{{ get_phrase('Pending tasks') }}</p>
                         </div>
                         <div class="ds_item_icon"><i class="bi bi-journal-check" style="font-size:34px;color:#f04b24;"></i></div>
                       </div>
                     </div>
                   </div>
                   <div class="col-md-6">
                     <div class="dashboard_ShortListItem">
                       <div class="dsHeader d-flex justify-content-between align-items-center">
                         <h5 class="title">{{ get_phrase('My attendance') }}</h5>
                       </div>
                       <div class="dsBody d-flex justify-content-between align-items-center">
                         <div class="ds_item_details">
                           <h4 class="total_no">{{ isset($stats['att_pct']) && $stats['att_pct']!==null ? $stats['att_pct'].'%' : '—' }}</h4>
                           <p class="total_info">{{ get_phrase('Present') }} {{ $stats['att_present'] ?? 0 }}/{{ $stats['att_total'] ?? 0 }}</p>
                         </div>
                         <div class="ds_item_icon"><i class="bi bi-calendar2-check" style="font-size:34px;color:#2f6fb0;"></i></div>
                       </div>
                     </div>
                   </div>
                 </div>
               </div>
             </div>
             <!-- Imcome Report -->
   
             <!-- Upcoming Events -->
             <div class="col-md-6 ms-auto">
               <div class="dashboard_report dashboard_upcoming_events">
                 <div
                   class="ds_report_header d-flex justify-content-between align-items-start"
                 >
                   <div class="ds_report_left">
                     <h4 class="title">{{ get_phrase('Upcoming Events') }}</h4>
                   </div>
                   
                 </div>
                 <div class="ds_report_list pt-38">
                   <ul class="upcoming_events_items d-flex flex-column">
   
                       @php $upcoming_events = DB::table('frontend_events')->where('school_id', auth()->user()->school_id)->where('timestamp', '>', time())->where('status', 1)->take(3)->orderBy('id', 'DESC')->get(); @endphp
                       @foreach($upcoming_events as $upcoming_event)
                       <li>
                           <div
                           class="upcoming_events_item d-flex justify-content-between align-items-start"
                           >
                           <div class="events_info">
                               <a href="#" class="title">{{$upcoming_event->title}}</a>
                               <p class="date">{{ date('D, M d Y', $upcoming_event->timestamp) }}</p>
                           </div>
                           
                           </div>
                       </li>
                       @endforeach
                   </ul>
                   <div class="text-end">
                     <a href="{{route('student.events.list')}}" class="all_report_btn_2">{{ get_phrase('See all') }}</a>
                   </div>
                 </div>
               </div>
             </div>
           </div>
         </div>
       </div>
       </div>
   
   
@endsection
