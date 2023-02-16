#!/bin/bash

for file in ~/Downloads/IT490-Spring2023/frontend/*;
do
    sudo cp $file /var/www/sample
    echo "$file has been copied to /var/www/sample" 
    echo "-----------------------------------------------------------------------------------"
done
