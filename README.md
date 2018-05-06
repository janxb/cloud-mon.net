## [cloud-mon.net](https://cloud-mon.net) - just another cloud monitoring
![cloud-mon.net Logo](https://raw.githubusercontent.com/LKDevelopment/cloud-mon.net/master/public/cloud_mon_little.png)

A little cloud monitoring service that monitors some cloud provider.
Actually we monitor the following cloud provider:
* [Hetzner Cloud](https://hetzner.cloud)
* [DigitalOcean](https://digitalocean.com)
* [Linode](https://linode.com)

Since we can not monitor all services of this providers we limit our self actually to the following checks:
* Response Time of the servers list endpoint
* Time between "create server api call" and the first successfully
* Network: Upload Speedtest
* Network: Download Speedtest

## Locations
Currently we perform the checks from three independent locations:
* [Germany](https://cloud-mon.net)
* [New York](https://do.cloud-mon.net)
* [Singapore](https://sing.cloud-mon.net)

## Daily tweets
We publish daily a summery of the results on Twitter. Follow [@CloudMonNet](https://twitter.com/CloudMonNet) for daily updates!
### Support us!
Would you like to support the development and operation of [cloud-mon.net](https://cloud-mon.net)? We are an independent cloud service monitoring and have only limited funds. All collected amounts are 100% invested in the development, operation and the [cloud-mon.net](https://cloud-mon.net) server created by the provider.