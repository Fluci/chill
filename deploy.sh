#!/bin/sh

# idempotent operation, a second run only repairs

# create all directories with full write rights and get dependencies

echo "Creating directories ..."

cd www || exit 1

DIRS="data logs tmp"

mkdir -p ${DIRS}
chmod go+rw ${DIRS}

cd versions/latest || exit 1

DIRS="tmp vendor tmp/twig_cache tmp/cache"

mkdir -p ${DIRS}
chmod go+rw ${DIRS}

echo "Composer available?"
composer -V

if [ $? != 0 ]
    then
        echo "composer not found." 1>&2
        exit 1
fi;

echo "Running composer ..."
composer update

exit $?
