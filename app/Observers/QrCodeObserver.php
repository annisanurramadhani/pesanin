<?php

namespace App\Observers;

use App\Models\QrCode;

class QrCodeObserver
{
    public function created(QrCode $qrCode): void
    {
        audit(
            'CREATE QR CODE',
            $qrCode,
            null,
            $this->safeData($qrCode)
        );
    }

    public function updating(QrCode $qrCode): void
    {
        if (!$qrCode->isDirty()) {
            return;
        }

        $dirty = $qrCode->getDirty();

        $old = [];

        foreach ($dirty as $field => $value) {
            $old[$field] = $qrCode->getOriginal($field);
        }

        if (array_key_exists('status', $dirty)) {
            audit(
                'UPDATE QR CODE STATUS',
                $qrCode,
                [
                    'status' => $old['status'] ?? null,
                ],
                [
                    'status' => $dirty['status'],
                ]
            );

            return;
        }

        if (array_key_exists('type', $dirty)) {
            audit(
                'UPDATE QR CODE TYPE',
                $qrCode,
                [
                    'type' => $old['type'] ?? null,
                ],
                [
                    'type' => $dirty['type'],
                ]
            );

            return;
        }

        audit(
            'UPDATE QR CODE',
            $qrCode,
            $this->safeData($old),
            $this->safeData($dirty)
        );
    }

    public function deleted(QrCode $qrCode): void
    {
        audit(
            'DELETE QR CODE',
            $qrCode,
            $this->safeData($qrCode),
            null
        );
    }

    private function safeData($data): array
    {
        if ($data instanceof QrCode) {
            $data = $data->toArray();
        }

        return collect($data)
            ->except([
                'deleted_at',
            ])
            ->toArray();
    }
}
