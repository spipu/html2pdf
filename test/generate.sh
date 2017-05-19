#!/usr/bin/env bash

cd "$( dirname "${BASH_SOURCE[0]}" )"

rm -f *.pdf

cd ../examples
for PHP_SCRIPT in $(ls ./*.php);
do
    PDF_FILE=`echo "$PHP_SCRIPT" | sed 's/\.php/\.pdf/g' | sed 's/\.\//\.\.\/test\//g'`
    echo "Example $PHP_SCRIPT => $PDF_FILE"
    php $PHP_SCRIPT > $PDF_FILE
done

cd ../test
ls -l
