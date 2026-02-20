@extends('layouts.app')

@section('content')

<div style="padding:20px;">
    <h1>Teacher Timetable — Teacher #{{ $teacherId }} (Run #{{ $runId }})</h1>

    <div style="margin:15px 0;">
        <a href="{{ url('/timetables/runs') }}">← Back to Runs</a> |
        <a href="{{ url('/timetables/run/'.$runId.'/teacher/'.$teacherId.'/pdf') }}">
            Download Teacher PDF
        </a>
    </div>

    <table border="1" cellpadding="6" cellspacing="0" width="100%">
        <thead>
            <
