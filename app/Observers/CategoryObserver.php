<?php

namespace App\Observers;

use App\Models\Category;

class CategoryObserver
{
    public function created(Category $category): void
    {
        audit(
            'CREATE CATEGORY',
            $category,
            null,
            $this->safeData($category)
        );
    }

    public function updating(Category $category): void
    {
        if (!$category->isDirty()) {
            return;
        }

        $dirty = $category->getDirty();

        $old = collect($dirty)
            ->mapWithKeys(function ($value, $key) use ($category) {
                return [
                    $key => $category->getOriginal($key),
                ];
            })
            ->toArray();

        $new = $dirty;

        if (isset($new['name'])) {
            audit(
                'UPDATE CATEGORY NAME',
                $category,
                [
                    'name' => $old['name'] ?? null,
                ],
                [
                    'name' => $new['name'],
                ]
            );

            return;
        }

        if (isset($new['status'])) {
            audit(
                'UPDATE CATEGORY STATUS',
                $category,
                [
                    'status' => $old['status'] ?? null,
                ],
                [
                    'status' => $new['status'],
                ]
            );

            return;
        }

        audit(
            'UPDATE CATEGORY',
            $category,
            $this->safeData($old),
            $this->safeData($new)
        );
    }

    public function deleted(Category $category): void
    {
        audit(
            'DELETE CATEGORY',
            $category,
            $this->safeData($category),
            null
        );
    }

    private function safeData($data): array
    {
        if ($data instanceof Category) {
            $data = $data->toArray();
        }

        return collect($data)
            ->except([
                'deleted_at',
            ])
            ->toArray();
    }
}
