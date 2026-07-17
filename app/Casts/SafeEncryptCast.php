<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class SafeEncryptCast implements CastsAttributes
{
    /**
     * Cast the given value.
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value) || $value === '') {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException $e) {
            // Decryption failed (e.g. legacy plain text data). Return the value as-is.
            return $value;
        }
    }

    /**
     * Prepare the given value for storage.
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value) || $value === '') {
            return $value;
        }

        try {
            // Check if it's already encrypted to avoid double encryption
            Crypt::decryptString($value);
            return $value;
        } catch (DecryptException $e) {
            // Not encrypted yet, encrypt it
            return Crypt::encryptString($value);
        }
    }
}
