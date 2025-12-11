<?php

namespace App\Http\Controllers\Admin\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Services\Core\FileUploadService;

trait AdminControllerHelpers
{
    // Common validation method
    protected function validateRequest(Request $request, array $rules)
    {
        return Validator::make($request->all(), $rules);
    }

    // Common file upload wrapper
    protected function uploadFile($file, $folder, $preset = null)
    {
        return FileUploadService::upload($file, $folder, $preset);
    }

    // delete file
    protected function deleteFile($file)
    {
        return FileUploadService::delete($file);
    }

    // Return success response as JSON
    protected function successResponse(string $message, $data = null, int $statusCode = 200)
    {
        $response = [
            'status' => 'success',
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    // Return error response as JSON
    protected function errorResponse(string $message, $errors = null, int $statusCode = 400)
    {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    // Redirect with success
    protected function redirectWithSuccess(string $route, string $message, array $parameters = [])
    {
        return redirect()->route($route, $parameters)->with('message_success', $message);
    }

    // Redirect with error
    protected function redirectWithError(string $route, string $message, array $parameters = [])
    {
        return redirect()->route($route, $parameters)->with('message_danger', $message);
    }

    // Redirect back with success
    protected function redirectBackWithSuccess(string $message)
    {
        return redirect()->back()->with('success', $message);
    }

    // Redirect back with error
    protected function redirectBackWithError(string $message)
    {
        return redirect()->back()->with('message_danger', $message);
    }

    
}
