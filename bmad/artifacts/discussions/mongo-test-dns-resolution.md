Title: Mongo-test DNS resolution when running phpunit from host
Date: 2026-03-11

Summary
-------
When running `vendor/bin/phpunit` from the host (Windows) PHP CLI, tests fail with:

  MongoDB\Driver\Exception\ConnectionTimeoutException: Failed to resolve 'mongo-test'

Root cause
----------
Docker service names (like `mongo-test`) resolve via Docker's internal DNS inside containers. Running phpunit from the host CLI does not use Docker DNS, so `mongo-test` cannot be resolved.

Workarounds (choose one)
------------------------
1) Run tests inside the app container (recommended for parity):

   docker-compose exec idae-legacy bash -lc "cd idae/web && vendor/bin/phpunit --testsuite=Integration --filter=JsonDataTest"

2) Use host-accessible DSN when running from host CLI:

   export MONGO_TEST_DSN='mongodb://127.0.0.1:27018'
   vendor/bin/phpunit --testsuite=Integration --filter=JsonDataTest

   On Windows PowerShell:

   $env:MONGO_TEST_DSN='mongodb://127.0.0.1:27018'; vendor\bin\phpunit --testsuite=Integration --filter=JsonDataTest

3) Use `host.docker.internal` if available and Docker is configured to expose ports:

   MONGO_TEST_DSN='mongodb://host.docker.internal:27018'

4) Fix networking / hosts (advanced): add an entry mapping `mongo-test` to the host IP — fragile and not recommended.

Recommendation
--------------
Run tests inside the `idae-legacy` container using `docker-compose exec` to ensure service name resolution and parity with CI.

Recorder: bmad/orchestrator
