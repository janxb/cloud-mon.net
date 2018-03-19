<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>cloud-mon.net - just another cloud monitoring</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">
    <!-- Styles -->

</head>
<body class="bg-grey-lighter">
<div class="container mx-auto">
    <nav class="flex items-center justify-between flex-wrap bg-blue p-6 mb-3">
        <div class="flex items-center flex-no-shrink text-white mr-6">
            <img src="{{ asset('cloud_mon.png',true) }}" class="w-10">
            <span class="font-semibold text-xl tracking-tight">cloud-mon.net</span>
        </div>
        <div class="block lg:hidden">
            <button class="flex items-center px-3 py-2 border rounded text-teal-lighter border-teal-light hover:text-white hover:border-white">
                <svg class="fill-current h-3 w-3" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><title>
                        Menu</title>
                    <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/>
                </svg>
            </button>
        </div>
        <div class="w-full block flex-grow lg:flex lg:items-center lg:w-auto">
            <div class="text-sm lg:flex-grow">
                <a href="#api_response_time"
                   class="block mt-4 lg:inline-block lg:mt-0 text-teal-lighter hover:text-white mr-4">
                    API Response Time
                </a>
                <a href="#server_creation_time"
                   class="block mt-4 lg:inline-block lg:mt-0 text-teal-lighter hover:text-white mr-4">
                    Server Creation
                </a>

            </div>
        </div>
        <div>
            <a href="https://lukas-kaemmerling.de/legal" target="_blank"
               class="block mt-4 text-sm lg:inline-block lg:mt-0 text-teal-lighter hover:text-white mr-4">Impressum</a>
        </div>
    </nav>

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
    <div class="w-auto text-center bg-white p-3 mt-2">
        <h3>Welcome!</h3>
        <p class="pt-2">This is just a little monitoring for some cloud providers. We check every provider once a hour
            and display the results here. Since a valid monitoring can only be trusted when the source code is open,
            here is the source available on <a href="https://github.com/LKDevelopment/cloud-mon.net">Github.</a></p>
    </div>
    <div class="w-auto h-100 text-center">
        <h3 class="p-2">Response Time of the servers list endpoint</h3>
        <canvas id="api_response_time"></canvas>
    </div>
    <div class="w-auto h-100 text-center">
        <h3 class="p-2">Time between API Call and first Ping (in seconds last 24 hours)</h3>
        <canvas id="server_creation_time"></canvas>
    </div>
</div>
<script>
    var ctx = document.getElementById("server_creation_time");
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
                    ticks: {
                        beginAtZero: false
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Time on the Clock'
                    }
                }],
            }
        }
    });
    var ctx = document.getElementById("api_response_time");
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
                    ticks: {
                        beginAtZero: false
                    },
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
