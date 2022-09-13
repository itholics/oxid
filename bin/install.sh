#!/usr/bin/env bash

cd ../../../../../

#COMPOSER_MEMORY_LIMIT=-1 composer config repositories.itholics/oxid path "source/modules/ith_modules/oxid" # for development
COMPOSER_MEMORY_LIMIT=-1 composer require itholics/oxid -n --no-scripts --update-no-dev
vendor/bin/oe-console oe:module:install-configuration source/modules/ith_modules/oxid