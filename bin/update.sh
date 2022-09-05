#!/usr/bin/env bash

cd ../../../../../

if [ $1 ]
then
  COMPOSER_MEMORY_LIMIT=-1 composer update ith_modules/oxid -n --no-scripts --no-dev
fi
vendor/bin/oe-console oe:module:install-configuration source/modules/ith_modules/oxid
vendor/bin/oe-console oe:module:deactivate ith_oxid
vendor/bin/oe-console oe:module:activate ith_oxid