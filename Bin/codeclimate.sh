#!/usr/bin/env sh

cd .. &&
php ../_vendor/bin/test-reporter --stdout > ./Var/vendor/codeclimate/report.json &&
curl -X POST -d @./Var/vendor/codeclimate/report.json -H 'Content-Type: application/json' -H 'User-Agent: Code Climate (PHP Test Reporter v0.1.1)' https://codeclimate.com/test_reports

