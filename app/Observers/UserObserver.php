<?php

namespace App\Observers;

use App\Models\User;


class UserObserver
{

    /**
     * Ketika user dibuat
     */
    public function created(User $user): void
    {
        audit(
            'CREATE USER',
            $user,
            null,
            $this->safeData($user)
        );
    }


    /**
     * Ketika user diupdate
     */
    public function updating(User $user): void
    {
        if (!$user->isDirty()) {
            return;
        }

        $old = $user->getOriginal();
        $new = $user->getDirty();


        /*
        |--------------------------------------------------------------------------
        | Perubahan Role
        |--------------------------------------------------------------------------
        */

        if (isset($new['role'])) {

            audit(
                'UPDATE USER ROLE',
                $user,
                [
                    'role' => $old['role'] ?? null,
                ],
                [
                    'role' => $new['role'],
                ]
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Perubahan Status
        |--------------------------------------------------------------------------
        */

        if (isset($new['status'])) {

            audit(
                'UPDATE USER STATUS',
                $user,
                [
                    'status' => $old['status'] ?? null,
                ],
                [
                    'status' => $new['status'],
                ]
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Perubahan Email
        |--------------------------------------------------------------------------
        */

        if (isset($new['email'])) {

            audit(
                'UPDATE USER EMAIL',
                $user,
                [
                    'email' => $old['email'] ?? null,
                ],
                [
                    'email' => $new['email'],
                ]
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Perubahan Data User Lainnya
        |--------------------------------------------------------------------------
        */

        audit(
            'UPDATE USER',
            $user,
            $this->safeData($old),
            $this->safeData($new)
        );
    }


    /**
     * Ketika user dihapus
     */
    public function deleted(User $user): void
    {
        audit(
            'DELETE USER',
            $user,
            $this->safeData($user),
            null
        );
    }


    /**
     * Data yang boleh masuk Audit Log
     */
    private function safeData($data): array
    {
        if ($data instanceof User) {
            $data = $data->toArray();
        }

        return collect($data)
            ->except([
                'password',
                'remember_token',
                'verification_code',
                'verification_code_expires_at',
            ])
            ->toArray();
    }

}
