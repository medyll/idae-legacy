<?php
declare(strict_types=1);

/**
 * Store agent preferences using the existing MongoDB schema and ID allocator.
 * @package AppCommon
 * @date 2026-11-09
 */
namespace AppCommon;

use Closure;

final class AgentPreferences
{
    /** @param Closure(): int $nextId */
    public function __construct(
        private MongoCollection $collection,
        private Closure $nextId,
    ) {
    }

    public function get(int $agentId, string $key, string $table): mixed
    {
        $preference = $this->collection->findOne($this->filter($agentId, $key, $table));
        return $preference['valeurAgent_pref'] ?? null;
    }

    public function set(int $agentId, mixed $values): void
    {
        foreach ($values as $key => $value) {
            $filter = ['idagent' => $agentId, 'codeAgent_pref' => $key];
            $fields = $filter + ['valeurAgent_pref' => $value];
            $existing = $this->collection->findOne($filter);
            if (empty($existing['idagent_pref'])) {
                $fields['idagent_pref'] = ($this->nextId)();
            }

            $this->collection->updateOne($filter, ['$set' => $fields], ['upsert' => true]);
        }
    }

    public function delete(int $agentId, string $key, string $table): bool
    {
        return $this->collection->remove($this->filter($agentId, $key, $table));
    }

    /** @return array{idagent: int, codeAgent_pref: string} */
    private function filter(int $agentId, string $key, string $table): array
    {
        $suffix = empty($table) ? '' : '_' . $table;
        return ['idagent' => $agentId, 'codeAgent_pref' => $key . $suffix];
    }
}
