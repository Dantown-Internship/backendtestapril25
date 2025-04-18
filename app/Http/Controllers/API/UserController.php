<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @group User Management
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);
        
        // Enable query logging to check eager loading
        \DB::enableQueryLog();
        
        // Create a unique cache key based on user company and request parameters
        $cacheKey = 'users_' . $request->user()->company_id . '_' . http_build_query($request->all());
        
        // Check if we have a cache hit
        $cacheExists = Cache::has($cacheKey);
        if ($cacheExists) {
            Log::info('Cache HIT: ' . $cacheKey);
        } else {
            Log::info('Cache MISS: ' . $cacheKey);
        }
        
        // Cache the results for 15 minutes
        $result = Cache::remember($cacheKey, now()->addMinutes(15), function () use ($request) {
            return User::where('company_id', $request->user()->company_id)
                ->with('company') // Eager load company relationship
                ->paginate(15);
        });
        
        // Log the executed queries for debugging
        $queries = \DB::getQueryLog();
        Log::info('User Eager Loading Check - Query Count: ' . count($queries));
        foreach ($queries as $index => $query) {
            Log::info("Query #{$index}: " . $query['query']);
        }
        
        return $result;
    }

    /**
     * Store a newly created user in storage.
     *
     * @group User Management
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', Password::defaults()],
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $request->user()->company_id, // Set company_id from authenticated admin
            'role' => $request->role,
        ]);

        // Clear user list cache for this company
        $this->clearUserCache($request->user()->company_id);

        return response()->json($user, 201);
    }

    /**
     * Display the specified user.
     *
     * @group User Management
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Cache individual user views
        $cacheKey = 'user_' . $id;
        
        // Check if we have a cache hit
        $cacheExists = Cache::has($cacheKey);
        if ($cacheExists) {
            Log::info('Cache HIT: ' . $cacheKey);
        } else {
            Log::info('Cache MISS: ' . $cacheKey);
        }
        
        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($id) {
            $user = User::findOrFail($id);
            
            // First check if user is from same company - if not, return 404
            if (auth()->user()->company_id !== $user->company_id) {
                abort(404, 'User not found');
            }
            
            // Then check other permissions
            $this->authorize('view', $user);
            
            return response()->json($user->load('company'));
        });
    }

    /**
     * Update the specified user in storage.
     *
     * @group User Management
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $this->authorize('update', $user);
        
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'sometimes|required|in:Admin,Manager,Employee',
        ]);
        
        // Non-admin users can't change role
        if ($request->user()->role !== 'Admin' && $request->has('role')) {
            return response()->json(['message' => 'Unauthorized to change role'], 403);
        }

        $user->update($request->only(['name', 'email', 'role']));
        
        // Clear both the list cache and the individual user cache
        $this->clearUserCache($user->company_id);
        Cache::forget('user_' . $id);
        Log::info('Cache INVALIDATION: user_' . $id);
        
        return response()->json($user);
    }

    /**
     * Remove the specified user from storage.
     *
     * @group User Management
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        $this->authorize('delete', $user);
        
        // Store company_id before deleting for cache clearing
        $companyId = $user->company_id;
        
        $user->delete();
        
        // Clear the cache for this company's users
        $this->clearUserCache($companyId);
        Cache::forget('user_' . $id);
        Log::info('Cache INVALIDATION: user_' . $id);
        
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
    
    /**
     * Helper method to clear user caches for a company
     *
     * @param  int  $companyId
     * @return void
     */
    private function clearUserCache($companyId)
    {
        // Use pattern-based cache clearing for all keys starting with 'users_{company_id}_'
        Cache::forget('users_' . $companyId . '_*');
        Log::info('Cache INVALIDATION: users_' . $companyId . '_*');
    }
} 