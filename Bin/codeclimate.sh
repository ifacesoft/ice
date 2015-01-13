#!/usr/bin/env sh

CODECLIMATE_REPO_TOKEN=781407b639c8efcf74cdd6daadb7264131fb309562f73b4235dcccf4b250f6a3 ../_vendor/bin/test-reporter --coverage-report ./Var/vendor/phpunit/coverage.xml --stdout > ./Var/vendor/codeclimate/report.json &&
curl -X POST -d @./Var/vendor/codeclimate/report.json -H 'Content-Type: application/json' -H 'User-Agent: Code Climate (PHP Test Reporter v0.1.1)' https://codeclimate.com/test_reports

