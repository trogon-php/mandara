<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\ClientCredentials\StoreClientCredentialRequest;
use App\Http\Requests\ClientCredentials\UpdateClientCredentialRequest;
use App\Services\Integrations\ClientCredentialService;
use Illuminate\Http\Request;

class ClientCredentialController extends AdminBaseController
{
    public function __construct(private ClientCredentialService $service) {}

    public function index(Request $request)
    {
        // Check and create primary credentials if they don't exist
        $this->ensurePrimaryCredentialsExist();

        $filters = array_filter($request->only(['provider', 'status', 'date_from', 'date_to']));
        $searchParams = ['search' => $request->get('search')];

        $list_items = $this->service->getFilteredData([
            'search' => $searchParams['search'], 
            'filters' => $filters
        ]);

        return view('admin.client_credentials.index', [
            'page_title' => 'Client Credentials',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    public function create()
    {
        return view('admin.client_credentials.create', [
            'page_title' => 'Add Client Credential',
            'providerOptions' => \App\Models\ClientCredential::getProviderOptions(),
        ]);
    }

    public function store(StoreClientCredentialRequest $request)
    {
        try {
            $this->service->store($request->validated());
            return $this->successResponse('Client credential created successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function show(string $id)
    {
        $credential = $this->service->find($id);
        if (!$credential) {
            return $this->errorResponse('Client credential not found');
        }

        return view('admin.client_credentials.show', [
            'page_title' => 'Client Credential Details',
            'credential' => $credential,
        ]);
    }

    public function edit(string $id)
    {
        $edit_data = $this->service->find($id);
        if (!$edit_data) {
            return $this->errorResponse('Client credential not found');
        }

        return view('admin.client_credentials.edit', [
            'page_title' => 'Edit Client Credential',
            'edit_data' => $edit_data,
            'providerOptions' => \App\Models\ClientCredential::getProviderOptions(),
        ]);
    }

    public function update(UpdateClientCredentialRequest $request, string $id)
    {
        try {
            // Check if this is a primary credential and prevent editing the key
            $credential = $this->service->find($id);
            if ($credential && $this->isPrimaryCredential($credential->credential_key)) {
                $data = $request->validated();
                // Remove credential_key from data to prevent editing
                unset($data['credential_key']);
                $this->service->update($id, $data);
                return $this->successResponse('Client credential updated successfully (credential key protected)');
            }

            $this->service->update($id, $request->validated());
            return $this->successResponse('Client credential updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        // Check if this is a primary credential that should not be deleted
        $credential = $this->service->find($id);
        if ($credential && $this->isPrimaryCredential($credential->credential_key)) {
            return $this->errorResponse('Primary credentials cannot be deleted');
        }

        if (!$this->service->delete($id)) {
            return $this->errorResponse('Failed to delete client credential');
        }
        return $this->successResponse('Client credential deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        // Check if any of the selected credentials are primary credentials
        $credentials = \App\Models\ClientCredential::whereIn('id', $request->ids)->get();
        $primaryCredentials = $credentials->filter(function($credential) {
            return $this->isPrimaryCredential($credential->credential_key);
        });

        if ($primaryCredentials->isNotEmpty()) {
            $primaryKeys = $primaryCredentials->pluck('credential_key')->implode(', ');
            return $this->errorResponse("Cannot delete primary credentials: {$primaryKeys}");
        }

        if (!$this->service->bulkDelete($request->ids)) {
            return $this->errorResponse('Failed to delete client credentials');
        }
        return $this->successResponse('Selected client credentials deleted successfully');
    }



    /**
     * Get credentials by provider
     */
    public function getByProvider(Request $request)
    {
        $provider = $request->get('provider');
        if (!$provider) {
            return $this->errorResponse('Provider is required');
        }

        try {
            $credentials = $this->service->getByProvider($provider);
            return response()->json([
                'success' => true,
                'data' => $credentials
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get credentials: ' . $e->getMessage());
        }
    }

    /**
     * Get credential by key for programmatic access
     */
    public function getByCredentialKey(Request $request)
    {
        $credentialKey = $request->get('credential_key');
        if (!$credentialKey) {
            return $this->errorResponse('Credential key is required');
        }

        try {
            $credential = $this->service->getByCredentialKey($credentialKey);
            if (!$credential) {
                return $this->errorResponse('Credential not found');
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $credential->id,
                    'provider' => $credential->provider,
                    'title' => $credential->title,
                    'credential_key' => $credential->credential_key,
                    'account_key' => $credential->decrypted_account_key,
                    'account_secret' => $credential->decrypted_account_secret,
                    'remarks' => $credential->decrypted_remarks,
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get credential: ' . $e->getMessage());
        }
    }

    /**
     * Check if a credential key is a primary credential that should be protected
     */
    private function isPrimaryCredential(string $credentialKey): bool
    {
        $protectedKeys = ['vimeo_primary', 'zoom_primary', '2factor_primary'];
        return in_array($credentialKey, $protectedKeys);
    }

    /**
     * Ensure all primary credentials exist, create them if they don't
     */
    private function ensurePrimaryCredentialsExist(): void
    {
        $primaryCredentials = [
            [
                'provider' => 'vimeo',
                'title' => 'Vimeo Primary',
                'credential_key' => 'vimeo_primary',
                'account_key' => null,
                'account_secret' => null,
                'remarks' => 'Primary Vimeo integration credentials'
            ],
            [
                'provider' => 'zoom',
                'title' => 'Zoom Primary',
                'credential_key' => 'zoom_primary',
                'account_key' => null,
                'account_secret' => null,
                'remarks' => 'Primary Zoom integration credentials'
            ],
            [
                'provider' => '2factor',
                'title' => '2Factor Primary',
                'credential_key' => '2factor_primary',
                'account_key' => null,
                'account_secret' => null,
                'remarks' => 'Primary 2Factor integration credentials'
            ]
        ];

        foreach ($primaryCredentials as $credentialData) {
            $existing = \App\Models\ClientCredential::where('credential_key', $credentialData['credential_key'])->first();
            
            if (!$existing) {
                \App\Models\ClientCredential::create($credentialData);
            }
        }
    }
}
