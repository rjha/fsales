#!/bin/bash

set -e

if [ "$UID" -eq "0" ]
then
    echo "Do not run this script as root."
    exit 100 ;
fi

#cleanup
rm --force fs-bundle* 

#generate
# fs-bundle.js
# fs-bundle.css

`dirname $0`/cat.php

#minify bundle files

java -jar yuicompressor-2.4.7.jar   fs-bundle.js -o fs-bundle.min.js  --charset utf-8 --disable-optimizations --preserve-semi --line-break 0
java -jar yuicompressor-2.4.7.jar   fs-bundle.css -o fs-bundle.min.css  --charset utf-8 --disable-optimizations --preserve-semi --line-break 0


#copy

cp ./fs-bundle.min.js ../web/js/.
cp ./fs-bundle.js ../web/js/.

cp ./fs-bundle.css ../web/css/.
cp ./fs-bundle.min.css ../web/css/.

#cleanup
rm fs-bundle*
