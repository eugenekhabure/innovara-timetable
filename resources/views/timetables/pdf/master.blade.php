<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9px; }
    h2 { margin: 0 0 6px 0; font-size: 14px; }
    .muted { color:#666; font-size: 8px; }
    table { border-collapse: collapse; width: 100%; margin-bottom: 12px; }
    th, td { border: 1px solid #222; padding: 3px; vertical-align: top; }
    th { background: #f2f2f2; }
    .cell { line-height: 1.15; }
    .band td { background:#111; color:#fff; font-weight:700; text-transform: uppercase; letter-spacing: .5px; }
  </style>
</head>
<body>

@php
  $mode = request('mode', 'normal');
  $isCompact = ($mode === 'compact');

  $rowsPerBlock = (int) request('rows', $isCompact ? 30 : 20);
  if ($rowsPerBlock < 8) $rowsPerBlock = 8;

  $days = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
  $periods = $periods ?? collect($entries->pluck('period_index')->unique()->sort()->values()->all());
  $classes = $entries->pluck('class_id')->unique()->sort()->values()->all();

  $map = ['Mon'=>'Monday','Tue'=>'Tuesday','Wed'=>'Wednesday','Thu'=>'Thursday','Fri'=>'Friday'];

  $subjectCodes = [
    1=>'ENG',2=>'MAT',3=>'KIS',4=>'SCI',5=>'SST',6=>'CRE',7=>'AGR',8=>'HOM',9=>'ART',10=>'PE'
  ];

  $sectionOf = function ($classLabel) {
    $s = strtoupper(trim((string)$classLabel));
    if (preg_match('/^PP/', $s)) return 'PP';
    if (preg_match('/^(GRADE|G)\s*[1-3]\b/', $s)) return 'Lower Primary';
    if (preg_match('/^(GRADE|G)\s*[4-6]\b/', $s)) return 'Upper Primary';
    if (preg_match('/^(GRADE|G)\s*[7-9]\b/', $s)) return 'JSS';
    if (preg_match('/^(FORM|F)\s*[1-4]\b/', $s)) return 'Secondary';
    return 'Classes';
  };

  $grid = [];
  foreach ($classes as $c) foreach ($days as $d) foreach ($periods as $p) $grid[$c][$d][$p] = null;

  foreach ($entries as $e) {
    $raw = trim((string)$e->day);
    $dayName = $map[$raw] ?? ucfirst(strtolower($raw));
    if (!in_array($dayName, $days, true)) continue;
    $grid[$e->class_id][$dayName][$e->period_index] = $e;
  }

  $blocks = array_chunk($classes, $rowsPerBlock);
@endphp

<h2>
  Master Timetable — Run #{{ $run->id }} ({{ $run->name }})
  <span class="muted">({{ $isCompact ? 'Compact' : 'Normal' }})</span>
</h2>

@foreach($blocks as $bi => $blockClasses)

  <div class="muted" style="margin:4px 0 6px 0;">
    Block {{ $bi + 1 }} of {{ count($blocks) }} — rows/block={{ $rowsPerBlock }}
  </div>

  <table>
    <thead>
      <tr>
        <th style="width:70px;">Class</th>
        @foreach($days as $d)
          <th colspan="{{ count($periods) }}">{{ $d }}</th>
        @endforeach
      </tr>
      <tr>
        <th></th>
        @foreach($days as $d)
          @foreach($periods as $p)
            @php
              $meta = $periodMeta[$p] ?? null;
              $label = $meta['label'] ?? ('P'.$p);
              $type  = $meta['type'] ?? 'lesson';
            @endphp
            <th>{{ $label }}</th>
          @endforeach
        @endforeach
      </tr>
    </thead>

    <tbody>
      @php $prevSection = null; @endphp

      @foreach($blockClasses as $c)
        @php $sec = $sectionOf($c); @endphp

        @if($sec !== $prevSection)
          <tr class="band">
            <td colspan="{{ 1 + (count($days) * count($periods)) }}">{{ $sec }}</td>
          </tr>
          @php $prevSection = $sec; @endphp
        @endif

        <tr>
          <th>{{ $c }}</th>

          @foreach($days as $d)
            @foreach($periods as $p)
              @php
                $cell = $grid[$c][$d][$p] ?? null;
                $meta = $periodMeta[$p] ?? null;
                $type = $meta['type'] ?? 'lesson';
              @endphp

              <td>
                @if($type !== 'lesson')
                  {{ $meta['label'] ?? 'BREAK' }}
                @elseif($cell)
                  @php
                    $subCode = $subjectCodes[$cell->subject_id] ?? ('S'.$cell->subject_id);
                    $teaCode = 'T'.$cell->teacher_id;
                  @endphp

                  @if($isCompact)
                    <strong>{{ $subCode }}</strong> <span class="muted">{{ $teaCode }}</span>
                  @else
                    <strong>S{{ $cell->subject_id }}</strong><br>
                    <span class="muted">T{{ $cell->teacher_id }}</span>
                  @endif
                @else
                  —
                @endif
              </td>
            @endforeach
          @endforeach
        </tr>
      @endforeach
    </tbody>
  </table>

  @if($bi + 1 < count($blocks))
    <div style="page-break-after: always;"></div>
  @endif
@endforeach

</body>
</html>
