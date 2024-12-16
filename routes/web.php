<?php

use App\Http\Controllers\ObjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    echo "<h1>API Test</h1>";
});

Route::post('object', [ObjectController::class, 'save']);
Route::get('object/get_all_records', [ObjectController::class, 'getAllRecords']);
Route::get('object/{key}', [ObjectController::class, 'getByKey']);