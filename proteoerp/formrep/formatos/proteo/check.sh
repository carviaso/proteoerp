#!/bin/bash

for arch in `ls *.for`
 do
 php -l $arch
done
