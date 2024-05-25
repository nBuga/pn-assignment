Plan-net Assignment project


This recipe was tested on Docker Desktop 4.30.0
==============================================================================

Description
-----------------
Brings up a Linux Containers with Docker, which configures
and installs everything from mysql to php (and php modules), apache and so on.
For more info see docker-compose.yml

Requirements
------------

1. Install [Docker](docker.io)

Setup instructions for Docker environment
-----------------

1. Clone project plan-net-assignment
(e.g. `` git clone git@github.com:nBuga/plan-net-assignment.git``)
2. cd docker 
3. Run ``docker-compose up --build -d``
4. Run ``docker exec -it plan-net /bin/bash``
5. Go to ``cd /var/www/plan-net/``
6. RUN ``composer install``
   1. If we have error on mysql, check if the MySQL container is still up. Otherwise, exit from the plan-net container and RUN ``docker-compose up -d`` again to up the MySQL container
7. RUN ``bin/console doctrine:migrations:migrate``
7. RUN ``bin/console app:import-partners en``
7. RUN ``bin/console app:import-partners de``
7. RUN ``bin/console app:import-prizes en``
7. RUN ``bin/console app:import-prizes de``
8. RUN ``bin/console doctrine:fixtures:load``
- You can configure domain names by editing ``/etc/hosts`` on the host and putting the IP and domain names desired, such as:
##
# Host Database
# localhost is used to configure the loopback interface
# when the system is booting.  Do not change this entry.
##
127.0.0.1       localhost
255.255.255.0   broadcasthost
::1             localhost

10.254.254.0 local.plan-net.ro plan-net.mysql

RUN ``sudo ifconfig lo0 alias 10.254.254.0``

For the API Documentation you can see it here:
http://local.plan-net.ro/api/doc

![img.png](plan-net/public/api_documentation.png)