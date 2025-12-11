<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Crypt;

class ClientCredential extends BaseModel
{
    protected $table = 'client_credentials';

    protected $casts = [
        'provider' => 'string',
        'title' => 'string',
        'credential_key' => 'string',
    ];

    protected $fillable = [
        'provider',
        'title',
        'credential_key',
        'account_key',
        'account_secret',
        'remarks',
    ];

    /**
     * Get the provider options for dropdown
     */
    public static function getProviderOptions(): array
    {
        return [
            'vimeo' => 'Vimeo',
            'zoom' => 'Zoom',
            '2factor' => '2Factor',
        ];
    }

    /**
     * Scope to filter by provider
     */
    public function scopeByProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Scope to filter by credential key
     */
    public function scopeByCredentialKey($query, string $credentialKey)
    {
        return $query->where('credential_key', $credentialKey);
    }

    /**
     * Get credentials by key for programmatic access
     */
    public static function getByCredentialKey(string $credentialKey): ?self
    {
        return static::byCredentialKey($credentialKey)->first();
    }

    /**
     * Get decrypted account key
     */
    public function getDecryptedAccountKeyAttribute(): ?string
    {
        if (empty($this->account_key)) {
            return null;
        }
        
        try {
            return Crypt::decryptString($this->account_key);
        } catch (\Exception $e) {
            \Log::error('Failed to decrypt account_key for ClientCredential ID: ' . $this->id, [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get decrypted account secret
     */
    public function getDecryptedAccountSecretAttribute(): ?string
    {
        if (empty($this->account_secret)) {
            return null;
        }
        
        try {
            return Crypt::decryptString($this->account_secret);
        } catch (\Exception $e) {
            \Log::error('Failed to decrypt account_secret for ClientCredential ID: ' . $this->id, [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get decrypted remarks
     */
    public function getDecryptedRemarksAttribute(): ?string
    {
        if (empty($this->remarks)) {
            return null;
        }
        
        try {
            return Crypt::decryptString($this->remarks);
        } catch (\Exception $e) {
            \Log::error('Failed to decrypt remarks for ClientCredential ID: ' . $this->id, [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Set encrypted account key
     */
    public function setAccountKeyAttribute($value): void
    {
        $this->attributes['account_key'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Set encrypted account secret
     */
    public function setAccountSecretAttribute($value): void
    {
        $this->attributes['account_secret'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Set encrypted remarks
     */
    public function setRemarksAttribute($value): void
    {
        $this->attributes['remarks'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Get the display name for the provider
     */
    public function getProviderDisplayAttribute(): string
    {
        $options = static::getProviderOptions();
        return $options[$this->provider] ?? $this->provider;
    }

    /**
     * Check if credentials are complete
     */
    public function isComplete(): bool
    {
        return !empty($this->account_key) && !empty($this->account_secret);
    }

    /**
     * Get masked account key for display
     */
    public function getMaskedAccountKeyAttribute(): string
    {
        if (empty($this->account_key)) {
            return 'Not set';
        }

        $decrypted = $this->getDecryptedAccountKeyAttribute();
        if (empty($decrypted)) {
            return 'Encryption Error';
        }

        if (strlen($decrypted) <= 8) {
            return str_repeat('*', strlen($decrypted));
        }

        return substr($decrypted, 0, 4) . str_repeat('*', strlen($decrypted) - 8) . substr($decrypted, -4);
    }

    /**
     * Get masked account secret for display
     */
    public function getMaskedAccountSecretAttribute(): string
    {
        if (empty($this->account_secret)) {
            return 'Not set';
        }

        $decrypted = $this->getDecryptedAccountSecretAttribute();
        if (empty($decrypted)) {
            return 'Encryption Error';
        }

        if (strlen($decrypted) <= 8) {
            return str_repeat('*', strlen($decrypted));
        }

        return substr($decrypted, 0, 4) . str_repeat('*', strlen($decrypted) - 8) . substr($decrypted, -4);
    }
}

