<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HostingDatabase;
use Illuminate\Http\Request;
use Tqdev\PhpCrudApi\Api;
use Tqdev\PhpCrudApi\Config\Config;
use Tqdev\PhpCrudApi\RequestFactory;
use Tqdev\PhpCrudApi\ResponseUtils;

class DatabaseApiController extends Controller
{
    /**
     * Handle incoming API requests for a specific database using php-crud-api.
     */
    public function handle(Request $request, $hashid, $path = '')
    {
        // 1. Authenticate using API Key
        $apiKey = $request->bearerToken() ?? $request->header('x-api-key');
        
        if (!$apiKey) {
            return response()->json(['error' => 'API Key is missing. Use Bearer token or x-api-key header.'], 401);
        }

        $decoded = \Vinkla\Hashids\Facades\Hashids::decode($hashid);
        if (empty($decoded)) {
            return response()->json(['error' => 'Invalid database ID.'], 404);
        }

        $database = HostingDatabase::where('id', $decoded[0])
            ->where('api_key', $apiKey)
            ->first();

        if (!$database) {
            return response()->json(['error' => 'Unauthorized or invalid API Key for this database.'], 401);
        }

        // 2. Setup php-crud-api configuration
        $config = new Config([
            'driver' => 'mysql',
            'address' => $database->host,
            'port' => $database->port ?? '3306',
            'username' => $database->db_username,
            'password' => $database->db_password, // Decrypted via model accessor
            'database' => $database->db_name,
            'basePath' => '/api/v1/db/' . $hashid,
            'middlewares' => 'cors',
            'debug' => env('APP_DEBUG', false),
        ]);

        // 3. Create the API instance and handle the request
        try {
            $api = new Api($config);
            
            // Auto-inject /records/ to make it implicit
            $uri = $_SERVER['REQUEST_URI'];
            $base = '/api/v1/db/' . $hashid;
            
            $relativePath = substr($uri, strlen($base));
            if ($relativePath && !preg_match('#^/(records|openapi|status|columns|cache|login|register|me|password)#', $relativePath)) {
                $newUri = $base . '/records' . ($relativePath[0] === '/' ? '' : '/') . ltrim($relativePath, '/');
                $_SERVER['REQUEST_URI'] = $newUri;
            }

            $psrRequest = RequestFactory::fromGlobals();
            $psrResponse = $api->handle($psrRequest);
            
            // Output the PSR-7 response to the client
            ResponseUtils::output($psrResponse);
            exit;

        } catch (\Exception $e) {
            return response()->json(['error' => 'API Error: ' . $e->getMessage()], 500);
        }
    }
}
