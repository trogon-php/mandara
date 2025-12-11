<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all existing client credentials
        $credentials = DB::table('client_credentials')->get();
        
        foreach ($credentials as $credential) {
            $updates = [];
            
            // Check and encrypt account_key if it's not already encrypted
            if (!empty($credential->account_key)) {
                try {
                    // Try to decrypt - if it fails, it's not encrypted
                    Crypt::decryptString($credential->account_key);
                } catch (\Exception $e) {
                    // If decryption fails, encrypt the value
                    $updates['account_key'] = Crypt::encryptString($credential->account_key);
                }
            }
            
            // Check and encrypt account_secret if it's not already encrypted
            if (!empty($credential->account_secret)) {
                try {
                    // Try to decrypt - if it fails, it's not encrypted
                    Crypt::decryptString($credential->account_secret);
                } catch (\Exception $e) {
                    // If decryption fails, encrypt the value
                    $updates['account_secret'] = Crypt::encryptString($credential->account_secret);
                }
            }
            
            // Check and encrypt remarks if it's not already encrypted
            if (!empty($credential->remarks)) {
                try {
                    // Try to decrypt - if it fails, it's not encrypted
                    Crypt::decryptString($credential->remarks);
                } catch (\Exception $e) {
                    // If decryption fails, encrypt the value
                    $updates['remarks'] = Crypt::encryptString($credential->remarks);
                }
            }
            
            // Update the record if there are changes
            if (!empty($updates)) {
                DB::table('client_credentials')
                    ->where('id', $credential->id)
                    ->update($updates);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as we can't safely decrypt
        // without knowing the original values
    }
};