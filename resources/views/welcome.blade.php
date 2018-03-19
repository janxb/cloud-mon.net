<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Cloud Mon</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>
    <!-- Styles -->

</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="content" style="width:40%">
        <canvas id="hetzner_cloud_server_creation_time" width="100" height="100"></canvas>
    </div>
</div>
<script>
    var ctx = document.getElementById("hetzner_cloud_server_creation_time");
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels:{!! json_encode(\App\Models\Provider::find(1)->checks()->where('check','=','server_creation_time')->get()->map(function($check){
        return  $check->created_at->format('H:i:s');
       })) !!},
            datasets:
            {!! json_encode(\App\Models\Provider::all()->map(function($provider){
            return [
            'label' => $provider->name,
            'fill' => false,
            'backgroundColor'=>'#ff0000',
            'borderColor' => '#ff0000',
            'data' => $provider->checks()->where('check','=','server_creation_time')->get()->map(function($check){
    return [
    'x' => $check->created_at->format('H:i:s'),
    'y' => (float) $check->result
    ];})
            ];
            })) !!}
        },
        options: {
            responsive: true,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }],
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Time'
                    }
                }],
            }
        }
    });

</script>
</body>
</html>
