# Veldora Framework Documentation

> **Veldora** — _A PHP framework you actually own._
> Modern PHP 8.2+ MVC architecture, expressive routing, Blade-inspired templates, guard-based authentication, 41+ UI components, queues, mail, events, cache, storage, and zero framework lock-in.

---

## 1. Getting Started & Installation

Veldora provides two first-class installation methods: **Composer** (standard for PHP workflows) and **npx / npm** (an interactive zero-config scaffolder).

### System Requirements

Make sure your machine or server meets the following requirements:

| Requirement | Minimum Version | Note |
|---|---|---|
| **PHP** | 8.2 or higher | Strict typing, readonly classes, constructor promotion |
| **PDO Extension** | Enabled | Required for all database drivers |
| **SQLite Extension** | Enabled | Used by default for instant local setup |
| **Composer** | 2.0+ | Standard PHP package manager |
| **Node.js** | 18+ | (Optional) Only required if using `npx` / `npm` |

---

### Option 1 — Composer Installation (Recommended)

You can create a fresh, self-contained Veldora project in seconds using Composer:

```bash
composer create-project veldora/veldora my-app
cd my-app
php veldora serve
```

```terminal
Creating a "veldora/veldora" project at "./my-app"
Installing veldora/veldora (v0.6.0)
  - Downloading veldora/veldora (v0.6.0)
  - Installing veldora/veldora (v0.6.0): Extracting archive
Created project in ./my-app
Generating optimized autoload files
> @php -r "file_exists('.env') || copy('.env.example', '.env');"
> @php veldora key:generate

✔ Application key set successfully!

🎉 Success! Created my-app at ./my-app
```

---

### Option 2 — Interactive npm / npx Installer

If you prefer using Node.js or an interactive prompt:

```bash
# Run instantly with npx (no global install needed):
npx create-veldora-app my-app

# Or install globally for a permanent `veldora` command:
npm install -g create-veldora-app
veldora new my-app
```

```terminal
  ▲ Veldora Framework  v0.6.0
  The modern PHP framework you actually own.

  ? What is your project named? (my-veldora-app): my-blog

  Creating a new Veldora app in ./my-blog...

  ✔ Configured project skeleton
  ✔ Generated secure APP_KEY
  ✔ Configured storage and logs directory

  🎉 Success! Created my-blog at ./my-blog
```

#### npm CLI Options

```bash
# Create project directly
npx create-veldora-app my-app

# Create using the `new` subcommand
npx create-veldora-app new my-app

# Show help guide and all available flags
npx create-veldora-app --help

# Check installed version
npx create-veldora-app --version
```

---

### Starting the Development Server

Once your project is created, enter the folder and start the built-in development server:

```bash
cd my-app
php veldora serve
```

Open **http://localhost:8000** in your browser. You will see the Veldora welcome screen.

To run on a custom port or host:

```bash
php veldora serve --port=8080 --host=0.0.0.0
```

---

### Project Structure Overview

```
my-app/
├── app/
│   ├── Controllers/          # Request handlers
│   ├── Middleware/           # HTTP filters (Auth, CSRF, Admin, etc.)
│   ├── Models/               # ActiveRecord ORM entities
│   ├── Services/             # Application business logic
│   ├── Events/               # Event classes
│   ├── Listeners/            # Event listeners
│   ├── Jobs/                 # Background queue jobs
│   ├── Mail/                 # Mailable classes
│   └── Http/
│       ├── Requests/         # Form validation request classes
│       └── Resources/        # JSON API resource transformers
├── bootstrap/
│   └── app.php               # Container & service registration
├── config/
│   ├── app.php               # App name, debug mode, timezone
│   ├── auth.php              # Guard settings & user providers
│   ├── database.php          # Connection credentials (sqlite, mysql, pgsql)
│   ├── mail.php              # SMTP & mail transport settings
│   ├── queue.php             # Queue driver settings (sync, database)
│   ├── cache.php             # Cache store settings (file, array)
│   ├── filesystems.php       # Storage disks (local, public)
│   ├── logging.php           # Log channels & daily log rotation
│   └── session.php           # Session driver & cookie configuration
├── database/
│   ├── factories/            # Model factories for testing & seeding
│   ├── migrations/           # Versioned database schema definitions
│   └── seeders/              # Database seeders
├── public/
│   └── index.php             # Web entry point
├── resources/
│   └── views/                # .veldora.php view templates
│       ├── components/       # UI components (<x-button>, etc.)
│       └── layouts/          # Base layouts
├── routes/
│   └── web.php               # Route definitions
├── storage/
│   ├── app/                  # Private file storage
│   ├── framework/            # Compiled views, sessions, cache
│   └── logs/                 # Daily application logs (app.log)
├── .env                      # Local environment configuration
├── .env.example              # Environment template
└── veldora                   # Framework CLI binary (php veldora ...)
```

---

### Environment & `.env` Configuration

Veldora loads environment variables from `.env` on every boot before configuration files are read:

```ini
APP_NAME=Veldora
APP_ENV=local
APP_KEY=base64:your_generated_app_key_here
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_DRIVER=sqlite
DB_DATABASE=database/veldora.sqlite

SESSION_DRIVER=file
SESSION_LIFETIME=120
QUEUE_DRIVER=sync
CACHE_DRIVER=file
```

Read environment variables in code using the global `env()` or `config()` helpers:

```php
$debug = env('APP_DEBUG', false);
$appName = config('app.name', 'Veldora');
```

---

## 2. Routing & HTTP Layer

All web routes are defined in `routes/web.php`. The framework injects the global `$router` instance automatically.

### Basic Routes

```php
// routes/web.php

use App\Controllers\HomeController;
use App\Controllers\PostController;
use App\Controllers\AuthController;

// Closure route returning a view
$router->get('/', fn() => view('welcome'));

// Controller action route
$router->get('/posts', [PostController::class, 'index']);
$router->post('/posts', [PostController::class, 'store']);
$router->get('/posts/{id}', [PostController::class, 'show']);
$router->put('/posts/{id}', [PostController::class, 'update']);
$router->delete('/posts/{id}', [PostController::class, 'destroy']);
```

### Route Parameters

Route parameters are wrapped in curly braces and injected directly into controller methods by name:

```php
// Route
$router->get('/users/{id}/posts/{slug}', [PostController::class, 'userPost']);

// Controller
class PostController
{
    public function userPost(string $id, string $slug): Response
    {
        $post = Post::where('user_id', '=', $id)
                    ->where('slug', '=', $slug)
                    ->first();

        return view('posts.show', ['post' => $post]);
    }
}
```

### Route Groups & Middleware

Group routes that share common path prefixes or middleware:

```php
// Protected routes
$router->group(['middleware' => ['auth']], function ($r) {
    $r->get('/dashboard', [DashboardController::class, 'index']);
    $r->get('/profile', [ProfileController::class, 'edit']);
    $r->put('/profile', [ProfileController::class, 'update']);
});

// Admin group with prefix and dual middleware
$router->group(['prefix' => '/admin', 'middleware' => ['auth', 'admin']], function ($r) {
    $r->get('/users', [AdminController::class, 'users']);
    $r->delete('/users/{id}', [AdminController::class, 'deleteUser']);
});
```

---

## 3. Controllers & Requests

Controllers organize your request handling logic into discrete classes.

### Generating a Controller

```bash
php veldora make:controller PostController
```

### Writing Controller Actions

```php
namespace App\Controllers;

use Veldora\Framework\Http\Request;
use Veldora\Framework\Http\Response;
use App\Models\Post;

class PostController
{
    public function index(Request $request): Response
    {
        $page = (int) $request->query('page', 1);
        $posts = Post::paginate(10, $page);

        return view('posts.index', ['posts' => $posts]);
    }

    public function store(Request $request): Response
    {
        $validated = $request->validated([
            'title' => 'required|min:3|max:255',
            'body'  => 'required',
        ]);

        $post = Post::create([
            'title'   => $validated['title'],
            'body'    => $validated['body'],
            'user_id' => auth()->id(),
        ]);

        return Response::redirect('/posts/' . $post->id)
            ->with('success', 'Post published successfully!');
    }
}
```

### HTTP Responses & Factories

Veldora provides a dedicated, expressive response system designed for modern APIs and full-stack web apps alike:

```php
use Veldora\Framework\Http\JsonResponse;
use Veldora\Framework\Http\RedirectResponse;
use Veldora\Framework\Http\Response;

// 1. View Responses
return view('welcome', ['name' => 'World']);

// 2. Global response() Factory
return response('Custom plain text', 200);
return response()->noContent();                         // 204 No Content
return response()->download($filePath, 'invoice.pdf');   // Stream as download
return response()->file($imagePath);                    // Display inline

// 3. JSON Responses
return json(['user' => $user]);
return JsonResponse::success(['token' => $token], 'Logged in successfully');
return JsonResponse::error('Validation failed', 422, ['email' => ['Invalid email']]);
return JsonResponse::paginate($items, ['total' => 100, 'current_page' => 1]);

// 4. Redirect Responses & Session Chaining
return redirect('/dashboard');
return redirect()->to('/login', 301);
return back()->with('status', 'Profile saved!');
return back()->withErrors($validator->errors())->withInput();
```

### Handling File Uploads

When receiving multipart form data, uploaded files are automatically wrapped in `Veldora\Framework\Http\UploadedFile`:

```php
public function uploadAvatar(Request $request): Response
{
    if (!$request->hasFile('avatar')) {
        return back()->with('error', 'Please choose a file to upload.');
    }

    $file = $request->file('avatar'); // UploadedFile instance

    // Validate type and maximum file size (in KB)
    $file->validate(['image/jpeg', 'image/png', 'image/webp'], maxKb: 2048);

    // Save with a unique generated UUID to target directory
    $savedPath = $file->store('storage/app/avatars');

    // Or save with explicit custom filename
    $customPath = $file->storeAs('storage/app/avatars', 'avatar_1.png');

    return back()->with('success', 'Avatar updated!');
}
```

### Session Management

Veldora includes a session wrapper accessible through the global `session()` helper or `$request->session()`:

```php
// Storing and retrieving values (supports dot-notation)
session()->put('user.settings.theme', 'dark');
$theme = session()->get('user.settings.theme', 'light');

// Pull (get and delete)
$flashNotice = session()->pull('notice');

// Flash messages (persisted for the next request only)
session()->flash('success', 'Order placed!');

// CSRF Protection
$token = session()->token();
$valid = session()->verifyToken($request->input('_token'));
```

---

## 4. Blade-Inspired Templates

Veldora templates end with `.veldora.php` and live in `resources/views/`. They provide clean syntax and compile to pure PHP without runtime overhead.

### Outputting Variables

```html
<!-- Escaped output (prevents XSS) -->
<h1>&#123;&#123; $post->title &#125;&#125;</h1>

<!-- Raw unescaped HTML -->
<div>{!! $post->content_html !!}</div>
```

### Control Directives

```html
<!-- Conditionals -->
&#64;if($user->isAdmin())
    <span class="badge">Admin</span>
&#64;elseif($user->isEditor())
    <span class="badge">Editor</span>
&#64;else
    <span>Member</span>
&#64;endif

<!-- Loops -->
&#64;foreach($posts as $post)
    <div class="card">
        <h3>&#123;&#123; $post->title &#125;&#125;</h3>
    </div>
&#64;endforeach

<!-- Forelse with fallback -->
&#64;forelse($comments as $comment)
    <p>&#123;&#123; $comment->body &#125;&#125;</p>
&#64;empty
    <p>No comments yet.</p>
&#64;endforelse

<!-- Auth state checks -->
&#64;auth
    <a href="/dashboard">Dashboard</a>
    <a href="/logout">Logout</a>
&#64;endauth

&#64;guest
    <a href="/login">Login</a>
    <a href="/register">Register</a>
&#64;endguest
```

### Layout Inheritance & Yields

Create a parent layout in `resources/views/layouts/app.veldora.php`:

```html
<!DOCTYPE html>
<html>
<head>
    <title>&#64;yield('title', 'My Application')</title>
</head>
<body>
    <header>
        <nav><!-- navigation links --></nav>
    </header>

    <main>
        &#64;yield('content')
    </main>

    <footer>&copy; &#123;&#123; date('Y') &#125;&#125; Veldora</footer>
</body>
</html>
```

Extend it in your page views:

```html
&#64;extends('layouts.app')

&#64;section('title', 'Blog Posts')

&#64;section('content')
    <h1>All Posts</h1>
    <!-- Page content here -->
&#64;endsection
```

### Reusable UI Components

Use the `<x-component-name>` syntax to render reusable components:

```html
<x-button variant="primary" size="lg">Save Changes</x-button>

<x-card title="Account Settings">
    <x-input name="username" label="Username" value="&#123;&#123; $user->name &#125;&#125;" />
</x-card>
```

---

## 5. Database, Schema & Migrations

Veldora supports SQLite, MySQL, and PostgreSQL with a unified Schema Blueprint.

### Creating a Migration

```bash
php veldora make:migration create_posts_table
```

```php
use Veldora\Framework\Database\Schema\Blueprint;
use Veldora\Framework\Database\Schema\Migration;
use Veldora\Framework\Database\Schema\Schema;

class CreatePostsTable extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('body');
            $table->boolean('is_published')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
}
```

### Migration Commands

```bash
# Run pending migrations
php veldora migrate

# Check migration status
php veldora migrate:status

# Rollback last migration batch
php veldora migrate:rollback

# Reset database & re-run all migrations
php veldora migrate:fresh
```

### Available Blueprint Column Types

| Method | SQL Type | Description |
|---|---|---|
| `$table->id()` | AUTO_INCREMENT PK | Primary key ID |
| `$table->string('name', 255)` | VARCHAR(255) | String column |
| `$table->text('content')` | TEXT | Large text block |
| `$table->integer('count')` | INT | Integer |
| `$table->boolean('active')` | TINYINT(1) / INTEGER | Boolean true/false |
| `$table->timestamp('created_at')` | TIMESTAMP | Date & time |
| `$table->timestamps()` | created_at + updated_at | Auto timestamp pair |
| `$table->softDeletes()` | deleted_at | Soft deletion column |
| `$table->rememberToken()` | remember_token | Auth remember token |

---

## 6. ActiveRecord Models & Query Builder

Models extend `Veldora\Framework\Database\Model` and map directly to database tables.

### Creating a Model

```bash
php veldora make:model Post
```

```php
namespace App\Models;

use Veldora\Framework\Database\Model;
use Veldora\Framework\Database\Relations\BelongsTo;
use Veldora\Framework\Database\Relations\HasMany;

class Post extends Model
{
    // Allowed fields for mass assignment
    protected array $fillable = [
        'title',
        'slug',
        'body',
        'user_id',
        'is_published',
        'published_at',
    ];

    // Automatic type casting
    protected array $casts = [
        'is_published' => 'bool',
        'published_at' => 'datetime',
    ];

    // Hidden from JSON serialization
    protected array $hidden = ['deleted_at'];
}
```

### CRUD Operations

```php
// Create
$post = Post::create([
    'title' => 'New Release',
    'slug'  => 'new-release',
    'body'  => 'Veldora 1.0 is here!',
]);

// Find by ID
$post = Post::find(1);

// Find or fail
$post = Post::where('slug', '=', 'new-release')->first();

// Update
$post->title = 'Updated Title';
$post->save();

// Delete
$post->delete();

// Query builder chain
$published = Post::where('is_published', '=', 1)
                 ->where('user_id', '=', 5)
                 ->orderBy('created_at', 'DESC')
                 ->limit(10)
                 ->get();

// Paginate results
$paginator = Post::where('is_published', '=', 1)->paginate(15);
```

---

## 7. Model Relationships

Veldora's ActiveRecord ORM supports intuitive relationships between database tables. Relations allow you to define connections directly inside model classes and traverse them cleanly without writing complex `JOIN` queries.

### Supported Relationship Types

| Relationship | Method | Example Use Case |
|---|---|---|
| **One-to-One** | `$this->hasOne(Profile::class)` | A User has one Profile |
| **One-to-Many** | `$this->hasMany(Post::class)` | An Author has many Posts |
| **Inverse One-to-Many** | `$this->belongsTo(User::class)` | A Post belongs to an Author |
| **Many-to-Many** | `$this->belongsToMany(Role::class, 'role_user')` | A User has many Roles via a pivot table |
| **Has-One-Through** | `$this->hasOneThrough(History::class, User::class)` | A Supplier has one History through a User |
| **Has-Many-Through** | `$this->hasManyThrough(Post::class, User::class)` | A Country has many Posts through its Users |
| **Polymorphic One-to-One** | `$this->morphOne(Image::class, 'imageable')` | A User or Post has one Image |
| **Polymorphic One-to-Many** | `$this->morphMany(Comment::class, 'commentable')` | A Post or Video has many Comments |
| **Polymorphic Many-to-Many** | `$this->morphToMany(Tag::class, 'taggable')` | A Post or Video has many Tags via shared pivot |
| **Polymorphic Inverse** | `$this->morphTo('commentable')` | A Comment resolves back to Post or Video |
| **Polymorphic Many Inverse** | `$this->morphedByMany(Post::class, 'taggable')` | A Tag retrieves all tagged Posts or Videos |

### Defining Standard Relationships

```php
namespace App\Models;

use Veldora\Framework\Database\Model;
use Veldora\Framework\Database\Relations\BelongsTo;
use Veldora\Framework\Database\Relations\HasMany;
use Veldora\Framework\Database\Relations\HasOne;
use Veldora\Framework\Database\Relations\BelongsToMany;
use Veldora\Framework\Database\Relations\HasOneThrough;

class User extends Model
{
    // One-to-One
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class, 'user_id');
    }

    // One-to-Many
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    // Many-to-Many via pivot table
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }
}

class Supplier extends Model
{
    // Has-One-Through (Supplier -> User -> History)
    public function userHistory(): HasOneThrough
    {
        return $this->hasOneThrough(History::class, User::class, 'supplier_id', 'user_id');
    }
}
```

### Defining Polymorphic Relationships

Polymorphic relations allow a single model to belong to more than one other type of model on a single association:

```php
namespace App\Models;

use Veldora\Framework\Database\Model;
use Veldora\Framework\Database\Relations\MorphTo;
use Veldora\Framework\Database\Relations\MorphOne;
use Veldora\Framework\Database\Relations\MorphMany;
use Veldora\Framework\Database\Relations\MorphToMany;
use Veldora\Framework\Database\Relations\MorphedByMany;

class Post extends Model
{
    // Polymorphic One-to-One
    public function headerImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    // Polymorphic One-to-Many
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    // Polymorphic Many-to-Many
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}

class Comment extends Model
{
    // Polymorphic Inverse (resolves owner: Post, Video, Podcast, etc.)
    public function commentable(): MorphTo
    {
        return $this->morphTo('commentable');
    }
}

class Tag extends Model
{
    // Inverse Polymorphic Many-to-Many
    public function posts(): MorphedByMany
    {
        return $this->morphedByMany(Post::class, 'taggable');
    }
}
```

### Querying and Creating Across Relations

Access related models as dynamic properties or chain queries directly:

```php
$user = User::find(1);

// Access relations as dynamic properties
$posts = $user->posts;

// Query builder chaining via relation proxy
$published = $user->posts()->where('is_published', '=', 1)->get();

// Create related polymorphic records directly
$post = Post::find(10);
$comment = $post->comments()->create([
    'body' => 'Great overview of Veldora relationships!',
]);

// Attach / Detach / Sync on polymorphic and pivot relations
$post->tags()->attach([1, 2]);
$post->tags()->detach(1);
$post->tags()->sync([2, 3, 5]);
```

---

## 8. Authentication System

Veldora includes a robust, secure authentication system out of the box with session management, password hashing (Argon2id/Bcrypt), remember tokens, and route protection middleware.

### Generating Full Auth Scaffold

Generate complete, production-ready login, registration, and dashboard controllers and views with a single command:

```bash
php veldora make:auth
php veldora migrate
```

This creates:
- `app/Controllers/AuthController.php` — Handles login, registration, logout, and password resets
- `app/Models/User.php` — Authenticatable model with password hashing and fillable attributes
- `database/migrations/*_create_users_table.php` — Database schema with `email`, `password`, `remember_token`
- `resources/views/auth/login.veldora.php` — Accessible login form with CSRF token
- `resources/views/auth/register.veldora.php` — Registration form with client and server validation
- `resources/views/dashboard.veldora.php` — Protected user dashboard
- Automatic authentication routes registered in `routes/web.php`

### Global Authentication Helpers

```php
// Check if the current visitor is logged in
if (auth()->check()) {
    $user = auth()->user(); // Returns current User model instance
    $userId = auth()->id();  // Returns current user ID
}

// Log in a specific user model
auth()->login($user, $remember = true);

// Log out and invalidate the session
auth()->logout();
```

### Protecting Routes with Middleware

Protect routes so only authenticated users or guests can access them:

```php
// Only logged-in users can access
$router->group(['middleware' => ['auth']], function ($r) {
    $r->get('/dashboard', [DashboardController::class, 'index']);
    $r->get('/settings',  [SettingsController::class, 'index']);
});

// Only guests (unauthenticated visitors) can access login/register
$router->group(['middleware' => ['guest']], function ($r) {
    $r->get('/login',    [AuthController::class, 'showLogin']);
    $r->post('/login',   [AuthController::class, 'login']);
    $r->get('/register', [AuthController::class, 'showRegister']);
    $r->post('/register',[AuthController::class, 'register']);
});
```

### Password Hashing

Passwords are automatically hashed securely using PHP's native `password_hash()` with modern Argon2id or Bcrypt:

```php
use Veldora\Framework\Support\Hash;

// Hash a plaintext password
$hashed = Hash::make($request->input('password'));

// Verify password against hash
if (Hash::check($request->input('password'), $user->password)) {
    // Password matches!
}
```

---

## 9. Validation & Form Requests

Veldora provides an expressive, rule-based validation engine. You can perform inline validation directly inside controller actions or encapsulate validation logic inside reusable Form Request classes.

### Inline Validation in Controllers

The `$request->validated()` method validates incoming request data against a set of rules. If validation fails, it automatically redirects back with input errors and old form values:

```php
public function store(Request $request): Response
{
    $data = $request->validated([
        'title'    => 'required|min:3|max:255',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed',
        'age'      => 'nullable|integer|min:18',
        'status'   => 'required|in:draft,published,archived',
    ]);

    // If execution reaches here, data is 100% valid
    $user = User::create($data);

    return Response::redirect('/users')
        ->with('success', 'User created successfully!');
}
```

### Available Validation Rules

| Rule | Description | Example |
|---|---|---|
| `required` | Field must be present and not empty | `'name' => 'required'` |
| `nullable` | Field may be null or empty | `'phone' => 'nullable\|numeric'` |
| `email` | Must be a valid email address format | `'email' => 'required\|email'` |
| `min:value` | Minimum string length or numeric value | `'password' => 'min:8'` |
| `max:value` | Maximum string length or numeric value | `'title' => 'max:255'` |
| `numeric` | Must be a numeric value | `'price' => 'required\|numeric'` |
| `integer` | Must be an integer | `'age' => 'required\|integer\|min:1'` |
| `in:foo,bar` | Must match one of the allowed values | `'role' => 'in:admin,editor,user'` |
| `confirmed` | Field must match `{field}_confirmation` | `'password' => 'confirmed'` |
| `unique:table,col` | Must be unique in the specified database table | `'email' => 'unique:users,email'` |
| `url` | Must be a valid URL | `'website' => 'nullable\|url'` |

### Displaying Validation Errors in Views

In your `.veldora.php` templates, display validation feedback easily:

```html
<form method="POST" action="/users">
    @csrf

    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" class="input">
        @error('name')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" class="input">
        @error('email')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Create User</button>
</form>
```

### Dedicated Form Request Classes

For complex forms, keep controllers clean by creating a dedicated Form Request:

```bash
php veldora make:request StoreUserRequest
```

```php
namespace App\Http\Requests;

use Veldora\Framework\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    // Authorization logic: return false to abort with 403 Forbidden
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_admin;
    }

    // Validation rules
    public function rules(): array
    {
        return [
            'name'     => 'required|min:2|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ];
    }
}
```

### Custom Validation Rules

When built-in rules are not enough, generate a dedicated validation rule class:

```bash
php veldora make:rule Uppercase
```

This creates `app/Rules/Uppercase.php` implementing `Veldora\Framework\Validation\Rule`:

```php
namespace App\Rules;

use Veldora\Framework\Validation\Rule;

class Uppercase implements Rule
{
    /**
     * Determine if the validation rule passes.
     */
    public function passes(string $attribute, mixed $value): bool
    {
        return is_string($value) && strtoupper($value) === $value;
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return 'The :attribute must be uppercase.';
    }
}
```

Use your custom rule by passing an array of rules to `$request->validate()`:

```php
use App\Rules\Uppercase;

$validated = $request->validate([
    'code' => ['required', 'string', new Uppercase()],
]);
```

---

## 10. CLI Console & 51 Built-in Commands

Veldora includes a powerful, zero-dependency CLI runner (`php veldora`) featuring **51 built-in commands**. Powered by `executeDirect()`, every command executes instantly in both zero-dependency environments and standard Symfony Console environments.

```bash
php veldora <command> [options]
```

### Application & Diagnostics

| Command | Description |
|---|---|
| `php veldora serve` | Start local development server with real-time logs (`--port=8000`, `--host=127.0.0.1`) |
| `php veldora about` | Display framework environment, database driver, PHP version, and cache status |
| `php veldora doctor` | Run system health diagnostics, check PHP extensions, and verify directory permissions |
| `php veldora key:generate` | Generate a cryptographically secure 32-character `APP_KEY` in `.env` |
| `php veldora storage:link` | Create symbolic link from `public/storage` to `storage/app/public` |
| `php veldora down` | Put the application into maintenance mode (optional: `--secret=your-token` for bypass) |
| `php veldora up` | Bring the application out of maintenance mode |
| `php veldora env` | Display current application environment name |
| `php veldora env:encrypt` | Encrypt `.env` file using AES-256 with `APP_KEY` for safe version control |
| `php veldora env:decrypt` | Decrypt `.env.encrypted` file using `APP_KEY` on production servers |

### Code Generators (`make:*`)

| Command | Description |
|---|---|
| `php veldora make:controller <Name>` | Scaffold a new HTTP Controller class in `app/Controllers/` |
| `php veldora make:model <Name> [-m]` | Scaffold an ActiveRecord Model class (`-m` automatically creates migration) |
| `php veldora make:migration <name>` | Create an anonymous class database migration in `database/migrations/` |
| `php veldora make:middleware <Name>` | Scaffold an HTTP Middleware class in `app/Http/Middleware/` |
| `php veldora make:policy <Name>` | Scaffold an authorization Policy class in `app/Policies/` (`--model=Post`) |
| `php veldora make:observer <Name>` | Scaffold a Model Observer class in `app/Observers/` (`--model=User`) |
| `php veldora make:rule <Name>` | Scaffold a custom Validation Rule class in `app/Rules/` |
| `php veldora make:request <Name>` | Create a validated Form Request class in `app/Http/Requests/` |
| `php veldora make:resource <Name>` | Create an API JSON Resource transformer in `app/Http/Resources/` |
| `php veldora make:factory <Name>` | Create a Model Factory in `database/factories/` (`--model=User`) |
| `php veldora make:seeder <Name>` | Create a Database Seeder in `database/seeders/` |
| `php veldora make:command <Name>` | Create a custom Veldora Console Command class |
| `php veldora make:job <Name>` | Scaffold a background Queue Job class in `app/Jobs/` |
| `php veldora make:event <Name>` | Scaffold a dispatchable Event class in `app/Events/` |
| `php veldora make:listener <Name>` | Scaffold an Event Listener in `app/Listeners/` |
| `php veldora make:mail <Name>` | Scaffold a Mailable email class in `app/Mail/` with HTML view template |
| `php veldora make:component <name>` | Create a new UI component template in `resources/views/components/` |
| `php veldora make:auth` | Scaffold full authentication (Login, Register, Forgot/Reset Password, Profile, Views, Routes) |

### Database Management

| Command | Description |
|---|---|
| `php veldora migrate` | Run pending database migrations |
| `php veldora migrate:rollback` | Rollback the last migration batch (`--step=N` to rollback N steps) |
| `php veldora migrate:fresh` | Drop all tables and re-run all migrations from scratch (`--seed` to auto-seed) |
| `php veldora migrate:status` | Show the status and batch table for all migrations |
| `php veldora db:seed` | Run database seeders from `database/seeders/` (`--class=UserSeeder`) |
| `php veldora db:wipe` | Drop all tables, views, and types in the database |
| `php veldora db:show` | Display database schema overview, table list, and row counts |

### Routing & URL Generation

| Command | Description |
|---|---|
| `php veldora route:list` | Display a formatted table of all registered routes, HTTP methods, actions, and middleware |
| `php veldora route:cache` | Compile and cache registered routes into `storage/framework/routes.cache.php` |
| `php veldora route:clear` | Remove the route cache file for local development |

### Optimization & Caching

| Command | Description |
|---|---|
| `php veldora optimize` | Cache framework configuration, routes, and views for production |
| `php veldora optimize:clear` | Clear all cached bootstrap files and templates |
| `php veldora config:cache` | Compile all config files into a single cached file for fast boot |
| `php veldora config:clear` | Remove configuration cache |
| `php veldora config:show <key>` | Display configuration values for a specific key |
| `php veldora view:cache` | Compile all `.veldora.php` templates into PHP cache files |
| `php veldora view:clear` | Flush all compiled view templates from `storage/framework/views/` |
| `php veldora cache:clear` | Flush application and session cache |

### Queue Processing

| Command | Description |
|---|---|
| `php veldora queue:work` | Start processing jobs on the queue daemon (`--queue=default`, `--sleep=3`, `--tries=3`) |
| `php veldora queue:failed` | Display a list of all failed queue jobs with exception traces |
| `php veldora queue:retry <id|all>` | Retry a specific failed job by ID or retry all failed jobs |
| `php veldora queue:clear` | Delete all pending jobs from the specified queue |

### UI Component Management

| Command | Description |
|---|---|
| `php veldora ui:list` | List all 41+ available UI components and their installation status |
| `php veldora add <components...>` | Install UI components into `resources/views/components/` (e.g. `php veldora add button card modal`) |


---

## 11. Events & Listeners

Events provide a simple observer pattern implementation, allowing you to subscribe and listen for various events that occur in your application. This cleanly decouples business operations (such as sending a welcome email after registration) from your HTTP controllers.

### Generating Events & Listeners

```bash
php veldora make:event OrderPlaced
php veldora make:listener SendOrderConfirmation
```

### Defining the Event

Events are lightweight data containers holding the information related to the event:

```php
// app/Events/OrderPlaced.php
namespace App\Events;

use App\Models\Order;
use Veldora\Framework\Events\Event;

class OrderPlaced extends Event
{
    public function __construct(
        public readonly Order $order
    ) {}
}
```

### Defining the Listener

Listeners handle the logic when an event is fired:

```php
// app/Listeners/SendOrderConfirmation.php
namespace App\Listeners;

use Veldora\Framework\Events\Event;
use Veldora\Framework\Events\Listener;
use App\Mail\OrderInvoiceEmail;

class SendOrderConfirmation implements Listener
{
    public function handle(Event $event): void
    {
        // Access event payload directly
        $order = $event->order;

        // Send email via mailer
        mailer($order->customer_email)->send(new OrderInvoiceEmail($order));

        log_info('Order invoice sent', ['order_id' => $order->id]);
    }
}
```

### Registering Events and Listeners

Register your event-listener mappings in `config/events.php`:

```php
return [
    'listen' => [
        \App\Events\OrderPlaced::class => [
            \App\Listeners\SendOrderConfirmation::class,
            \App\Listeners\UpdateInventoryStock::class,
        ],
        \App\Events\UserRegistered::class => [
            \App\Listeners\SendWelcomeNotification::class,
        ],
    ],
];
```

### Dispatching Events

Dispatch events anywhere in your application:

```php
use App\Events\OrderPlaced;

// Option 1: Static dispatch method
OrderPlaced::dispatch($order);

// Option 2: Global event() helper
event(new OrderPlaced($order));
```

---

## 12. Background Queues & Jobs

Queues allow you to defer time-consuming tasks (like sending emails, processing images, or calling third-party webhooks) to a background process, dramatically speeding up web request response times.

### Queue Drivers

Configure the queue driver in `.env`:

```ini
QUEUE_DRIVER=database   # Options: sync, database
```

| Driver | Description | Best For |
|---|---|---|
| `sync` | Runs jobs immediately in the same process | Local debugging & testing |
| `database` | Stores jobs in the `jobs` database table and processes asynchronously | Production apps |

### Creating a Job

```bash
php veldora make:job ProcessVideoEncoding
```

```php
// app/Jobs/ProcessVideoEncoding.php
namespace App\Jobs;

use Veldora\Framework\Queue\Job;

class ProcessVideoEncoding extends Job
{
    public int $maxTries = 3;     // Maximum attempts before failing
    public int $retryAfter = 60;  // Delay in seconds between retries

    public function __construct(
        public readonly int $videoId,
        public readonly string $format = 'mp4'
    ) {}

    public function handle(): void
    {
        // Heavy processing logic runs in background worker
        log_info("Encoding video {$this->videoId} to {$this->format}");

        // Perform encoding...
    }
}
```

### Dispatching Jobs

```php
use App\Jobs\ProcessVideoEncoding;

// Dispatch immediately to default queue
ProcessVideoEncoding::dispatch($video->id);

// Dispatch with a delay (runs after 2 minutes)
ProcessVideoEncoding::dispatch($video->id)->delay(120);

// Dispatch to a specific queue channel
ProcessVideoEncoding::dispatch($video->id)->onQueue('media');
```

### Running the Queue Worker

Run the background worker via the CLI:

```bash
# Process jobs continuously on default queue
php veldora queue:work

# Specify queue channel and sleep interval
php veldora queue:work --queue=media,default --sleep=3 --tries=3
```

---

## 13. Mail & SMTP Transport

Veldora includes a clean, expressive Mailable system for sending HTML and plain-text emails via SMTP, Mailgun, or local log file transport.

### Configuration

Configure your mail settings in `.env`:

```ini
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="Veldora App"
```

### Creating a Mailable

```bash
php veldora make:mail WelcomeEmail
php veldora make:mail OrderReceiptEmail
```

### Defining a Mailable

```php
// app/Mail/WelcomeEmail.php
namespace App\Mail;

use App\Models\User;
use Veldora\Framework\Mail\Mailable;

class WelcomeEmail extends Mailable
{
    public function __construct(
        public readonly User $user
    ) {}

    public function build(): static
    {
        return $this
            ->subject('Welcome to ' . config('app.name') . '!')
            ->from('hello@example.com', 'Veldora Team')
            ->view('emails.welcome', [
                'user'      => $this->user,
                'loginUrl'  => url('/login'),
            ]);
    }
}
```

### Designing the Email View Template

Create the template at `resources/views/emails/welcome.veldora.php`:

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Hello, &#123;&#123; $user->name &#125;&#125;!</h2>
    <p>Thank you for joining <strong>&#123;&#123; config('app.name') &#125;&#125;</strong>. We're excited to have you on board.</p>
    <p>
        <a href="&#123;&#123; $loginUrl &#125;&#125;" style="background: #8b5cf6; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 6px; display: inline-block;">
            Go to Your Dashboard
        </a>
    </p>
</body>
</html>
```

### Sending & Queueing Emails

```php
use App\Mail\WelcomeEmail;

// Send immediately via SMTP
mailer($user->email)->send(new WelcomeEmail($user));

// Queue for background delivery (requires queue:work running)
mailer($user->email)->queue(new WelcomeEmail($user));

// Send with CC and BCC
mailer($user->email)
    ->cc('admin@example.com')
    ->bcc('audit@example.com')
    ->send(new WelcomeEmail($user));
```

---

## 14. Cache System

The cache system lets you store the result of expensive database queries, computed data, or external API responses so they don't need to be recalculated on every request. Veldora supports two drivers: **file** (default, persists to disk) and **array** (in-memory, resets per request — useful for testing).

### Configuration

Set the driver in your `.env` file:

```ini
CACHE_DRIVER=file   # Options: file, array
```

Or change it per-environment in `config/cache.php`.

### Cache Drivers

| Driver | Description | When to use |
|---|---|---|
| `file` | Stores cached items in `storage/framework/cache/` | Default for most apps; persistent across requests |
| `array` | Stores in PHP memory only; lost after each request | Automated tests, local debugging |

### Storing & Reading Values

```php
// Store a value for 10 minutes (600 seconds)
cache(['top_posts' => $posts], 600);

// Retrieve a cached value (returns null if missing or expired)
$posts = cache('top_posts');
```

### The `remember()` Pattern (Recommended)

The `remember()` helper is the cleanest way to use the cache. It checks if a value is cached — if yes, it returns it; if not, it runs your closure, stores the result, and returns it:

```php
// Fetch from cache, or run the closure and cache for 1 hour
$stats = cache()->remember('dashboard_stats', 3600, function () {
    return [
        'total_users' => User::count(),
        'total_posts' => Post::count(),
        'new_today'   => User::where('created_at', '>=', date('Y-m-d'))->count(),
    ];
});
```

This is the preferred pattern for controller actions that serve expensive aggregated data.

### All Cache Methods

```php
// Store (TTL in seconds)
cache(['key' => $value], 3600);
cache()->put('key', $value, 600);

// Retrieve
$value = cache('key');            // Returns null if missing
$value = cache('key', 'default'); // Returns default if missing

// Remember (fetch or compute)
$value = cache()->remember('key', 3600, fn() => expensiveQuery());

// Check existence
if (cache()->has('key')) { ... }

// Remove single item
cache()->forget('key');

// Clear entire cache store
cache()->flush();

// Atomic counter increment / decrement (great for rate limiting, view counts)
cache()->increment('page_views:post-42');
cache()->increment('page_views:post-42', 5); // Increment by 5
cache()->decrement('credits_remaining');

// Store forever (no expiry)
cache()->forever('site_settings', $settings);
```

### Practical Example — Caching Posts in a Controller

```php
public function index(Request $request): Response
{
    $page = (int) $request->query('page', 1);

    $posts = cache()->remember("posts:page:{$page}", 300, function () use ($page) {
        return Post::where('is_published', '=', 1)
                   ->orderBy('created_at', 'DESC')
                   ->paginate(15, $page);
    });

    return view('posts.index', ['posts' => $posts]);
}
```

---

## 15. File Storage & Disks

Veldora provides a unified filesystem API for managing files across multiple storage **disks**. By default, two disks are pre-configured: `local` (private, server-only) and `public` (web-accessible via a URL).

### Configuration

Disks are defined in `config/filesystems.php`:

```php
return [
    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root'   => storage_path('app'),       // storage/app/
        ],
        'public' => [
            'driver' => 'local',
            'root'   => storage_path('app/public'), // storage/app/public/
            'url'    => '/storage',                 // Web URL prefix
        ],
    ],
];
```

### When to Use Each Disk

| Disk | Path | Web Accessible | Use For |
|---|---|---|---|
| `local` | `storage/app/` | ❌ No | Private files: invoices, exports, backups |
| `public` | `storage/app/public/` | ✅ Yes (via `/storage/`) | User-uploaded avatars, images, documents |

### Storing Files

```php
// Store raw binary content
storage('public')->put('avatars/user-42.png', $binaryContent);

// Store an uploaded file from a request
$file = $request->file('avatar'); // Returns a PHP SplFileInfo instance
$path = 'avatars/' . uniqid() . '.jpg';
storage('public')->put($path, file_get_contents($file->getPathname()));
```

### Reading Files

```php
// Read raw file contents
$content = storage('local')->get('exports/report.csv');

// Get file size in bytes
$size = storage('local')->size('exports/report.csv');

// Check if file exists
if (storage('public')->exists('avatars/user-42.png')) {
    echo 'File found!';
}
```

### Generating Public URLs

For files on the `public` disk, generate a browser-accessible URL:

```php
// Returns: /storage/avatars/user-42.png
$url = storage('public')->url('avatars/user-42.png');

// Use in a view:
// <img src="{{ $url }}" alt="Avatar">
```

### Deleting Files

```php
// Delete a single file
storage('public')->delete('avatars/old-avatar.png');

// Delete multiple files
storage('public')->delete(['thumbs/img1.jpg', 'thumbs/img2.jpg']);
```

### Listing Files in a Directory

```php
// List all files in a directory
$files = storage('public')->files('avatars');

// List all directories
$dirs = storage('local')->directories('exports');
```

### Practical Example — File Upload Controller

```php
public function uploadAvatar(Request $request): Response
{
    $validated = $request->validated([
        'avatar' => 'required',
    ]);

    $user = auth()->user();
    $file = $request->file('avatar');

    // Delete old avatar if it exists
    if ($user->avatar_path && storage('public')->exists($user->avatar_path)) {
        storage('public')->delete($user->avatar_path);
    }

    // Store new avatar
    $path = 'avatars/user-' . $user->id . '-' . uniqid() . '.jpg';
    storage('public')->put($path, file_get_contents($file->getPathname()));

    // Save path to user record
    $user->avatar_path = $path;
    $user->save();

    return Response::redirect('/profile')
        ->with('success', 'Avatar updated successfully!');
}
```

---

## 16. PSR-3 Logging

Veldora includes a PSR-3 compliant logger that writes structured log entries to daily rotating log files in `storage/logs/`. Each log entry includes a timestamp, severity level, message, and optional context array.

### Log File Location

Log files are stored at `storage/logs/app.log` and automatically rotate daily (e.g., `app-2026-08-23.log`) based on your `config/logging.php` settings.

### Log Levels

Veldora supports all 8 standard PSR-3 log levels, from lowest to highest severity:

| Level | Helper | Use For |
|---|---|---|
| `debug` | `log_debug()` | Detailed development/diagnostic info |
| `info` | `log_info()` | Normal application events (user login, payment processed) |
| `notice` | `logger()->notice()` | Normal but significant conditions |
| `warning` | `logger()->warning()` | Non-critical issues that should be investigated |
| `error` | `log_error()` | Runtime errors that don't require immediate action |
| `critical` | `logger()->critical()` | Critical conditions (component unavailable) |
| `alert` | `logger()->alert()` | Action must be taken immediately |
| `emergency` | `logger()->emergency()` | System is unusable |

### Using Global Helpers

```php
// Informational events (user actions, successful operations)
log_info('User signed in', [
    'user_id' => $user->id,
    'ip'      => $request->ip(),
    'agent'   => $request->userAgent(),
]);

// Debug info during development
log_debug('Slow query detected', [
    'query'   => $sql,
    'elapsed' => '1250ms',
]);

// Errors that need investigation
log_error('Payment gateway failure', [
    'order_id' => $order->id,
    'gateway'  => 'stripe',
    'error'    => $e->getMessage(),
    'trace'    => $e->getTraceAsString(),
]);
```

### Using the Logger Instance

```php
$logger = logger();

$logger->warning('Rate limit approaching threshold', ['attempts' => 95, 'limit' => 100]);
$logger->critical('Database connection lost!', ['host' => config('database.host')]);
$logger->alert('Disk space below 5%', ['free_bytes' => disk_free_space('/'), 'path' => '/']);
```

### Logging in Exception Handlers

A common pattern is to log errors inside try/catch blocks:

```php
public function processPayment(Request $request): Response
{
    try {
        $charge = $this->paymentService->charge(
            $request->input('amount'),
            $request->input('card_token')
        );

        log_info('Payment successful', [
            'user_id'    => auth()->id(),
            'amount'     => $charge->amount,
            'charge_id'  => $charge->id,
        ]);

        return Response::redirect('/dashboard')
            ->with('success', 'Payment complete!');

    } catch (\Exception $e) {
        log_error('Payment failed', [
            'user_id' => auth()->id(),
            'error'   => $e->getMessage(),
        ]);

        return Response::redirect('/checkout')
            ->with('error', 'Payment could not be processed. Please try again.');
    }
}
```

### Log Configuration

Adjust the log channel and retention in `config/logging.php`:

```php
return [
    'default' => env('LOG_CHANNEL', 'daily'),

    'channels' => [
        'daily' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/app.log'),
            'days'   => 14,    // Keep logs for 14 days
            'level'  => env('LOG_LEVEL', 'debug'),
        ],
        'single' => [
            'driver' => 'single',
            'path'   => storage_path('logs/app.log'),
            'level'  => 'debug',
        ],
    ],
];
```

---

## 17. HTTP Client

The Veldora HTTP Client provides a clean, fluent interface for making outbound HTTP requests to external APIs and services. It is built on top of PHP's native cURL and streams, with support for JSON, authentication, retries, and fake responses in tests.

### Basic Requests

```php
use Veldora\Framework\Http\Client\Http;

// Simple GET request
$response = Http::get('https://api.github.com/users/veldorahq');

// GET with query parameters
$response = Http::get('https://api.example.com/posts', [
    'page'     => 1,
    'per_page' => 20,
    'status'   => 'published',
]);

// POST with JSON body (auto sets Content-Type: application/json)
$response = Http::post('https://api.example.com/users', [
    'name'  => 'John Doe',
    'email' => 'john@example.com',
]);

// PUT and DELETE
$response = Http::put('https://api.example.com/users/42', ['name' => 'Jane Doe']);
$response = Http::delete('https://api.example.com/users/42');
```

### Authentication

```php
// Bearer token (OAuth 2.0, JWT, API keys)
$response = Http::withToken('your-api-token-here')
    ->get('https://api.example.com/protected-resource');

// Basic HTTP authentication
$response = Http::withBasicAuth('username', 'password')
    ->get('https://api.example.com/data');

// Custom headers
$response = Http::withHeaders([
    'X-API-Key'    => config('services.stripe.key'),
    'X-Request-ID' => uniqid('req_'),
])->post('https://api.stripe.com/v1/charges', $payload);
```

### Working with Responses

```php
$response = Http::get('https://api.github.com/repos/veldorahq/veldora');

// Check status
$response->successful();      // true if status 200-299
$response->failed();          // true if status 400+
$response->status();          // integer: 200, 404, 500, etc.
$response->ok();              // true if status 200
$response->notFound();        // true if status 404
$response->serverError();     // true if status 500+

// Read body
$data   = $response->json();           // Decoded PHP array
$text   = $response->body();           // Raw string
$header = $response->header('X-RateLimit-Remaining');

// Fluent conditional
if ($response->successful()) {
    $repo = $response->json();
    log_info('Repo fetched', ['stars' => $repo['stargazers_count']]);
} else {
    log_error('GitHub API error', ['status' => $response->status()]);
}
```

### Request Options

```php
// Accept JSON responses (sets Accept: application/json header)
$response = Http::acceptJson()->get('https://api.example.com/data');

// Send as form (application/x-www-form-urlencoded)
$response = Http::asForm()->post('https://api.example.com/login', [
    'username' => 'admin',
    'password' => 'secret',
]);

// Set a timeout (in seconds)
$response = Http::timeout(30)->get('https://slow-api.example.com/data');

// Follow redirects (enabled by default; pass false to disable)
$response = Http::withoutRedirecting()->get('https://api.example.com');
```

### Retries & Fault Tolerance

```php
// Retry up to 3 times, waiting 500ms between attempts
$response = Http::retry(3, 500)->get('https://unstable-api.com/feed');

// Retry with exponential backoff
$response = Http::retry(5, 200)->post('https://api.example.com/data', $payload);
```

### Practical Example — Integrating a Payment Gateway

```php
namespace App\Services;

use Veldora\Framework\Http\Client\Http;

class StripeService
{
    public function createCharge(int $amountCents, string $cardToken): array
    {
        $response = Http::withBasicAuth(config('services.stripe.secret'), '')
            ->asForm()
            ->post('https://api.stripe.com/v1/charges', [
                'amount'      => $amountCents,
                'currency'    => 'usd',
                'source'      => $cardToken,
                'description' => 'Veldora order',
            ]);

        if ($response->failed()) {
            $error = $response->json()['error']['message'] ?? 'Unknown error';
            throw new \RuntimeException("Stripe charge failed: {$error}");
        }

        return $response->json();
    }
}
```

---

## 18. API JSON Resources

JSON Resources provide a dedicated transformation layer between your Eloquent models and the JSON responses your API returns. This lets you control exactly what fields are exposed, add computed properties, include related data, and maintain a consistent API contract without putting transformation logic inside your controllers.

### Why Use Resources?

Without resources, your controller might directly return model data:

```php
// ❌ Bad: exposes all fields including sensitive ones (password, email, etc.)
return Response::json($user->toArray());
```

With resources, you control the output explicitly:

```php
// ✅ Good: clean, controlled API response
return (new UserResource($user))->toResponse();
```

### Creating a Resource

```bash
php veldora make:resource PostResource
php veldora make:resource UserResource
```

### Defining a Resource

```php
// app/Http/Resources/PostResource.php
namespace App\Http\Resources;

use Veldora\Framework\Http\Resources\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'excerpt'      => substr($this->body, 0, 120) . '...',
            'published_at' => $this->published_at?->format('Y-m-d H:i:s'),
            'is_published' => (bool) $this->is_published,
            'author'       => [
                'id'     => $this->author->id,
                'name'   => $this->author->name,
                'avatar' => storage('public')->url($this->author->avatar_path ?? 'default.png'),
            ],
            'meta' => [
                'comment_count' => $this->comments()->count(),
            ],
        ];
    }
}
```

### Returning Single Resources

```php
// routes/web.php
$router->get('/api/posts/{id}', [PostController::class, 'show']);

// app/Controllers/PostController.php
public function show(string $id): Response
{
    $post = Post::find((int) $id);

    if (! $post) {
        return Response::json(['error' => 'Post not found'], 404);
    }

    return (new PostResource($post))->toResponse();
}
```

Response:
```json
{
  "id": 1,
  "title": "Hello Veldora",
  "slug": "hello-veldora",
  "excerpt": "Veldora is a modern PHP framework...",
  "published_at": "2026-08-23 14:00:00",
  "is_published": true,
  "author": { "id": 5, "name": "Jane Doe", "avatar": "/storage/avatars/user-5.png" },
  "meta": { "comment_count": 12 }
}
```

### Returning Resource Collections

```php
// Return all posts as a collection
public function index(Request $request): Response
{
    $posts = Post::where('is_published', '=', 1)
                 ->orderBy('created_at', 'DESC')
                 ->get();

    return PostResource::collection($posts)->toResponse();
}
```

### Returning Paginated Collections

Pass a paginator instead of a collection to automatically include `meta` and `links`:

```php
public function index(Request $request): Response
{
    $page = (int) $request->query('page', 1);
    $paginator = Post::where('is_published', '=', 1)->paginate(15, $page);

    return PostResource::collection($paginator)->toResponse();
}
```

Response:
```json
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 120,
    "last_page": 8
  },
  "links": {
    "first": "/api/posts?page=1",
    "last": "/api/posts?page=8",
    "prev": null,
    "next": "/api/posts?page=2"
  }
}
```

### Building a Complete REST API

```php
// routes/web.php — RESTful API routes
$router->group(['prefix' => '/api'], function ($r) {
    $r->get('/posts',        [PostController::class, 'index']);   // GET  /api/posts
    $r->post('/posts',       [PostController::class, 'store']);   // POST /api/posts
    $r->get('/posts/{id}',   [PostController::class, 'show']);    // GET  /api/posts/1
    $r->put('/posts/{id}',   [PostController::class, 'update']);  // PUT  /api/posts/1
    $r->delete('/posts/{id}',[PostController::class, 'destroy']); // DELETE /api/posts/1
});

// app/Controllers/PostController.php
class PostController
{
    public function index(Request $request): Response
    {
        $page  = (int) $request->query('page', 1);
        $posts = Post::where('is_published', '=', 1)->paginate(15, $page);
        return PostResource::collection($posts)->toResponse();
    }

    public function store(Request $request): Response
    {
        $data = $request->validated([
            'title' => 'required|min:3|max:255',
            'body'  => 'required|min:10',
        ]);

        $post = Post::create([
            ...$data,
            'user_id'      => auth()->id(),
            'slug'         => strtolower(str_replace(' ', '-', $data['title'])),
            'is_published' => 1,
        ]);

        return (new PostResource($post))->toResponse();
    }

    public function update(string $id, Request $request): Response
    {
        $post = Post::find((int) $id);

        if (! $post || $post->user_id !== auth()->id()) {
            return Response::json(['error' => 'Not found or unauthorized'], 403);
        }

        $data = $request->validated([
            'title' => 'required|min:3|max:255',
            'body'  => 'required|min:10',
        ]);

        $post->title = $data['title'];
        $post->body  = $data['body'];
        $post->save();

        return (new PostResource($post))->toResponse();
    }

    public function destroy(string $id): Response
    {
        $post = Post::find((int) $id);

        if (! $post || $post->user_id !== auth()->id()) {
            return Response::json(['error' => 'Not found or unauthorized'], 403);
        }

        $post->delete();

        return Response::json(['message' => 'Post deleted successfully']);
    }
}
```

---

## 19. Testing & Model Factories

Veldora includes an expressive testing framework built on top of PHPUnit. Write feature tests that simulate real HTTP requests, assert on responses, and verify database state — without needing a real browser.

### Setting Up Tests

Tests live in the `tests/` directory. Run the full test suite with:

```bash
php vendor/bin/phpunit
```

Or run a single test file:

```bash
php vendor/bin/phpunit tests/Feature/PostTest.php
```

### Writing HTTP Feature Tests

```php
namespace Tests\Feature;

use Veldora\Framework\Testing\TestCase;
use Database\Factories\UserFactory;
use Database\Factories\PostFactory;

class PostTest extends TestCase
{
    public function test_guests_cannot_create_posts(): void
    {
        $response = $this->post('/posts', ['title' => 'Test', 'body' => 'Body']);
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_create_post(): void
    {
        $user = (new UserFactory())->create();

        $response = $this->actingAs($user)->post('/posts', [
            'title' => 'My First Post',
            'body'  => 'This is the body content.',
        ]);

        $response->assertRedirect('/posts');
        $this->assertDatabaseHas('posts', ['title' => 'My First Post']);
    }

    public function test_user_can_view_their_posts(): void
    {
        $user = (new UserFactory())->create();
        $post = (new PostFactory())->create(['user_id' => $user->id, 'title' => 'My Post']);

        $response = $this->actingAs($user)->get('/posts');

        $response->assertOk();
        $response->assertSee('My Post');
    }

    public function test_user_cannot_delete_another_users_post(): void
    {
        $owner  = (new UserFactory())->create();
        $other  = (new UserFactory())->create();
        $post   = (new PostFactory())->create(['user_id' => $owner->id]);

        $response = $this->actingAs($other)->delete('/posts/' . $post->id);

        $response->assertStatus(403);
        $this->assertDatabaseHas('posts', ['id' => $post->id]); // Still exists
    }
}
```

### All Response Assertion Methods

```php
// HTTP status
$response->assertOk();              // status 200
$response->assertStatus(201);       // specific status code
$response->assertRedirect('/login'); // 302 redirect to URL
$response->assertNotFound();        // 404
$response->assertForbidden();       // 403

// Response body
$response->assertSee('Welcome');         // Contains string
$response->assertDontSee('Error');       // Does NOT contain string
$response->assertSeeText('Dashboard');   // Contains plain text

// JSON responses
$response->assertJson(['status' => 'ok']);          // JSON has these keys/values
$response->assertJsonCount(5, 'data');              // JSON array has N items
$response->assertJsonPath('data.0.title', 'Post 1'); // Specific JSON path value
```

### Database Assertions

```php
// Assert a record exists in the database
$this->assertDatabaseHas('posts', [
    'title'   => 'My Post',
    'user_id' => 42,
]);

// Assert a record does NOT exist in the database
$this->assertDatabaseMissing('posts', ['title' => 'Deleted Post']);

// Assert a record was soft-deleted (deleted_at is not null)
$this->assertSoftDeleted('posts', ['id' => 1]);
```

### Model Factories

Factories let you generate fake model instances for tests and seeders without writing manual SQL or repetitive `User::create(...)` calls.

Generate a factory:

```bash
php veldora make:factory PostFactory
```

Define the factory:

```php
// database/factories/PostFactory.php
namespace Database\Factories;

use App\Models\Post;
use Veldora\Framework\Database\Factory;

class PostFactory extends Factory
{
    protected string $model = Post::class;

    public function definition(): array
    {
        return [
            'user_id'      => (new UserFactory())->create()->id,
            'title'        => $this->faker->sentence(6),
            'slug'         => $this->faker->slug(),
            'body'         => $this->faker->paragraphs(3, true),
            'is_published' => 1,
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
        ];
    }
}
```

Use factories in tests:

```php
// Create a single instance and persist to database
$post = (new PostFactory())->create();

// Create with overridden attributes
$draftPost = (new PostFactory())->create(['is_published' => 0]);

// Create multiple instances
$posts = (new PostFactory())->count(5)->create();

// Make instance WITHOUT persisting (for unit tests)
$post = (new PostFactory())->make();
```

### Testing JSON API Endpoints

```php
class PostApiTest extends TestCase
{
    public function test_api_returns_paginated_posts(): void
    {
        (new PostFactory())->count(20)->create();

        $response = $this->get('/api/posts?page=1');

        $response->assertOk();
        $response->assertJson(['meta' => ['per_page' => 15]]);
        $response->assertJsonCount(15, 'data');
    }

    public function test_api_requires_auth_for_store(): void
    {
        $response = $this->post('/api/posts', [
            'title' => 'Test',
            'body'  => 'Body',
        ]);

        $response->assertStatus(401);
    }

    public function test_api_creates_post_for_authenticated_user(): void
    {
        $user = (new UserFactory())->create();

        $response = $this->actingAs($user)->post('/api/posts', [
            'title' => 'New API Post',
            'body'  => 'This is the post body content here.',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('title', 'New API Post');
        $this->assertDatabaseHas('posts', ['title' => 'New API Post', 'user_id' => $user->id]);
    }
}
```

### Running the Test Suite

```bash
# Run all tests
php vendor/bin/phpunit

# Run a single test file
php vendor/bin/phpunit tests/Feature/PostTest.php

# Run tests matching a filter
php vendor/bin/phpunit --filter test_guests_cannot_create_posts

# Run with verbose output
php vendor/bin/phpunit --testdox
```

---

## 20. Veldora UI (41+ Components)

Veldora UI provides a library of **41+ production-ready, accessible UI components** built specifically for Veldora's template engine. Unlike traditional CSS frameworks that bundle thousands of unused classes, Veldora UI lets you scaffold only the components you need directly into your `resources/views/components/` directory.

You own 100% of the component markup, styling, and logic.

### Installing Components

```bash
# List all 41+ available UI components
php veldora list:components

# Install specific components (space-separated)
php veldora add button card modal tabs alert badge

# Scaffold a custom component template
php veldora make:component user-card
```

---

### Component Catalogue by Category

Veldora UI organizes its 41 components into 6 logical categories:

#### 1. Forms & Inputs (9 Components)

| Component | Tag | Description |
|---|---|---|
| **Input** | `<x-input>` | Text, email, password, and number input fields with labels & error states |
| **Textarea** | `<x-textarea>` | Multi-line auto-resizing text input |
| **Select** | `<x-select>` | Accessible dropdown selection menu with optgroup support |
| **Checkbox** | `<x-checkbox>` | Custom styled boolean and array checkbox toggles |
| **Radio** | `<x-radio>` | Accessible single-choice radio button groups |
| **Input Group** | `<x-inputgroup>` | Inputs with prepended or appended icons, buttons, or currency tags |
| **File Upload** | `<x-fileupload>` | Drag-and-drop file upload zone with file size & format preview |
| **Date Picker** | `<x-datepicker>` | Modern calendar date and date-range selection picker |
| **Combobox** | `<x-combobox>` | Searchable autocomplete filter input with keyboard navigation |

#### 2. Actions & Controls (4 Components)

| Component | Tag | Description |
|---|---|---|
| **Button** | `<x-button>` | Versatile button with variants (`primary`, `secondary`, `danger`, `ghost`, `link`), sizes (`sm`, `md`, `lg`), and loading states |
| **Dropdown** | `<x-dropdown>` | Contextual menu popover triggered on click or hover with divider & item support |
| **Switch** | `<x-switch>` | Smooth toggle switch control for boolean settings |
| **DataTable** | `<x-datatable>` | Advanced data grid with client-side sorting, pagination, and search filtering |

#### 3. Feedback & Status (9 Components)

| Component | Tag | Description |
|---|---|---|
| **Alert** | `<x-alert>` | Banner feedback message (`info`, `success`, `warning`, `danger`) with optional close button |
| **Badge** | `<x-badge>` | Compact indicator pill for statuses, tags, counts, and categories |
| **Toast** | `<x-toast>` | Non-intrusive floating notification toast with timer auto-dismiss |
| **Spinner** | `<x-spinner>` | Smooth CSS loading animation in multiple sizes and colors |
| **Progress** | `<x-progress>` | Determinate or indeterminate animated progress bar |
| **Skeleton** | `<x-skeleton>` | Placeholder loading shimmer lines, circles, and blocks |
| **Empty State** | `<x-empty>` | Illustrated placeholder for empty datasets or zero-search results |
| **Confirm** | `<x-confirm>` | Action confirmation dialog for destructive actions (e.g. Delete) |
| **Rating** | `<x-rating>` | Interactive star/heart rating input and display |

#### 4. Data Display (8 Components)

| Component | Tag | Description |
|---|---|---|
| **Card** | `<x-card>` | Structured content container with header, body, footer, and hover effects |
| **Table** | `<x-table>` | Styled tabular data display with striped rows and sticky headers |
| **Stat** | `<x-stat>` | Metric KPI card with title, value, change percentage (+/-), and trend icon |
| **Timeline** | `<x-timeline>` | Vertical sequence of historical events, activities, or status steps |
| **Accordion** | `<x-accordion>` | Collapsible disclosure panels for FAQs and multi-section content |
| **Avatar** | `<x-avatar>` | User profile image with fallback initials and online indicator dot |
| **Tooltip** | `<x-tooltip>` | Hover and focus tooltip popup with positional arrows |
| **Tabs** | `<x-tabs>` | Tabbed navigation switcher with smooth active pill animations |

#### 5. Navigation (5 Components)

| Component | Tag | Description |
|---|---|---|
| **Navbar** | `<x-navbar>` | Responsive top navigation header with logo, links, search, and mobile menu toggle |
| **Breadcrumb** | `<x-breadcrumb>` | Hierarchical path breadcrumbs for deep page navigation |
| **Pagination** | `<x-pagination>` | Page number switcher with Next, Previous, and active state indicators |
| **Stepper** | `<x-stepper>` | Multi-step form or onboarding wizard progress indicator |
| **Sidebar** | `<x-sidebar>` | Collapsible dashboard sidebar navigation with grouped link sections |

#### 6. Layout & Overlay (6 Components)

| Component | Tag | Description |
|---|---|---|
| **Modal** | `<x-modal>` | Accessible dialog overlay with backdrop blur, focus trap, and header/body/footer slots |
| **Drawer** | `<x-drawer>` | Off-canvas slide-out sheet from left, right, top, or bottom |
| **Popover** | `<x-popover>` | Floating rich-content popover anchored to a trigger element |
| **Divider** | `<x-divider>` | Horizontal or vertical separator with optional middle label |
| **Container** | `<x-container>` | Responsive max-width wrapper with consistent horizontal padding |
| **Footer** | `<x-footer>` | Multi-column site footer with copyright notice and social links |

---

### Component Code Examples

#### Button Component
```html
<x-button variant="primary" size="md">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    Save Changes
</x-button>

<x-button variant="danger" size="sm" onclick="confirmDelete()">Delete Post</x-button>
<x-button variant="ghost" size="md">Cancel</x-button>
```

#### Modal Dialog
```html
<x-modal id="create-user-modal" title="Add New Team Member">
    <form method="POST" action="/users" id="user-form">
        @csrf
        <x-input name="name" label="Full Name" placeholder="John Doe" required />
        <x-input name="email" label="Email Address" type="email" placeholder="john@example.com" required />
    </form>

    <x-slot name="footer">
        <x-button variant="ghost" onclick="VeldoraUI.closeModal('create-user-modal')">Cancel</x-button>
        <x-button variant="primary" type="submit" form="user-form">Create Account</x-button>
    </x-slot>
</x-modal>
```

#### Card & Metric Stat
```html
<x-card title="Monthly Performance" subtitle="Updated 5 minutes ago">
    <div class="grid-3">
        <x-stat title="Total Revenue" value="$48,250" change="+12.5%" trend="up" />
        <x-stat title="Active Users" value="1,420" change="+8.1%" trend="up" />
        <x-stat title="Bounce Rate" value="24.2%" change="-3.4%" trend="down" />
    </div>
</x-card>
```

#### Tabs Navigation
```html
<x-tabs :tabs="['account' => 'Account Info', 'security' => 'Security & Passwords', 'billing' => 'Billing']" active="account">
    <div id="tab-account">
        <!-- Account Settings Content -->
    </div>
    <div id="tab-security">
        <!-- Password and 2FA Settings -->
    </div>
    <div id="tab-billing">
        <!-- Invoices and Subscription Plans -->
    </div>
</x-tabs>
```


---

## 21. VS Code Extension

The official **Veldora VS Code Extension** provides syntax highlighting, autocomplete, and 32 snippets for `.veldora.php` files.

### Installation

Install directly from the VS Code Marketplace:

```bash
code --install-extension veldora.veldora-vscode
```

### Popular Snippets

| Prefix | Expands To |
|---|---|
| `v-if` | `&#64;if(...) ... &#64;endif` |
| `v-foreach` | `&#64;foreach(...) ... &#64;endforeach` |
| `v-forelse` | `&#64;forelse(...) ... &#64;empty ... &#64;endforelse` |
| `v-extends` | `&#64;extends('layouts.app')` |
| `v-section` | `&#64;section('name') ... &#64;endsection` |
| `v-yield` | `&#64;yield('name')` |
| `v-auth` | `&#64;auth ... &#64;endauth` |
| `v-guest` | `&#64;guest ... &#64;endguest` |
| `v-comp` | `<x-component-name>...</x-component-name>` |

---

## 22. AI Context Prompt & AI Skills

Because **Veldora** is a modern independent PHP framework, general AI models (ChatGPT, Claude, Gemini, Cursor, Copilot, Antigravity) do not have it in their pre-training data.

Use this **Complete Veldora AI Master Prompt** to teach any AI model the full framework architecture, CLI commands, routing, ORM, templates, and conventions.

### How to Use

1. Click **Copy Full Master Prompt** or **Download Prompt** below.
2. Paste it as the first message or System Prompt in your AI assistant conversation (ChatGPT, Claude, Cursor `.cursorrules`, Antigravity, or Copilot).
3. The AI will immediately understand all Veldora APIs and write 100% correct, runnable Veldora code.

```
You are an expert software engineer specialized in the Veldora PHP Framework (v0.6.0).
Veldora is a modern, independent, lightweight PHP 8.2+ MVC framework designed for maximum performance, clean developer ergonomics, zero boilerplate magic, and complete developer ownership.

================================================================================
1. VELDORA CORE ARCHITECTURE & PHILOSOPHY
================================================================================
- Language: PHP 8.2 or higher (uses strict typing, readonly properties, constructor promotion).
- Architecture: Classic MVC (Model-View-Controller) with IoC Container, PSR-4 Autoloading, Middleware Pipeline, ActiveRecord ORM, and Native Blade-inspired Template Engine.
- Key Principle: Zero unnecessary runtime magic. Everything is type-hinted, explicit, and IDE-friendly. You own the components and code in your application directory.

Directory Layout:
  app/
    Controllers/       -> HTTP Request handlers (methods receive Request $request, return Response)
    Http/
      Middleware/      -> HTTP filters (Auth, CSRF, Admin, StartSession, Throttle, etc.)
      Requests/        -> Validated Form Request classes (extend Veldora\Framework\Http\FormRequest)
      Resources/       -> API JSON transformers (extend Veldora\Framework\Http\Resources\JsonResource)
    Models/            -> ActiveRecord database entities (extend Veldora\Framework\Database\Model)
    Observers/         -> Model Lifecycle Observers (creating, updating, deleting, etc.)
    Policies/          -> Authorization Policy classes (extend or define policy gates)
    Rules/             -> Custom Validation Rules (implement Veldora\Framework\Validation\Rule)
    Services/          -> Business logic & third-party service integrations
    Events/            -> Event classes (extend Veldora\Framework\Events\Event)
    Listeners/         -> Event listeners (implement Veldora\Framework\Events\Listener)
    Jobs/              -> Background queue jobs (extend Veldora\Framework\Queue\Job)
    Mail/              -> Mailable email classes (extend Veldora\Framework\Mail\Mailable)
  bootstrap/
    app.php            -> Application bootstrapper & container bindings
  config/              -> app.php, auth.php, database.php, mail.php, queue.php, cache.php, session.php
  database/
    factories/         -> Model factories for testing & seeding
    migrations/        -> Versioned database migrations
    seeders/           -> Database seeders
  public/
    index.php          -> Single entry point for all HTTP traffic
    css/               -> Application and UI component styles
    storage/           -> Public storage symlink (via php veldora storage:link)
  resources/
    views/             -> .veldora.php view templates
      components/      -> 41+ UI components (<x-button>, <x-modal>, <x-tabs>, etc.)
      layouts/         -> Base application layouts (@extends, @yield)
  routes/
    web.php            -> Application route definitions
  storage/
    app/               -> Private and public file storage
    framework/         -> Compiled views, file cache, sessions
    logs/              -> Daily rotating log files (app.log)
  .env                 -> Environment variables (loaded automatically before config)
  veldora              -> Framework CLI binary (php veldora <command>)

================================================================================
2. INSTALLATION & SETUP
================================================================================
Option A (Interactive Scaffolder via npx / npm):
  npx create-veldora-app my-app
  cd my-app
  php veldora serve

Option B (Composer):
  composer create-project veldora/veldora-starter my-app
  cd my-app
  cp .env.example .env
  php veldora serve

Development Server:
  php veldora serve --port=8000 --host=127.0.0.1

================================================================================
3. COMPLETE 51 CLI COMMANDS REFERENCE (php veldora ...)
================================================================================
Application & Diagnostics:
  php veldora serve                     -> Start local development server with real-time request logs
  php veldora about                     -> Display framework environment, PHP version, DB driver, cache
  php veldora doctor                    -> Run system health diagnostics and verify PHP extensions
  php veldora key:generate              -> Generate a cryptographically secure 32-character APP_KEY in .env
  php veldora storage:link              -> Create symlink from public/storage to storage/app/public
  php veldora down [--secret=...]       -> Put application into maintenance mode (with secret bypass)
  php veldora up                        -> Bring application out of maintenance mode
  php veldora env                       -> Display current application environment name
  php veldora env:encrypt [--key=...]   -> Encrypt .env file with AES-256 for safe version control
  php veldora env:decrypt [--key=...]   -> Decrypt .env.encrypted file using APP_KEY

Code Generators (make:*):
  php veldora make:controller <Name>    -> Scaffold a new HTTP Controller in app/Controllers/
  php veldora make:model <Name> [-m]    -> Scaffold an ActiveRecord Model (-m creates migration)
  php veldora make:migration <name>     -> Create a database migration in database/migrations/
  php veldora make:middleware <Name>    -> Scaffold an HTTP Middleware in app/Http/Middleware/
  php veldora make:policy <Name>        -> Scaffold an authorization Policy in app/Policies/ (--model=Post)
  php veldora make:observer <Name>      -> Scaffold a Model Observer in app/Observers/ (--model=User)
  php veldora make:rule <Name>          -> Scaffold a custom Validation Rule in app/Rules/
  php veldora make:request <Name>       -> Create a Form Request validator in app/Http/Requests/
  php veldora make:resource <Name>      -> Create an API JSON Resource in app/Http/Resources/
  php veldora make:job <Name>           -> Scaffold a background Queue Job in app/Jobs/
  php veldora make:event <Name>         -> Scaffold an Event class in app/Events/
  php veldora make:listener <Name>      -> Scaffold an Event Listener in app/Listeners/
  php veldora make:mail <Name>          -> Scaffold a Mailable email class in app/Mail/
  php veldora make:seeder <Name>        -> Create a Database Seeder in database/seeders/
  php veldora make:factory <Name>       -> Create a Model Factory in database/factories/
  php veldora make:command <Name>       -> Create a custom Veldora Console Command
  php veldora make:component <name>     -> Scaffold a UI component template in resources/views/components/
  php veldora make:auth                 -> Scaffold full authentication (Login, Register, Forgot/Reset Password, Views)

Database Management:
  php veldora migrate                   -> Run pending database migrations
  php veldora migrate:rollback          -> Rollback the last migration batch (--step=N)
  php veldora migrate:fresh             -> Drop all tables and re-run all migrations (--seed)
  php veldora migrate:status            -> Show status and batch history of all migrations
  php veldora db:seed                   -> Run database seeders (--class=UserSeeder)
  php veldora db:wipe                   -> Drop all tables, views, and types in the database
  php veldora db:show                   -> Display database schema summary, table list, row counts

Routing & Cache:
  php veldora route:list                -> Display formatted table of all registered routes
  php veldora route:cache               -> Compile and cache routes for production
  php veldora route:clear               -> Clear route cache
  php veldora config:cache              -> Compile config into a single cached file
  php veldora config:clear              -> Clear configuration cache
  php veldora config:show [key]         -> Display configuration values for a key
  php veldora view:cache                -> Precompile all .veldora.php templates
  php veldora view:clear                -> Clear compiled template cache
  php veldora cache:clear               -> Flush application and session cache
  php veldora optimize                  -> Run all cache optimizers for production
  php veldora optimize:clear            -> Clear all framework caches at once

Queue Processing:
  php veldora queue:work                -> Start background queue worker daemon (--queue=default --sleep=3)
  php veldora queue:failed              -> Display list of failed queue jobs
  php veldora queue:retry <id|all>      -> Retry failed job(s)
  php veldora queue:clear               -> Delete all pending jobs from queue

UI Components:
  php veldora list:components           -> List all 41+ available UI components
  php veldora add <components...>       -> Scaffold components into resources/views/components/

================================================================================
4. ROUTING & HTTP LAYER
================================================================================
In routes/web.php ($router is automatically injected):
  $router->get('/', [HomeController::class, 'index']);
  $router->get('/posts/{slug}', [PostController::class, 'show']);
  $router->post('/posts', [PostController::class, 'store'])->middleware(['auth']);
  $router->put('/posts/{id}', [PostController::class, 'update'])->middleware(['auth']);
  $router->delete('/posts/{id}', [PostController::class, 'destroy'])->middleware(['auth']);

  // Route Groups:
  $router->group(['prefix' => '/admin', 'middleware' => ['auth', 'admin']], function ($r) {
      $r->get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
      $r->get('/users', [AdminController::class, 'users'])->name('admin.users');
  });

Controller Convention:
  namespace App\Controllers;

  use Veldora\Framework\Http\Request;
  use Veldora\Framework\Http\Response;
  use Veldora\Framework\View\Engine;
  use App\Models\Post;

  class PostController
  {
      public function __construct(protected Engine $view) {}

      public function index(Request $request): Response
      {
          $posts = Post::where('is_published', '=', 1)->orderBy('created_at', 'DESC')->paginate(10);
          return view('posts.index', ['posts' => $posts]);
      }

      public function show(string $slug): Response
      {
          $post = Post::where('slug', '=', $slug)->first();
          if (!$post) {
              abort(404, 'Post not found');
          }
          return view('posts.show', ['post' => $post]);
      }

      public function store(Request $request): Response
      {
          $data = $request->validated([
              'title' => 'required|min:3|max:200',
              'body'  => 'required',
          ]);
          $data['user_id'] = auth()->id();
          $post = Post::create($data);

          return redirect('/posts/' . $post->id)->with('success', 'Post published successfully!');
      }

      public function api(): Response
      {
          return response()->json(['status' => 'ok', 'data' => Post::all()], 200);
      }
  }

================================================================================
5. TEMPLATES (.veldora.php)
================================================================================
- Directives:
  @extends('layouts.app')
  @section('title', 'Page Title')
  @section('content') ... @endsection
  @yield('title', 'Default Title')
  @yield('content')
  @include('partials.nav', ['active' => 'home'])
  @csrf                              -> Injects hidden CSRF token input
  @method('PUT')                     -> Spoofs PUT/PATCH/DELETE HTTP method
  @if($condition) ... @elseif(...) ... @else ... @endif
  @foreach($items as $item) ... @endforeach
  @forelse($items as $item) ... @empty ... @endforelse
  @auth ... @endauth                 -> Display only if authenticated
  @guest ... @endguest               -> Display only if guest
  @error('field') ... @enderror      -> Validation error block
- Variable Output:
  {{ $variable }}                    -> Automatically escaped via htmlspecialchars
  {!! $rawHtml !!}                   -> Raw unescaped HTML output
- UI Component Syntax:
  <x-button variant="primary" size="md">Save Changes</x-button>
  <x-modal id="my-modal" title="Confirm Action">
      <p>Modal body content</p>
      <x-slot name="footer">
          <x-button variant="danger">Confirm</x-button>
      </x-slot>
  </x-modal>

================================================================================
6. ACTIVRECORD MODELS, RELATIONSHIPS & MIGRATIONS
================================================================================
Model Class:
  namespace App\Models;

  use Veldora\Framework\Database\Model;
  use Veldora\Framework\Database\SoftDeletes;
  use Veldora\Framework\Database\Relations\BelongsTo;
  use Veldora\Framework\Database\Relations\HasMany;
  use Veldora\Framework\Database\Relations\BelongsToMany;

  class Post extends Model
  {
      use SoftDeletes;

      protected ?string $table = 'posts';
      protected array $fillable = ['title', 'slug', 'body', 'user_id', 'is_published'];
      protected array $casts = ['is_published' => 'bool', 'published_at' => 'datetime'];
      protected array $hidden = ['deleted_at'];

      public function author(): BelongsTo
      {
          return $this->belongsTo(User::class, 'user_id');
      }

      public function comments(): HasMany
      {
          return $this->hasMany(Comment::class, 'post_id');
      }
  }

CRUD & Query Builder:
  $post = Post::create(['title' => 'My Title', 'body' => 'Content']);
  $post = Post::find(1);
  $posts = Post::where('is_published', '=', 1)->orderBy('created_at', 'DESC')->get();
  $post = Post::where('slug', '=', 'my-slug')->first();
  $paginator = Post::paginate(15);
  $post->update(['title' => 'Updated']);
  $post->delete();

Model Observers & Lifecycle:
  User::observe(UserObserver::class);   -> Auto-wires creating, updating, saving, deleting hooks
  // Returning false in creating/updating/saving/deleting cancels the database operation

Migrations:
  use Veldora\Framework\Database\Schema\Blueprint;
  use Veldora\Framework\Database\Schema\Migration;
  use Veldora\Framework\Database\Schema\Schema;

  return new class extends Migration {
      public function up(): void {
          Schema::create('posts', function (Blueprint $table) {
              $table->id();
              $table->integer('user_id');
              $table->string('title');
              $table->string('slug')->unique();
              $table->text('body');
              $table->boolean('is_published')->default(0);
              $table->timestamps();
              $table->softDeletes();
          });
      }

      public function down(): void {
          Schema::dropIfExists('posts');
      }
  };

================================================================================
7. AUTHENTICATION & SECURITY
================================================================================
Auth Helpers:
  auth()->check()        -> bool (true if user logged in)
  auth()->user()         -> ?User (current user model)
  auth()->id()           -> ?int (current user ID)
  auth()->login($user)   -> Log in a User instance
  auth()->logout()       -> Log out and flush session
  auth()->attempt(['email' => $e, 'password' => $p]) -> bool

Built-in Middleware:
  'auth'                 -> Requires active login (redirects to /login)
  'guest'                -> Requires guest (redirects to / if logged in)
  'admin'                -> Requires user->is_admin == 1
  'csrf'                 -> Verifies CSRF token on POST/PUT/DELETE
  'start_session'        -> Initializes session cookies

================================================================================
8. SUBSYSTEMS & GLOBAL HELPERS
================================================================================
Global Helpers:
  app(Class::class)      -> Resolve dependency from container
  config('app.name')     -> Read configuration value
  env('KEY', 'default')  -> Read environment variable
  view('name', $data)    -> Render a view response
  response($body, $code) -> Create HTTP response
  redirect($url)         -> Create redirect response
  session('key')         -> Read or write session value
  csrf_token()           -> Get current CSRF token string
  old('name', 'default') -> Retrieve flashed form input
  route('name', $params) -> Generate URL for named route

Queues & Background Jobs:
  SendInvoiceJob::dispatch($order)->onQueue('default')->delay(60);
  php veldora queue:work

Mail:
  mailer($user->email)->send(new WelcomeEmail($user));

Events:
  OrderPlaced::dispatch($order);
  event(new OrderPlaced($order));

Cache:
  cache()->remember('stats', 3600, fn() => DB::table('orders')->count());
  cache()->put('key', $val, 3600);
  $val = cache('key');
  cache()->forget('key');

File Storage:
  storage('public')->put('uploads/doc.pdf', $fileContents);
  $url = storage('public')->url('uploads/doc.pdf');

PSR-3 Logging:
  log_info('Payment processed', ['amount' => 100]);
  log_error('Exception caught', ['exception' => $e]);

HTTP Client:
  $res = Http::withHeaders(['X-Key' => 'secret'])->get('https://api.example.com/data');
  $data = $res->json();

================================================================================
9. 41+ VELDORA UI COMPONENTS
================================================================================
Forms & Inputs (9):      input, textarea, select, checkbox, radio, inputgroup, fileupload, datepicker, combobox
Actions & Controls (4):  button, dropdown, switch, datatable
Feedback & Status (9):   alert, badge, toast, spinner, progress, skeleton, empty, confirm, rating
Data Display (8):        card, table, stat, timeline, accordion, avatar, tooltip, tabs
Navigation (5):          navbar, breadcrumb, pagination, stepper, sidebar
Layout & Overlay (6):    modal, drawer, popover, divider, container, footer

Install via CLI:
  php veldora add button card modal tabs alert badge

================================================================================
10. STRICT AI CODE GENERATION RULES
================================================================================
1. Always declare declare(strict_types=1); at the top of every PHP file.
2. Use exact Veldora namespaces:
   - Veldora\Framework\Http\Request
   - Veldora\Framework\Http\Response
   - Veldora\Framework\Database\Model
   - Veldora\Framework\Database\Schema\Schema
   - Veldora\Framework\Database\Schema\Blueprint
   - Veldora\Framework\Database\Schema\Migration
3. In views (.veldora.php), ALWAYS use native Veldora directives (@csrf, @method('PUT'), @if, @foreach, {{ $var }}) and <x-component> tags.
4. Never generate incomplete placeholders or pseudo-code; always write production-ready, fully functional Veldora code.
```

---

## 23. Model Observers & Lifecycle Hooks

Model Observers group lifecycle event listeners for an ActiveRecord model into a dedicated class. Instead of scattering event callbacks across models or controllers, observers encapsulate model-driven business logic such as sending welcome emails, dispatching jobs, clearing caches, or generating slugs.

### Generating an Observer

Use the CLI generator to scaffold an observer class:

```bash
php veldora make:observer UserObserver --model=User
```

This creates `app/Observers/UserObserver.php`:

```php
namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function creating(User $user): void
    {
        // Executed before a new record is inserted
    }

    public function created(User $user): void
    {
        // Executed after a new record is inserted
    }

    public function updating(User $user): void
    {
        // Executed before an existing record is updated
    }

    public function updated(User $user): void
    {
        // Executed after an existing record is updated
    }

    public function saving(User $user): void
    {
        // Executed before insert or update
    }

    public function saved(User $user): void
    {
        // Executed after insert or update
    }

    public function deleting(User $user): void
    {
        // Executed before record is deleted
    }

    public function deleted(User $user): void
    {
        // Executed after record is deleted
    }

    public function restoring(User $user): void
    {
        // Executed before a soft-deleted record is restored
    }

    public function restored(User $user): void
    {
        // Executed after a soft-deleted record is restored
    }

    public function forceDeleted(User $user): void
    {
        // Executed after record is permanently deleted
    }
}
```

### Registering Observers

Register an observer using `Model::observe()`, typically in your application bootstrap (`bootstrap/app.php`) or a Service Provider:

```php
use App\Models\User;
use App\Observers\UserObserver;

// Pass class-string (instantiated automatically)
User::observe(UserObserver::class);

// Or pass an existing instance
User::observe(new UserObserver());
```

### Supported Lifecycle Events

| Event Hook | When It Fires |
|---|---|
| `creating` | Before a new record is inserted into the database |
| `created` | After a new record has been inserted |
| `updating` | Before an existing record is updated |
| `updated` | After an existing record has been updated |
| `saving` | Before a record is saved (both new and updated) |
| `saved` | After a record has been saved (both new and updated) |
| `deleting` | Before a record is deleted from the database |
| `deleted` | After a record has been deleted |
| `restoring` | Before a soft-deleted record is restored |
| `restored` | After a soft-deleted record has been restored |
| `forceDeleted` | After a record is permanently removed from the database |

> Returning `false` from a `creating`, `updating`, `saving`, or `deleting` observer method will cancel the database operation immediately.


---

## 24. Veldora Connect — Integrations Ecosystem

**Veldora Connect** is the official first-party integrations monorepo for the Veldora Framework. It provides battle-tested, production-ready connectors for essential third-party services, starting with payment gateways.

> Install only what you need — each integration is an independent Composer package.

### Available Packages

| Package | Status | Description |
|---|---|---|
| `veldora/connect-stripe` | **Active (v0.7.0)** | Stripe payment gateway — Checkout, PaymentIntents, Customers, Webhooks |
| `veldora/connect-sslcommerz` | *Upcoming* | SSLCommerz payment gateway for Bangladesh |
| `veldora/connect-resend` | *Upcoming* | Resend transactional email service |
| `veldora/connect-s3` | *Upcoming* | AWS S3 / S3-compatible cloud storage |
| `veldora/connect-sentry` | *Upcoming* | Sentry crash reporting & telemetry |

---

### Stripe Integration (`veldora/connect-stripe`)

#### Installation

```bash
composer require veldora/connect-stripe
```

#### Environment Configuration

Add your Stripe API keys to `.env`:

```ini
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
STRIPE_CURRENCY=usd
```

#### Service Provider (if not auto-discovered)

```php
// config/app.php
'providers' => [
    // ...
    Veldora\Connect\Stripe\StripeServiceProvider::class,
],
```

#### Checkout Session

Create a Stripe Checkout session and redirect the user:

```php
use Veldora\Connect\Stripe\Facades\Stripe;

Route::post('/checkout', function () {
    $session = Stripe::checkout()->create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency'     => 'usd',
                'product_data' => ['name' => 'Veldora Pro'],
                'unit_amount'  => 2000,
            ],
            'quantity' => 1,
        ]],
        'mode'        => 'payment',
        'success_url' => url('/success?session_id={CHECKOUT_SESSION_ID}'),
        'cancel_url'  => url('/cancel'),
    ]);

    return redirect($session->url);
});
```

#### PaymentIntents

```php
use Veldora\Connect\Stripe\Facades\Stripe;

// Create a PaymentIntent
$intent = Stripe::paymentIntents()->create([
    'amount'   => 2000,
    'currency' => 'usd',
]);

// Confirm a PaymentIntent
$confirmed = Stripe::paymentIntents()->confirm($intent->id, [
    'payment_method' => 'pm_card_visa',
]);
```

#### Customer Management

```php
use Veldora\Connect\Stripe\Facades\Stripe;

// Create
$customer = Stripe::customers()->create([
    'email' => 'user@example.com',
    'name'  => 'John Doe',
]);

// Retrieve
$customer = Stripe::customers()->retrieve($customer->id);

// Update
Stripe::customers()->update($customer->id, ['name' => 'Jane Doe']);

// Delete
Stripe::customers()->delete($customer->id);
```

#### Webhook Handling

Verify and process incoming Stripe webhook events:

```php
use Veldora\Connect\Stripe\Facades\Stripe;
use Veldora\Framework\Http\Request;
use Veldora\Framework\Http\Response;

Route::post('/webhook/stripe', function (Request $request) {
    $payload   = $request->getContent();
    $sigHeader = $request->header('stripe-signature');

    try {
        $event = Stripe::webhook()->constructEvent(
            $payload,
            $sigHeader,
            config('stripe.webhook_secret')
        );

        match ($event->type) {
            'checkout.session.completed' => handleCheckout($event->data->object),
            'payment_intent.succeeded'   => handlePayment($event->data->object),
            default                      => null,
        };

        return Response::json(['status' => 'success']);
    } catch (\Exception $e) {
        return Response::json(['error' => $e->getMessage()], 400);
    }
});
```

#### Facade Reference

| Method | Description |
|---|---|
| `Stripe::checkout()->create($params)` | Create a Checkout Session |
| `Stripe::checkout()->retrieve($id)` | Retrieve a Checkout Session |
| `Stripe::paymentIntents()->create($params)` | Create a PaymentIntent |
| `Stripe::paymentIntents()->confirm($id, $params)` | Confirm a PaymentIntent |
| `Stripe::paymentIntents()->capture($id)` | Capture a PaymentIntent |
| `Stripe::customers()->create($params)` | Create a Customer |
| `Stripe::customers()->retrieve($id)` | Retrieve a Customer |
| `Stripe::customers()->update($id, $params)` | Update a Customer |
| `Stripe::customers()->delete($id)` | Delete a Customer |
| `Stripe::webhook()->constructEvent($payload, $sig, $secret)` | Verify & parse a webhook |

> [!NOTE]
> All Connect packages use the Veldora service container. The `stripe` binding and `StripeClient::class` binding are both registered automatically by `StripeServiceProvider`.

> [!TIP]
> Connect packages fire Veldora events on webhook dispatch: `StripeWebhookHandled` (success) and `StripeWebhookFailed` (on exception). Listen to them via the standard Veldora event system.

---

### Connect Repository

Source code and full monorepo: [github.com/veldorahq/connect](https://github.com/veldorahq/connect)

