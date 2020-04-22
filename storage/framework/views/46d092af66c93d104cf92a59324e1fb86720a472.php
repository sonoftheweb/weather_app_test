<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Weather Dashboard</title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet" type="text/css">

<body>

    <div id="app">
        <App></App>
    </div>

    <script type="text/javascript" src="<?php echo e(asset('js/app.js')); ?>"></script>
    

</body>

</html>
<?php /**PATH /var/www/html/ad94270a-992e-4e73-a76d-29a1918967f2/resources/views/weather_dashboard.blade.php ENDPATH**/ ?>