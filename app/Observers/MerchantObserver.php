<?php

namespace App\Observers;

use App\Models\Merchant;

class MerchantObserver
{
    public function created(Merchant $merchant): void
    {
        audit(
            'CREATE MERCHANT',
            $merchant,
            null,
            $this->safeData($merchant)
        );
    }

    public function updating(Merchant $merchant): void
    {
        if (!$merchant->isDirty()) {
            return;
        }

        $dirty = $merchant->getDirty();

        $old = [];

        foreach ($dirty as $field => $value) {
            $old[$field] = $merchant->getOriginal($field);
        }

        if (array_key_exists('name', $dirty)) {
            audit(
                'UPDATE MERCHANT NAME',
                $merchant,
                [
                    'name' => $old['name'] ?? null,
                ],
                [
                    'name' => $dirty['name'],
                ]
            );

            return;
        }

        if (array_key_exists('status', $dirty)) {
            audit(
                'UPDATE MERCHANT STATUS',
                $merchant,
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
            'UPDATE MERCHANT',
            $merchant,
            $old,
            $dirty
        );
    }

    public function deleted(Merchant $merchant): void
    {
        audit(
            'DELETE MERCHANT',
            $merchant,
            $this->safeData($merchant),
            null
        );
    }

    private function safeData($data): array
    {
        if ($data instanceof Merchant) {
            $data = $data->toArray();
        }

        return collect($data)
            ->except([
                'deleted_at',
            ])
            ->toArray();
    }
}
