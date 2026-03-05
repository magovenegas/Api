<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PostController as PostV1;
use App\Http\Controllers\Api\V2\PostController as PostV2;
use App\Http\Controllers\Api\LoginController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Ruta version 1
Route::apiResource('v1/posts', PostV1::class)
    ->only(['show','index','destroy'])
    ->middleware('auth:sanctum');

//Ruta Version 2
Route::apiResource('v2/posts',PostV2::class)
    ->only(['show','index'])
    ->middleware('auth:sanctum');

//Ruta para Login
Route::post('login',[LoginController::class,
    'login'
]);