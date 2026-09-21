<?php

namespace App\Observers;

use App\Models\MerchantBankAccount;

class MerchantBankAccountObserver
{
    public function created(MerchantBankAccount $account): void
    {
        audit(
            'CREATE BANK ACCOUNT',
            $account,
            null,
            $this->safeData($account)
        );
    }

    public function updating(MerchantBankAccount $account): void
    {
        if (!$account->isDirty()) {
            return;
        }

        $dirty = $account->getDirty();

        $old = [];

        foreach ($dirty as $field => $value) {
            $old[$field] = $account->getOriginal($field);
        }

        if (array_key_exists('status', $dirty)) {
            audit(
                'UPDATE BANK ACCOUNT STATUS',
                $account,
                [
                    'status' => $old['status'] ?? null,
                ],
                [
                    'status' => $dirty['status'],
                ]
            );

            return;
        }

        if (array_key_exists('is_locked', $dirty)) {
            audit(
                $dirty['is_locked']
                    ? 'LOCK BANK ACCOUNT'
                    : 'UNLOCK BANK ACCOUNT',
                $account,
                [
                    'is_locked' => $old['is_locked'] ?? null,
                ],
                [
                    'is_locked' => $dirty['is_locked'],
                ]
            );

            return;
        }

        audit(
            'UPDATE BANK ACCOUNT',
            $account,
            $this->safeData($old),
            $this->safeData($dirty)
        );
    }

    public function deleted(MerchantBankAccount $account): void
    {
        audit(
            'DELETE BANK ACCOUNT',
            $account,
            $this->safeData($account),
            null
        );
    }

    private function safeData($data): array
    {
        if ($data instanceof MerchantBankAccount) {
            $data = $data->toArray();
        }

        return collect($data)
            ->except([
                'account_number',
            ])
            ->toArray();
    }
}
