<?php

use App\Http\Controllers\AIEngineerController;
use App\Http\Controllers\StorageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (non-GraphQL)
|--------------------------------------------------------------------------
|
| The /graphql endpoint is automatically registered by the Lighthouse
| package (see config/lighthouse.php), so it does not need to be
| registered manually here.
|
| This file ONLY contains endpoints that are technically not suitable
| for representation as standard GraphQL responses:
|   - file downloads -> StreamedResponse (binary)
|   - AI message streaming -> Server-Sent Events (token-by-token streaming)
|
| All endpoints defined here use the auth:api (JWT) middleware,
| NOT the auth (session) middleware used in the previous version.
|
*/

Route::middleware('auth:api')->group(function () {
    Route::get('/storage/{id}/download', [StorageController::class, 'download'])
        ->name('storage.download');

    Route::post('/ai-engineer/conversations/{conversation}/send', [AIEngineerController::class, 'send'])
        ->name('ai-engineer.send');
});