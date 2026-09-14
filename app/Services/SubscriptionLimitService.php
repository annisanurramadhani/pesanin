<?php

namespace App\Services;

use App\Models\Merchant;
use RuntimeException;

class SubscriptionLimitService
{
    /**
     * Ambil paket dari subscription aktif merchant.
     */
    public function getPackage(Merchant $merchant)
    {
        $subscription = $merchant->activeSubscription()
            ->with('packageDuration.package')
            ->first();

        if (!$subscription || !$subscription->isActive()) {
            throw new RuntimeException(
                'Merchant belum memiliki subscription aktif.'
            );
        }

        if (!$subscription->packageDuration) {
            throw new RuntimeException(
                'Durasi subscription tidak ditemukan.'
            );
        }

        if (!$subscription->packageDuration->package) {
            throw new RuntimeException(
                'Paket subscription tidak ditemukan.'
            );
        }

        return $subscription->packageDuration->package;
    }

    /**
     * Ambil limit QR Code.
     */
    public function getQrLimit(Merchant $merchant): int
    {
        return (int) $this->getPackage($merchant)->max_qr_codes;
    }

    /**
     * Ambil limit Menu.
     */
    public function getMenuLimit(Merchant $merchant): int
    {
        return (int) $this->getPackage($merchant)->max_menus;
    }

    /**
     * Ambil limit Staff.
     */
    public function getStaffLimit(Merchant $merchant): int
    {
        return (int) $this->getPackage($merchant)->max_staff;
    }

    /**
     * Cek apakah resource masih boleh dibuat.
     */
    public function canCreate(
        Merchant $merchant,
        int $currentUsage,
        int $limit
    ): bool {
        return $currentUsage < $limit;
    }

    /**
     * Pastikan Staff masih boleh ditambahkan.
     */
    public function ensureCanCreateStaff(
        Merchant $merchant,
        int $currentUsage
    ): void {
        $limit = $this->getStaffLimit($merchant);

        if ($currentUsage >= $limit) {
            throw new RuntimeException(
                "Batas karyawan pada paket Anda sudah tercapai. " .
                "Maksimal {$limit} karyawan."
            );
        }
    }

    /**
     * Pastikan QR Code masih boleh ditambahkan.
     */
    public function ensureCanCreateQr(
        Merchant $merchant,
        int $currentUsage
    ): void {
        $limit = $this->getQrLimit($merchant);

        if ($currentUsage >= $limit) {
            throw new RuntimeException(
                "Batas QR Code pada paket Anda sudah tercapai. " .
                "Maksimal {$limit} QR Code."
            );
        }
    }

    /**
     * Pastikan Menu masih boleh ditambahkan.
     */
    public function ensureCanCreateMenu(
        Merchant $merchant,
        int $currentUsage
    ): void {
        $limit = $this->getMenuLimit($merchant);

        if ($currentUsage >= $limit) {
            throw new RuntimeException(
                "Batas menu pada paket Anda sudah tercapai. " .
                "Maksimal {$limit} menu."
            );
        }
    }
}
