#!/bin/bash 

for (( i = 78; i >= 0; i -= 1 ))
do
  app/console metrics:get $i --save 
  sleep 10
done
