# Architecture & Design Decisions

This document outlines the architectural decisions and design patterns used in the Multi-Tenant SaaS-Based Expense Management API.

## 🏗️ Overall Architecture

The application follows a standard Laravel MVC architecture with additional layers for services, observers, and policies to promote separation of concerns and maintainability.

```
├── app/
│   ├── Http/
│   │   ├── Controllers/API/   # API Controllers
│   │   ├── Middleware/        # Custom middleware for RBAC and tenant isolation
│   │   └── Requests/          # Form request validation
│   ├── Models/                # Eloquent models with relationships
│   ├── Jobs/                  # Background jobs
│   ├── Services/              # Service layer
│   ├── Observers/             # Model observers
│   └── Policies/              # Authorization policies
├── database/
│   ├── migrations/            # Database schema definitions
│   ├── factories/             # Model factories for testing
│   └── seeders/               # Database seeders
└── tests/                     # Test suite
```

## 🔑 Key Design Decisions

### 1. Multi-Tenant Strategy

 chose a **single database, multi-tenant** approach where each company's data is isolated through application logic rather than separate databases. This decision was made to:

- Simplify database administration
- Allow for easier reporting across all tenants when needed
- Maintain good performance through proper indexing

All tenant-specific queries include a `company_id` filter, enforced by:
- Middleware (`EnsureCompanyAccess`)
- Eloquent query scopes
- Policy-based authorization

### 2. Authentication & Authorization

 implemented a token-based authentication system using **Laravel Sanctum** because:

- It provides stateless API authentication
- It's lightight and purpose-built for SPAs and mobile apps
- It's integrated with Laravel's authentication system

For authorization,  used a combination of:
- Role-based middleware for coarse-grained control
- Policies for fine-grained permission checks
- Company-specific data filters for tenant isolation

### 3. Database Schema Design

The database schema was designed with these principles:

- **Normalization**: Tables are normalized to reduce redundancy
- **Performance**: Strategic indexes on frequently queried columns
- **Relationships**: Clear foreign key constraints between tables
- **Audit Trail**: Separate table for tracking changes

### 4. Query Optimization Techniques

To ensure good performance even with large datasets:

- **Eager Loading**: Used to prevent N+1 query problems
- **Database Indexing**: On columns used for filtering and joining
- **Redis Caching**: For frequently accessed, rarely changing data
- **Query Scopes**: Reusable query constraints

### 5. Background Processing

 used Laravel's queue system with these design decisions:

- **Jobs**: Self-contained, focused tasks
- **Redis Driver**: For reliable, persistent queue processing
- **Scheduled Tasks**: Weekly report generation via Laravel Scheduler
- **Error Handling**: Failed job tracking and retry mechanism

### 6. API Design Patterns

The API follows RESTful conventions with:

- Resource-based endpoints
- Appropriate HTTP methods (GET, POST, PUT, DELETE)
- Consistent response structure
- Proper status codes
- Pagination for list endpoints

## 🛠️ Implementation Details

### Multi-Tenant Implementation

```php
// Example of company isolation in middleware
public function handle($request, Closure $next)
{
    if ($request->route('company_id') && 
        $request->user()->company_id != $request->route('company_id')) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }
    return $next($request);
}
```

### Role-Based Access Control

```php
// Example of role-based middleware
public function handle($request, Closure $next, ...$roles)
{
    if (!in_array($request->user()->role, $roles)) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }
    return $next($request);
}
```

### Query Optimization

```php
// Example of eager loading in ExpenseController
public function index(Request $request)
{
    $query = Expense::with('user')
        ->where('company_id', $request->user()->company_id);
        
    // Filtering logic
    
    return $query->paginate(15);
}
```

### Background Job Processing

```php
// Weekly report job
public function handle()
{
    $companies = Company::all();
    foreach ($companies as $company) {
        $admins = User::where('company_id', $company->id)
            ->where('role', 'Admin')
            ->get();
            
        $expenses = Expense::where('company_id', $company->id)
            ->whereBetween('created_at', [now()->subWeek(), now()])
            ->get();
            
        foreach ($admins as $admin) {
            Mail::to($admin)->send(new WeeklyExpenseReport($expenses));
        }
    }
}
```

### Audit Logging

```php
// Logging changes via observer
public function updating(Expense $expense)
{
    $original = $expense->getOriginal();
    app(AuditLogger::class)->logAction(
        auth()->user(),
        'update',
        $original,
        $expense->toArray()
    );
}
```

## 📈 Future Improvements

Given more time, these architectural improvements could be made:

1. **Event Sourcing**: Implement a more robust event sourcing pattern for audit logs
2. **API Versioning**: Add versioning to the API for backward compatibility
3. **GraphQL**: Consider adding a GraphQL endpoint for more flexible querying
4. **Microservices**: Split functionalities into microservices for better scalability
5. **Real-time Updates**: Add WebSocket support for real-time notifications

## 📊 Performance Considerations

The current architecture should handle up to approximately:
- 1,000 companies
- 10,000 users
- 100,000 expenses

Beyond that, consider:
- Database sharding
- Read replicas
- More aggressive caching strategies
- Horizontal scaling 