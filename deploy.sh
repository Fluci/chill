#!/bin/bash

# idempotent operation, a second run only repairs

# create all directories with full write rights and get dependencies

echo "Creating directories ..."
cd www;
mkdir -p data logs tmp;
chmod go+rw data logs tmp;

cd versions/latest;

mkdir -p cache tmp twig_cache vendor;
chmod go+rw cache tmp twig_cache vendor;

echo "Composer available?";
composer -V;
if [ $? ];
    then
        echo "Running composer ...";
        composer update;
else
    echo "Composer not found.";
fi;
