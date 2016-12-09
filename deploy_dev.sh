#!/bin/sh

# idempotent operation, a second run only repairs

# create all directories with full write rights and get dependencies for dev-environment

echo "Run instance deployment ..."

./deploy.sh

echo "Setting up dev-environment ..."

echo "Creating directories ..."

mkdir -p tmp/pdepend tmp/phpcpd
chmod go+rw tmp/pdepend tmp/phpcpd

echo "Composer available?"
composer -V

if [ ! $? ]
    then
        echo "composer not found." 1>&2
        exit 1
fi;

echo "Running composer ..."
composer update

exit $?
