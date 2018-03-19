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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">
    <!-- Styles -->

</head>
<body class="bg-grey-lighter">
<div class="container mx-auto">
    <span class="text-center w-auto">
        <div class="bg-red-lightest border border-red-light text-red-dark px-4 py-3 rounded relative" role="alert">
  <strong class="font-bold">Attention!</strong>
  <span class="block sm:inline">This monitoring is still work in progress and hasn't enough data to be valid!</span>
  <span class="absolute pin-t pin-b pin-r px-4 py-3">
    <svg class="fill-current h-6 w-6 text-red" role="button" xmlns="http://www.w3.org/2000/svg"
         viewBox="0 0 20 20"><title>Close</title><path
                d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
  </span>
</div>
    </span>
    <div class="w-auto h-100 text-center">
        <h3>Response Time of the Servers List API Endpoint</h3>
        <canvas id="hetzner_cloud_api_response_time"></canvas>
    </div>
    <div class="w-auto h-100 text-center">
        <h3>Time between API Call and first Ping (in seconds last 24 hours)</h3>
        <canvas id="hetzner_cloud_server_creation_time"></canvas>
    </div>
</div>
<script>
    var ctx = document.getElementById("hetzner_cloud_server_creation_time");
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels:{!! json_encode(\App\Models\Provider::find(1)->checks()->where('check','=','server_creation_time')->limit(10)->get()->map(function($check){
        return  $check->created_at->format('d.m.Y h\:00 a');
       })) !!},
            datasets:
            {!! json_encode(\App\Models\Provider::all()->map(function($provider){
            return [
            'label' => $provider->name,
            'fill' => false,
            'backgroundColor'=> $provider->color,
            'borderColor' => $provider->color,
            'data' => $provider->checks()->where('check','=','server_creation_time')->limit(24)->get()->map(function($check){
    return [
    'x' => $check->created_at->format('d.m.Y H:i:s'),
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
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Seconds'
                    }
                }],
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Time on the Clock'
                    }
                }],
            }
        }
    });
    var ctx = document.getElementById("hetzner_cloud_api_response_time");
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels:{!! json_encode(\App\Models\Provider::find(1)->checks()->where('check','=','server_upgrade_time')->limit(10)->get()->map(function($check){
        return  $check->created_at->format('d.m.Y h\:00 a');
       })) !!},
            datasets:
            {!! json_encode(\App\Models\Provider::all()->map(function($provider){
            return [
            'label' => $provider->name,
            'fill' => false,
            'backgroundColor'=> $provider->color,
            'borderColor' => $provider->color,
            'data' => $provider->checks()->where('check','=','api_response_time')->limit(24)->get()->map(function($check){
    return [
    'x' => $check->created_at->format('d.m.Y H:i:s'),
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
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Seconds'
                    }
                }],
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Time on the Clock'
                    }
                }],
            }
        }
    });
</script>
</body>
</html>
