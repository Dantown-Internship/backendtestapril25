<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ExpenseController;
use App\Http\Controllers\API\AuditLogController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Expense;
use App\Http\Controllers\API\EagerLoadingTestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    // User routes
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Apply company access middleware to all protected routes
    Route::middleware(['company.access'])->group(function () {
        // Routes for user management (Phase 4)
        Route::apiResource('users', UserController::class);
        
        // Routes for expense management (Phase 5)
        Route::apiResource('expenses', ExpenseController::class);
        
        // Routes for audit logs (Phase 8) - Admin only
        Route::get('/audit-logs', [AuditLogController::class, 'index']);
        Route::get('/audit-logs/{id}', [AuditLogController::class, 'show']);
    });

    // Test routes for eager loading (DEV only)
    Route::get('/test-eager-loading', function (Request $request) {
        // Enable query logging
        DB::enableQueryLog();
        
        // Test 1: Without eager loading
        Log::info('--- TEST WITHOUT EAGER LOADING ---');
        $expenses = Expense::where('company_id', $request->user()->company_id)->limit(5)->get();
        
        // Collect queries before accessing relations
        $queriesBeforeAccess = DB::getQueryLog();
        DB::flushQueryLog();
        DB::enableQueryLog();
        
        // Now access the relations to trigger lazy loading
        foreach ($expenses as $expense) {
            // This will trigger lazy loading (N+1 problem)
            $username = $expense->user->name;
            $companyName = $expense->company->name;
        }
        
        $queriesAfterAccess = DB::getQueryLog();
        $queriesWithoutEager = array_merge($queriesBeforeAccess, $queriesAfterAccess);
        
        // Format queries for logging and response
        $nonEagerQueries = [];
        foreach ($queriesWithoutEager as $index => $query) {
            $nonEagerQueries[] = [
                'sql' => $query['query'],
                'bindings' => $query['bindings'],
                'time' => $query['time']
            ];
            Log::info("Non-Eager Query #{$index}: " . $query['query']);
        }
        
        Log::info('Queries without eager loading: ' . count($queriesWithoutEager));
        DB::flushQueryLog();
        
        // Test 2: With eager loading
        Log::info('--- TEST WITH EAGER LOADING ---');
        DB::enableQueryLog();
        $expensesEager = Expense::with(['user', 'company'])->where('company_id', $request->user()->company_id)->limit(5)->get();
        
        // Collect queries before accessing relations
        $queriesBeforeAccess = DB::getQueryLog();
        DB::flushQueryLog();
        DB::enableQueryLog();
        
        // Now access the relations (should NOT trigger additional queries)
        foreach ($expensesEager as $expense) {
            $username = $expense->user->name;
            $companyName = $expense->company->name;
        }
        
        $queriesAfterAccess = DB::getQueryLog();
        $queriesWithEager = array_merge($queriesBeforeAccess, $queriesAfterAccess);
        
        // Format queries for logging and response
        $eagerQueries = [];
        foreach ($queriesWithEager as $index => $query) {
            $eagerQueries[] = [
                'sql' => $query['query'],
                'bindings' => $query['bindings'],
                'time' => $query['time']
            ];
            Log::info("Eager Query #{$index}: " . $query['query']);
        }
        
        Log::info('Queries with eager loading: ' . count($queriesWithEager));
        
        return response()->json([
            'without_eager_loading' => [
                'query_count' => count($queriesWithoutEager),
                'queries' => $nonEagerQueries
            ],
            'with_eager_loading' => [
                'query_count' => count($queriesWithEager),
                'queries' => $eagerQueries
            ],
            'optimization_impact' => count($queriesWithoutEager) - count($queriesWithEager),
            'description' => 'With eager loading, you only need 3 queries (expenses, users, companies) instead of N+2 queries (1 for expenses + N for users + N for companies)'
        ]);
    });
    
    // More detailed and comprehensive eager loading test
    Route::get('/eager-loading-test', [EagerLoadingTestController::class, 'testEagerLoading']);
});
