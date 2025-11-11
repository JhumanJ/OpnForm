<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Versionable\Version as BaseVersion;

class Version extends BaseVersion
{

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Compute a structured diff between this snapshot and another (or the current model).
     * Returns an associative array of changed attributes:
     * [
     *   'attribute' => ['old' => mixed, 'new' => mixed, 'type' => 'json'|'text']
     * ]
     */
    public function diff(?BaseVersion $againstVersion = null): array
    {
        $currentSnapshotModel = $this->getModel();
        $targetModel = $againstVersion
            ? $againstVersion->getModel()
            : optional($this->versionable()->withTrashed()->first())
            ?->currentVersion()
            ?->getModel();

        if (!$targetModel instanceof Model) {
            return [];
        }

        // Use casted arrays for comparison so JSON/array casts are handled properly
        $currentData = $currentSnapshotModel->attributesToArray();
        $targetData = $targetModel->attributesToArray();

        // Build ignore list: timestamps, soft deletes, and model-defined excluded fields
        $ignoreKeys = [];
        if (method_exists($currentSnapshotModel, 'getCreatedAtColumn') && $currentSnapshotModel->getCreatedAtColumn()) {
            $ignoreKeys[] = $currentSnapshotModel->getCreatedAtColumn();
        }
        if (method_exists($currentSnapshotModel, 'getUpdatedAtColumn') && $currentSnapshotModel->getUpdatedAtColumn()) {
            $ignoreKeys[] = $currentSnapshotModel->getUpdatedAtColumn();
        }
        if (method_exists($currentSnapshotModel, 'getDeletedAtColumn') && $currentSnapshotModel->getDeletedAtColumn()) {
            $ignoreKeys[] = $currentSnapshotModel->getDeletedAtColumn();
        }
        if (property_exists($currentSnapshotModel, 'dontVersionFields') && is_array($currentSnapshotModel->dontVersionFields)) {
            $ignoreKeys = array_merge($ignoreKeys, $currentSnapshotModel->dontVersionFields);
        }

        foreach ($ignoreKeys as $key) {
            unset($currentData[$key], $targetData[$key]);
        }

        $allKeys = array_unique(array_merge(array_keys($currentData), array_keys($targetData)));
        $diff = [];

        foreach ($allKeys as $key) {
            $old = $currentData[$key] ?? null;
            $new = $targetData[$key] ?? null;

            if ($this->valuesEqual($old, $new)) {
                continue;
            }

            $diff[$key] = [
                'old' => $old,
                'new' => $new,
                'type' => (is_array($old) || is_array($new)) ? 'json' : 'text',
            ];
        }

        ksort($diff);
        return $diff;
    }

    /**
     * Safely unserialize the stored model_data into an array for internal use.
     */
    private function getModelData(): array
    {
        $raw = is_resource($this->model_data)
            ? stream_get_contents($this->model_data, -1, 0)
            : $this->model_data;

        if ($raw === null || $raw === '') {
            return [];
        }

        $data = @unserialize($raw);
        return is_array($data) ? $data : [];
    }

    /**
     * Deep equality for scalar/array values.
     */
    private function valuesEqual($a, $b): bool
    {
        if (is_array($a) || is_array($b)) {
            return json_encode($this->normalizeArray($a)) === json_encode($this->normalizeArray($b));
        }
        return $a === $b;
    }

    private function normalizeArray($value)
    {
        if (!is_array($value)) {
            return $value;
        }
        ksort($value);
        foreach ($value as $k => $v) {
            $value[$k] = is_array($v) ? $this->normalizeArray($v) : $v;
        }
        return $value;
    }
}
