#!/usr/bin/env bash

cd ../../../../../

vendor/bin/oe-console oe:module:deactivate ith_oxid
vendor/bin/oe-console oe:module:uninstall-configuration source/modules/ith_modules/oxid
COMPOSER_MEMORY_LIMIT=-1 composer remove itholics/oxid -n --no-scripts --update-no-dev