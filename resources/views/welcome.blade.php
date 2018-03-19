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
    <script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js"
            integrity="sha384-SlE991lGASHoBfWbelyBPLsUlwY1GwNDJo3jSJO04KZ33K2bwfV9YBauFfnzvynJ"
            crossorigin="anonymous"></script>
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
                <a href="#test_information"
                   class="block mt-4 lg:inline-block lg:mt-0 text-teal-lighter hover:text-white mr-4">
                    Test Information
                </a>
            </div>
        </div>
        <div>
            <a href="https://lukas-kaemmerling.de/legal" target="_blank"
               class="block mt-4 text-sm lg:inline-block lg:mt-0 text-teal-lighter hover:text-white mr-4">Impressum</a>
        </div>
    </nav>

    <div class="text-center w-auto">
        <div class="bg-orange-lightest border border-orange-light text-orange-dark px-4 py-3 rounded relative"
             role="alert">
            <strong class="font-bold">Warning!</strong>
            <span class="block sm:inline">This monitoring is still work in progress and hasn't enough data to be valid!</span>
        </div>
    </div>
    <div class="w-auto text-center bg-white p-3 mt-2">
        <h3>Welcome!</h3>
        <p class="pt-2">This is just a little monitoring for some cloud providers. We check every provider once a hour
            and display the results here. Since a valid monitoring can only be trusted when the source code is open,
            here is the source available on <a href="https://github.com/LKDevelopment/cloud-mon.net">Github.</a></p>
    </div>
    <div class="w-auto h-100 text-center p-2 mt-2">
        <h3 class="p-2">Response Time of the servers list endpoint</h3>
        <canvas id="api_response_time"></canvas>
    </div>
    <div class="w-auto h-100 text-center p-2 mt-2">
        <h3 class="p-2">Time between API Call and first Ping (in seconds last 24 hours)</h3>
        <canvas id="server_creation_time"></canvas>
    </div>
    <div class="w-auto text-center bg-white p-3 mt-2" id="test_information">
        <h3>Informations about the monitoring</h3>
        <p class="pt-2">The server that runs the monitoring stand at the datacenter (Falkenstein) from Hetzner and is an instance of a
            Hetzner Cloud CX11 Server.</p>
        <p class="pt-2">This test isn't associated with the tested Cloud Providers.</p>
        <h4>Instanced Definition</h4>
        <p class="pt-2">We start all servers with the default ubuntu 16.04 image from the provider. Since actually we
            doesn't test the performance of the servers we used the cheapest available servers and only test the
            performance of the api or there provision system.</p>
        <h5>Hetzner Cloud</h5>
        <p class="pt-2">On the Hetzner Cloud (Ceph or local Storage) we use the smallest available server, the cx11.</p>
        <h5>DigitalOcean</h5>
        <p class="pt-2">On DigitalOcean we use the smallest available server, the s-1vcpu-1gb.</p>
    </div>
    <div class="w-auto text-center p-3 mt-2 text-grey-darker">
        Crafted with <i class="fas fa-heart text-red"></i>, <a href="https://laravel.com" target="_blank"><i
                    class="fab fa-laravel"></i></a> & <a href="https://tailwindcss.com">
            <svg class="fill-current h-4 w-4 mr-2" width="54" height="54" viewBox="0 0 54 54"
                 xmlns="http://www.w3.org/2000/svg">
                <path d="M13.5 22.1c1.8-7.2 6.3-10.8 13.5-10.8 10.8 0 12.15 8.1 17.55 9.45 3.6.9 6.75-.45 9.45-4.05-1.8 7.2-6.3 10.8-13.5 10.8-10.8 0-12.15-8.1-17.55-9.45-3.6-.9-6.75.45-9.45 4.05zM0 38.3c1.8-7.2 6.3-10.8 13.5-10.8 10.8 0 12.15 8.1 17.55 9.45 3.6.9 6.75-.45 9.45-4.05-1.8 7.2-6.3 10.8-13.5 10.8-10.8 0-12.15-8.1-17.55-9.45-3.6-.9-6.75.45-9.45 4.05z"
                      fill="#52C0B5"/>
            </svg>
        </a> in {{ date('Y') }} by <a
                href="https://lukas-kaemmerling.de" target="_blank">Lukas Kämmerling</a>
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
            labels:{!! json_encode(\App\Models\Provider::find(1)->checks()->where('check','=','api_response_time')->limit(10)->get()->map(function($check){
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
