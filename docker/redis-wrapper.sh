#!/bin/bash

sysctl vm.overcommit_memory=1
mkdir -p /persist/redis/data
chown redis -R /persist/redis/data

sudo -u redis /usr/bin/redis-server
