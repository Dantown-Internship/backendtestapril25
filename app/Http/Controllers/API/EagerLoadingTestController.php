<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EagerLoadingTestController extends Controller
{
    /**
     * Run tests to demonstrate eager loading benefits
     *
     * @group Performance Testing
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function testEagerLoading(Request $request)
    {
        // Test 1: Without eager loading (Expense -> User relations)
        $results = $this->testExpenseUserRelation($request);
        
        // Test 2: Without eager loading (User -> Company relations)
        $userResults = $this->testUserCompanyRelation($request);
        
        // Test 3: With vs without eager loading in list view
        $listResults = $this->testListView($request);
        
        return response()->json([
            'expense_user_relation_test' => $results,
            'user_company_relation_test' => $userResults,
            'list_view_test' => $listResults,
            'conclusion' => 'Eager loading significantly reduces database queries, especially with larger datasets.'
        ]);
    }
    
    /**
     * Test eager loading with expense -> user relation
     */
    private function testExpenseUserRelation(Request $request) 
    {
        // Clear any previous query log
        DB::flushQueryLog();
        DB::enableQueryLog();
        
        // Without eager loading
        Log::info('--- EXPENSE -> USER: WITHOUT EAGER LOADING ---');
        $expenses = Expense::where('company_id', $request->user()->company_id)
            ->limit(5)
            ->get();
        
        $mainQuery = DB::getQueryLog();
        DB::flushQueryLog();
        DB::enableQueryLog();
        
        // Access each user (this will cause N+1 queries)
        $userNames = [];
        foreach ($expenses as $expense) {
            $userNames[] = $expense->user->name;
        }
        
        $relationQueries = DB::getQueryLog();
        
        // Log the total queries
        $nonEagerTotal = count($mainQuery) + count($relationQueries);
        Log::info("Without eager loading: {$nonEagerTotal} queries needed for {$expenses->count()} expenses");
        
        // With eager loading
        DB::flushQueryLog();
        DB::enableQueryLog();
        
        Log::info('--- EXPENSE -> USER: WITH EAGER LOADING ---');
        $eagerExpenses = Expense::with('user')
            ->where('company_id', $request->user()->company_id)
            ->limit(5)
            ->get();
        
        $eagerMainQuery = DB::getQueryLog();
        DB::flushQueryLog();
        DB::enableQueryLog();
        
        // Access each user (should not cause additional queries)
        $eagerUserNames = [];
        foreach ($eagerExpenses as $expense) {
            $eagerUserNames[] = $expense->user->name;
        }
        
        $eagerRelationQueries = DB::getQueryLog();
        
        // Log the total queries
        $eagerTotal = count($eagerMainQuery) + count($eagerRelationQueries);
        Log::info("With eager loading: {$eagerTotal} queries needed for {$eagerExpenses->count()} expenses");
        
        // Format main query and relation queries for output
        $formattedMainQuery = [];
        foreach ($mainQuery as $query) {
            $formattedMainQuery[] = $this->formatQuery($query);
        }
        
        $formattedRelationQueries = [];
        foreach ($relationQueries as $query) {
            $formattedRelationQueries[] = $this->formatQuery($query);
        }
        
        $formattedEagerQueries = [];
        foreach ($eagerMainQuery as $query) {
            $formattedEagerQueries[] = $this->formatQuery($query);
        }
        
        return [
            'non_eager_loading' => [
                'total_queries' => $nonEagerTotal,
                'main_query' => $formattedMainQuery,
                'relation_queries' => $formattedRelationQueries,
                'explanation' => "1 main query + {$expenses->count()} user queries = {$nonEagerTotal} total queries"
            ],
            'eager_loading' => [
                'total_queries' => $eagerTotal,
                'queries' => $formattedEagerQueries,
                'explanation' => "Just {$eagerTotal} queries regardless of how many expense records"
            ],
            'summary' => "Eager loading saved " . ($nonEagerTotal - $eagerTotal) . " queries with just {$expenses->count()} records"
        ];
    }
    
    /**
     * Test eager loading with user -> company relation
     */
    private function testUserCompanyRelation(Request $request)
    {
        // Clear any previous query log
        DB::flushQueryLog();
        DB::enableQueryLog();
        
        // Without eager loading
        Log::info('--- USER -> COMPANY: WITHOUT EAGER LOADING ---');
        $users = User::where('company_id', $request->user()->company_id)
            ->limit(5)
            ->get();
        
        $mainQuery = DB::getQueryLog();
        DB::flushQueryLog();
        DB::enableQueryLog();
        
        // Access each company (this will cause N+1 queries)
        $companyNames = [];
        foreach ($users as $user) {
            $companyNames[] = $user->company->name;
        }
        
        $relationQueries = DB::getQueryLog();
        
        // Log the total queries
        $nonEagerTotal = count($mainQuery) + count($relationQueries);
        Log::info("Without eager loading: {$nonEagerTotal} queries needed for {$users->count()} users");
        
        // With eager loading
        DB::flushQueryLog();
        DB::enableQueryLog();
        
        Log::info('--- USER -> COMPANY: WITH EAGER LOADING ---');
        $eagerUsers = User::with('company')
            ->where('company_id', $request->user()->company_id)
            ->limit(5)
            ->get();
        
        $eagerMainQuery = DB::getQueryLog();
        DB::flushQueryLog();
        DB::enableQueryLog();
        
        // Access each company (should not cause additional queries)
        $eagerCompanyNames = [];
        foreach ($eagerUsers as $user) {
            $eagerCompanyNames[] = $user->company->name;
        }
        
        $eagerRelationQueries = DB::getQueryLog();
        
        // Log the total queries
        $eagerTotal = count($eagerMainQuery) + count($eagerRelationQueries);
        Log::info("With eager loading: {$eagerTotal} queries needed for {$eagerUsers->count()} users");
        
        // Format queries for output
        $formattedMainQuery = [];
        foreach ($mainQuery as $query) {
            $formattedMainQuery[] = $this->formatQuery($query);
        }
        
        $formattedRelationQueries = [];
        foreach ($relationQueries as $query) {
            $formattedRelationQueries[] = $this->formatQuery($query);
        }
        
        $formattedEagerQueries = [];
        foreach ($eagerMainQuery as $query) {
            $formattedEagerQueries[] = $this->formatQuery($query);
        }
        
        return [
            'non_eager_loading' => [
                'total_queries' => $nonEagerTotal,
                'main_query' => $formattedMainQuery,
                'relation_queries' => $formattedRelationQueries,
                'explanation' => "1 main query + {$users->count()} company queries = {$nonEagerTotal} total queries"
            ],
            'eager_loading' => [
                'total_queries' => $eagerTotal,
                'queries' => $formattedEagerQueries,
                'explanation' => "Just {$eagerTotal} queries regardless of how many user records"
            ],
            'summary' => "Eager loading saved " . ($nonEagerTotal - $eagerTotal) . " queries with just {$users->count()} records"
        ];
    }
    
    /**
     * Test eager loading in a list view scenario
     */
    private function testListView(Request $request)
    {
        // Clear any previous query log
        DB::flushQueryLog();
        DB::enableQueryLog();
        
        // Without eager loading - expenses with user and company
        Log::info('--- LIST VIEW: WITHOUT EAGER LOADING ---');
        $expenses = Expense::where('company_id', $request->user()->company_id)
            ->limit(5)
            ->get();
        
        $mainQuery = DB::getQueryLog();
        DB::flushQueryLog();
        DB::enableQueryLog();
        
        // Access both user and company (this will cause 2*N queries)
        $items = [];
        foreach ($expenses as $expense) {
            $items[] = [
                'expense_title' => $expense->title,
                'created_by' => $expense->user->name,
                'company' => $expense->company->name
            ];
        }
        
        $relationQueries = DB::getQueryLog();
        
        // Log the total queries
        $nonEagerTotal = count($mainQuery) + count($relationQueries);
        Log::info("Without eager loading in list view: {$nonEagerTotal} queries");
        
        // With eager loading
        DB::flushQueryLog();
        DB::enableQueryLog();
        
        Log::info('--- LIST VIEW: WITH EAGER LOADING ---');
        $eagerExpenses = Expense::with(['user', 'company'])
            ->where('company_id', $request->user()->company_id)
            ->limit(5)
            ->get();
        
        $eagerMainQuery = DB::getQueryLog();
        DB::flushQueryLog();
        DB::enableQueryLog();
        
        // Access both user and company (should not cause additional queries)
        $eagerItems = [];
        foreach ($eagerExpenses as $expense) {
            $eagerItems[] = [
                'expense_title' => $expense->title,
                'created_by' => $expense->user->name,
                'company' => $expense->company->name
            ];
        }
        
        $eagerRelationQueries = DB::getQueryLog();
        
        // Log the total queries
        $eagerTotal = count($eagerMainQuery) + count($eagerRelationQueries);
        Log::info("With eager loading in list view: {$eagerTotal} queries");
        
        // Format queries for output
        $formattedNonEagerQueries = array_map([$this, 'formatQuery'], array_merge($mainQuery, $relationQueries));
        $formattedEagerQueries = array_map([$this, 'formatQuery'], array_merge($eagerMainQuery, $eagerRelationQueries));
        
        return [
            'non_eager_loading' => [
                'total_queries' => $nonEagerTotal,
                'queries' => $formattedNonEagerQueries,
                'explanation' => "1 main query + {$expenses->count()} user queries + {$expenses->count()} company queries = {$nonEagerTotal} total queries"
            ],
            'eager_loading' => [
                'total_queries' => $eagerTotal,
                'queries' => $formattedEagerQueries,
                'explanation' => "Just {$eagerTotal} queries (1 expenses + 1 users + 1 companies) regardless of how many expense records"
            ],
            'summary' => "Eager loading saved " . ($nonEagerTotal - $eagerTotal) . " queries with just {$expenses->count()} records. Imagine with 100 records: " . (1 + (2 * 100)) . " vs. just 3 queries!"
        ];
    }
    
    /**
     * Format a query log entry for readability
     */
    private function formatQuery($query)
    {
        $sql = $query['query'];
        $bindings = $query['bindings'];
        $time = $query['time'];
        
        // Replace ? with actual values for better readability
        $index = 0;
        $formattedSql = preg_replace_callback('/\?/', function() use ($bindings, &$index) {
            $value = $bindings[$index] ?? 'null';
            $index++;
            if (is_numeric($value)) {
                return $value;
            }
            return "'$value'";
        }, $sql);
        
        return [
            'sql' => $formattedSql,
            'time_ms' => $time
        ];
    }
} 