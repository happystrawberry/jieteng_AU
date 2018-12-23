#!/bin/bash
basedir=`cd $(dirname $0); pwd -P`
cd $basedir

pid=`ps -ef | grep 'php artisan queue:work redis --queue=pay' | grep -v grep | awk '{print $2}'`

if [ -z $pid ]; then
echo 'start queue daemon'
nohup php artisan queue:work redis --queue=pay --sleep=1 --tries=3 --daemon &>/dev/null &
else
echo 'queue daemon exist'
fi

