<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\postController;

Route::post('/signUp',[AuthController::class,'signUp']);
Route::post('/login',[AuthController::class,'login']);

Route::post('/logOut',[AuthController::class,'logOut'])->middleware('auth:sanctum');
Route::apiResource('posts',postController::class)->middleware('auth:sanctum');
