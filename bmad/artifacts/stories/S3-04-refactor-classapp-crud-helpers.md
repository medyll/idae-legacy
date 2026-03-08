# S3-04 — Refactor ClassApp CRUD helpers: insert/update/remove

Estimate: 3 points
Status: ready

Description:
Refactor ClassApp helper methods responsible for insert, update, and remove operations to use the modern MongoDB driver semantics and AppCommon\MongoCompat helpers. Replace legacy Mongo v1 constructs (MongoId, MongoRegex, MongoDate) with MongoCompat::toObjectId, ::toRegex, ::toDate where appropriate. Add strict types, PHPDoc, and robust error handling.

Acceptance criteria:
- insert/update/remove helpers use MongoCompat conversions for IDs, regexes, and dates.
- Unit tests added under idae/web/tests/Unit covering happy path and error cases.
- No direct usage of legacy Mongo classes in modified files.
- Code follows repository style (declare(strict_types=1); 4-space indent; English PHPDoc).
- Tests pass locally (vendor/bin/phpunit --filter S3_04)
