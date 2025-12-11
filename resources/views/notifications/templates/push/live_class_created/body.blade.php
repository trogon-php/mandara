@if($user_name)
Hi {{ $user_name }},
@endif

A new live class has been scheduled for your course!

📚 Course: {{ $course_title }}
🎓 Live Class: {{ $live_class_title }}

@if(isset($session_date_formatted) && isset($session_time_formatted))
📅 Date: {{ $session_date_formatted }}
⏰ Time: {{ $session_time_formatted }}
@elseif(isset($session_date) && isset($session_start_time))
📅 Date: {{ \Carbon\Carbon::parse($session_date)->format('F d, Y') }}
⏰ Time: {{ \Carbon\Carbon::parse($session_start_time)->format('h:i A') }}
@endif

@if(!empty($live_class_description))
{{ $live_class_description }}
@endif

Don't miss it!