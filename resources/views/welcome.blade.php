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
<body class="bg-grey-lighter font-sans leading-normal">
<nav class="flex items-center justify-between flex-wrap bg-blue p-6 mb-3">
    <div class="flex items-center flex-no-shrink text-white mr-6">
        <img src="{{ asset('cloud_mon.png',true) }}" class="w-10">
        <span class="font-semibold text-xl tracking-tight">cloud-mon.net</span>
    </div>
    <div class="block lg:hidden">
        <button class="flex items-center px-3 py-2 border rounded text-teal-lighter border-teal-light hover:text-white hover:border-white" onclick="toggleNav()">
            <svg class="fill-current h-3 w-3" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><title>
                    Menu</title>
                <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/>
            </svg>
        </button>
    </div>
    <div class="w-full block hidden flex-grow lg:flex lg:items-center lg:w-auto lg:block" id="nav">
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
            <a href="#api"
               class="block mt-4 lg:inline-block lg:mt-0 text-teal-lighter hover:text-white mr-4">
                API
            </a>
            <a href="https://twitter.com/CloudMonNet"
               class="block mt-4 lg:inline-block lg:mt-0 text-teal-lighter hover:text-white mr-4" target="_blank">
                Twitter
            </a>
        </div>
    </div>
</nav>
<div class="container mx-auto">
    @if(\App\Models\Check::count() < 1001)
        <div class="text-center w-auto my-4">
            <div class="bg-orange-lightest border border-orange-light text-orange-dark mx-4 px-4 py-3 rounded relative shadow"
                 role="alert">
                <strong class="font-bold">Warning!</strong>
                <span class="block sm:inline">This monitoring is still work in progress and hasn't enough data to be valid!</span>
            </div>
        </div>
    @endif
    <div class="w-auto text-center max-w-md mx-auto px-4 my-8">
        <h3 class="my-4">Welcome on cloud-mon.net!</h3>
        <p>
            This is just a little monitoring for some cloud providers. We check every provider once a hour
            and display the results here. Since a valid monitoring can only be trusted when the source code is open,
            the source available on <a class="text-blue hover:text-blue-dark"
                                       href="https://github.com/LKDevelopment/cloud-mon.net">Github.</a>
        
        </p>
        <p>Currently we have performed {{ \App\Models\Check::count() }} Checks in this location
            since {{ \App\Models\Check::withoutGlobalScopes()->first()->created_at->format('d.m.Y h\:00 a') }}</p>
        <p>Available Locations: <a href="https://cloud-mon.net">Germany</a>
            <a href="https://do.cloud-mon.net">New York</a>
            <a href="https://sing.cloud-mon.net">Singapore</a>
        </p>
        
        @if(in_array(env('APP_NAME'),['sing','ny']))
            <div class="bg-orange-lightest border border-orange-light text-orange-dark mx-4 px-4 py-3 rounded relative shadow"
                 role="alert">
                <strong class="font-bold">Warning!</strong>
                <span class="block sm:inline">Because of the costs of the monitoring, the checks from Singapore and New York are limited to Hetzner and Digital Ocean!</span>
            </div>
        @endif
    </div>
    
    <div class="w-auto h-100 text-center my-8 bg-white rounded-lg px-6 py-4 relative shadow">
        <h3 class="my-4 font-medium">Response time of the servers list endpoint</h3>
        <canvas id="api_response_time"></canvas>
    </div>
    <div class="w-auto h-100 text-center my-8 bg-white rounded-lg px-6 py-4 relative shadow">
        <h3 class="my-4 font-medium">Time between api call and first successfully ping (in seconds last 24 hours)</h3>
        <canvas id="server_creation_time"></canvas>
    </div>
    <div class="w-auto h-100 text-center my-8 bg-white rounded-lg px-6 py-4 relative shadow">
        <h3 class="my-4 font-medium">Network - Upload Speedtest (Mbit/s)</h3>
        <canvas id="speed_test_upload"></canvas>
    </div>
    <div class="w-auto h-100 text-center my-8 bg-white rounded-lg px-6 py-4 relative shadow">
        <h3 class="my-4 font-medium">Network - Download Speedtest (Mbit/s)</h3>
        <canvas id="speed_test_download"></canvas>
    </div>
    <div class="w-auto text-center my-8 text-sm max-w-md mx-auto" id="test_information">
        <h3 class="mt-8 mb-2 pt-8">Informations about the monitoring</h3>
        <p class="mb-2">
            @if(env('APP_NAME') == 'de')
                The server that runs the monitoring is located at the datacenter (Falkenstein) from Hetzner and
                is an
                instance of a
                Hetzner Cloud CX11 Server.
            @endif
            @if(env('APP_NAME') == 'sing')
                The server that runs the monitoring is located at the datacenter (Singapore) from DigitalOcean and
                is an
                instance of a the smallest server.
            @endif
            @if(env('APP_NAME') == 'ny')
                The server that runs the monitoring is located at the datacenter (New York) from DigitalOcean and
                is an
                instance of a the smallest server.
            @endif
        </p>
        <p class="mb-2">This test isn't associated with the tested Cloud Providers.</p>
        <h4 class="mt-4 mb-2">Instanced Definition</h4>
        <p class="mb-2">We start all servers with the default ubuntu 16.04 image from the provider. Actually we
            doesn't test the performance of the servers, so we used the cheapest available servers.</p>
        <p class="mb-2">We test only the performance of the api and the provision system.</p>
        <p class="mb-2">When there is no value for a time, this could be an error from the provider or the creation was
            running longer than an hour.</p>
        <div class="flex flex-wrap md:mt-4">
            <div class="md:w-1/2 md:px-4">
                <h4 class="mt-4 mb-2">Hetzner Cloud</h4>
                <p class="mb-2">On the Hetzner Cloud (Ceph or local Storage) we use the smallest available server, the
                    cx11.</p>
            </div>
            <div class="md:w-1/2 md:px-4">
                <h4 class="mt-4 mb-2">DigitalOcean</h4>
                <p class="mb-2">On DigitalOcean we use the smallest available server, the s-1vcpu-1gb.</p>
            </div>
            <div class="md:w-1/2 md:px-4">
                <h4 class="mt-4 mb-2">Linode</h4>
                <p class="mb-2">On Linode we use the smallest available server, the Linode 1024.</p>
            </div>
            <div class="md:w-1/2 md:px-4">
                <h4 class="mt-4 mb-2">Vultr</h4>
                <p class="mb-2">Vultr is no longer monitored by us, because the provisioning system is extremely
                    sluggish according to our previous monitoring.</p>
            </div>
        </div>
        <div class="text-center">
            <h3 class="mt-4 mb-2">Your provider isn't here?</h3>
            <p class="mb-2">Just write me a mail or open a issue on Github if you want a specific providere here.</p>
        </div>
    </div>
    <div class="w-auto text-center my-8 text-sm max-w-md mx-auto rounded-lg px-6 py-4 bg-grey-lightest shadow" id="api">
        <h3 class="my-2">API</h3>
        <p class="mb-2">If you need our data in any way, we have a little json api for you. Since it is just a really
            basic
            api, currently we have only the link to the api:</p>
        <a href="/api" class="text-blue hover:text-blue-dark" target="_blank">Go to the API.</a>
    </div>
    <div class="w-auto text-center p-3 mt-2 text-grey-dark mt-8 mb-4 text-xs">
        Crafted with <i class="fas fa-heart text-red"></i>, <a href="https://laravel.com" target="_blank"><i
                    class="fab fa-laravel"></i></a> & <a href="https://tailwindcss.com"><img src="tailwind.svg"
                                                                                             class="fill-current h-4 w-4 mr-2"></a>
        in {{ date('Y') }} by <a class="text-blue hover:text-blue-dark"
                                 href="https://lukas-kaemmerling.de" target="_blank">Lukas Kämmerling</a>
        <a href="https://lukas-kaemmerling.de/legal" target="_blank" class="text-blue hover:text-blue-dark">Impressum</a>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
    ['server_creation_time', 'api_response_time'].forEach(function (val) {
        $.getJSON('/api/_checks/' + val, function (response) {
            var ctx = document.getElementById(val);
            new Chart(ctx, {
                type: 'line',
                data: response,
                options: {
                    responsive: true,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: false
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
                                labelString: 'Time of day'
                            }
                        }],
                    }
                }
            });
        });
    });
    ['speed_test_upload', 'speed_test_download'].forEach(function (val) {
        $.getJSON('/api/_checks/' + val, function (response) {
            var ctx = document.getElementById(val);
            new Chart(ctx, {
                type: 'line',
                data: response,
                options: {
                    responsive: true,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: false
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Mbit/s'
                            }
                        }],
                        xAxes: [{
                            display: true,
                            ticks: {
                                beginAtZero: false
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Time of day'
                            }
                        }],
                    }
                }
            });
        });
    });

    function toggleNav() {
        if ($('#nav').hasClass('hidden')) {
            $('#nav').fadeIn('fast', function () {

            });
            $('#nav').removeClass('hidden');
        } else {
            $('#nav').fadeOut('fast', function () {

            });
            $('#nav').addClass('hidden');
        }
    }
</script>
</body>
</html>
