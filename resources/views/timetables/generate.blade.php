<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Generate Timetable</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 24px;">
    <h1>Generate new timetable</h1>

    <form method="POST" action="/timetables/generate">
        <?php echo csrf_field(); ?>
        <button type="submit" style="padding:10px 14px; cursor:pointer;">
            Generate Run
        </button>
    </form>

    <p style="margin-top:16px;">
        <a href="/timetables/runs">Back to runs</a>
    </p>
</body>
</html>
