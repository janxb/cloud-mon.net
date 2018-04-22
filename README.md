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