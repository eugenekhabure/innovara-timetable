<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Master Timetable</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10.5px; }
    .title { font-size: 16px; font-weight: bold; margin: 0; }
    .sub { margin: 4px 0 10px; color:#555; }
    table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    th, td { border: 1px solid #111; padding: 4px; vertical-align: top; }
    th { background: #f0f0f0; }
    .pcol { width: 60px; font-weight: bold; }
    .cellbox { border: 1px solid #ddd; padding: 3px; margin-bottom: 4px; }
    .class { font-weight: bold; font-size: 10.5px; }
    .muted { color:#555; font-size: 9.5px; }
  </style>
</head>
<body>

  <p class="title">MASTER TIMETABLE</p>
  <p class="sub">Run #{{ $run->id ?? '-' }} | Generated: {{ $run->created_at ?? '' }}</p>

  @php
    $days = $days ?? [1=>'Mon',2=>'Tue',3=>'Wed',4=>'Thu',5=>'Fri'];
    $periods = $periods ?? [
      ['index'=>1,'label'=>'P1'],['index'=>2,'label'=>'P2'],['index'=>3,'label'=>'P3'],['index'=>4,'label'=>'P4'],
      ['index'=>5,'label'=>'P5'],['index'=>6,'label'=>'P6'],['index'=>7,'label'=>'P7'],['index'=>8,'label'=>'P8'],
    ];
    $entries = $entries ?? collect();
    $grid = [];
    foreach ($entries as $e) {
      $d = data_get($e,'day'); $p = data_get($e,'period');
      if ($d === null || $p === null) continue;
      $grid[$d][$p][] = $e;
    }
  @endphp

  <table>
    <thead>
      <tr>
        <th class="pcol">Period</th>
        @foreach($days as $d => $dn)
          <th>{{ $dn }}</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      @foreach($periods as $per)
        @php $pIndex = $per['index']; @endphp
        <tr>
          <td class="pcol">{{ $per['label'] }}</td>
          @foreach($days as $d => $dn)
            <td>
              @php $items = $grid[$d][$pIndex] ?? []; @endphp
              @if(empty($items))
                <span class="muted">—</span>
              @else
                @foreach($items as $it)
                  @php
                    $class = data_get($it,'class_name') ?? ('Class #'.data_get($it,'class_id'));
                    $subj  = data_get($it,'subject_name') ?? 'Subject';
                    $tch   = data_get($it,'teacher_name') ?? 'Teacher';
                    $room  = data_get($it,'room_name') ?? '';
                  @endphp
                  <div class="cellbox">
                    <div class="class">{{ $class }}</div>
                    <div>{{ $subj }}</div>
                    <div class="muted">{{ $tch }}@if($room) · {{ $room }}@endif</div>
                  </div>
                @endforeach
              @endif
            </td>
          @endforeach
        </tr>
      @endforeach
    </tbody>
  </table>

</body>
</html>
