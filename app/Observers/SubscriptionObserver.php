<?php

namespace App\Observers;

use App\Models\Subscription;

class SubscriptionObserver
{
    public function created(Subscription $subscription): void
    {
        audit(
            'CREATE SUBSCRIPTION',
            $subscription,
            null,
            $this->safeData($subscription)
        );
    }

    public function updating(Subscription $subscription): void
    {
        if (!$subscription->isDirty()) {
            return;
        }

        $dirty = $subscription->getDirty();

        $old = [];

        foreach ($dirty as $field => $value) {
            $old[$field] = $subscription->getOriginal($field);
        }

        if (array_key_exists('status', $dirty)) {
            audit(
                'UPDATE SUBSCRIPTION STATUS',
                $subscription,
                [
                    'status' => $old['status'] ?? null,
                ],
                [
                    'status' => $dirty['status'],
                ]
            );

            return;
        }

        audit(
            'UPDATE SUBSCRIPTION',
            $subscription,
            $old,
            $dirty
        );
    }

    public function deleted(Subscription $subscription): void
    {
        audit(
            'DELETE SUBSCRIPTION',
            $subscription,
            $this->safeData($subscription),
            null
        );
    }

    private function safeData($data): array
    {
        if ($data instanceof Subscription) {
            $data = $data->toArray();
        }

        return collect($data)
            ->except([
                'deleted_at',
            ])
            ->toArray();
    }
}
