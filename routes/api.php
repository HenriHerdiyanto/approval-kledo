<?php

use App\Http\Controllers\API\ApprovalController;
use App\Http\Controllers\API\ApprovalStageController;
use App\Http\Controllers\Api\ApproverController;
use App\Http\Controllers\API\ExpenseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StatusController;

Route::prefix('v1')->group(function () {
    Route::apiResource('statuses', StatusController::class);
    Route::apiResource('approvers', ApproverController::class);
    Route::apiResource('approval-stages', ApprovalStageController::class);
    Route::apiResource('expenses', ExpenseController::class);
    Route::apiResource('approvals', ApprovalController::class);
});
