<?php

use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AuditTrailController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

Route::get('/', function () {
    $products = Product::query()
        ->active()
        ->orderByRaw("CASE collection WHEN 'women' THEN 1 WHEN 'men' THEN 2 WHEN 'unisex' THEN 3 ELSE 4 END")
        ->orderBy('name')
        ->get()
        ->groupBy('collection');

    return view('landingpage', [
        'productsByCollection' => $products,
    ]);
});

Route::get('/shopping-cart', function () {
    return view('shoppingcart');
})->name('shopping-cart');

Route::get('/customer/profile', function (Request $request) {
    $user = $request->user();

    return response()->json([
        'authenticated' => $user !== null,
        'profile' => $user ? [
            'surname' => $user->surname,
            'firstName' => $user->first_name ?: $user->name,
            'contact' => $user->contact_number,
            'email' => $user->email,
            'region' => $user->region,
            'city' => $user->city,
            'barangay' => $user->barangay,
            'street' => $user->street,
            'landmark' => $user->landmark,
        ] : null,
    ]);
})->name('customer.profile');

Route::get('/customer/csrf-token', function () {
    return response()->json([
        'token' => csrf_token(),
    ]);
})->name('customer.csrf-token');

Route::post('/customer/register', function (Request $request) {
    $validated = $request->validate([
        'surname' => ['required', 'string', 'max:120'],
        'firstName' => ['required', 'string', 'max:120'],
        'contact' => ['required', 'string', 'max:40'],
        'email' => ['required', 'email', 'max:160', 'unique:users,email'],
        'password' => ['required', 'string', 'min:8'],
        'region' => ['nullable', 'string', 'max:120'],
        'city' => ['nullable', 'string', 'max:120'],
        'barangay' => ['nullable', 'string', 'max:120'],
        'street' => ['nullable', 'string', 'max:180'],
        'landmark' => ['nullable', 'string', 'max:180'],
    ]);

    $user = User::create([
        'name' => $validated['firstName'].' '.$validated['surname'],
        'surname' => $validated['surname'],
        'first_name' => $validated['firstName'],
        'email' => $validated['email'],
        'contact_number' => $validated['contact'],
        'region' => $validated['region'] ?? null,
        'city' => $validated['city'] ?? null,
        'barangay' => $validated['barangay'] ?? null,
        'street' => $validated['street'] ?? null,
        'landmark' => $validated['landmark'] ?? null,
        'password' => $validated['password'],
    ]);

    Auth::login($user);
    $request->session()->regenerate();

    return response()->json(['message' => 'Registered and logged in.']);
})->name('customer.register');

Route::post('/customer/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    if (! Auth::attempt($credentials, true)) {
        throw ValidationException::withMessages([
            'email' => 'The email or password is incorrect.',
        ]);
    }

    $request->session()->regenerate();

    return response()->json(['message' => 'Logged in.']);
})->name('customer.login');

Route::post('/customer/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json(['message' => 'Logged out.']);
})->name('customer.logout');

Route::get('/customer/orders', function (Request $request) {
    $user = $request->user();

    $orders = Order::query()
        ->where('user_id', $user->id)
        ->orWhere('delivery_address', 'like', '%Email: '.$user->email.'%')
        ->latest()
        ->paginate(12);

    return view('customer.orders', [
        'orders' => $orders,
        'user' => $user,
    ]);
})->middleware('auth')->name('customer.orders');

Route::get('/customer/orders/{reference}', function (Request $request, string $reference) {
    $user = $request->user();
    $reference = strtoupper($reference);

    $order = Order::query()
        ->where(function ($query) use ($user) {
            $query
                ->where('user_id', $user->id)
                ->orWhere('delivery_address', 'like', '%Email: '.$user->email.'%');
        })
        ->latest()
        ->get()
        ->first(fn (Order $order) => hash_equals($order->order_reference, $reference));

    abort_unless($order, 404);

    return view('customer.order-show', [
        'order' => $order,
        'user' => $user,
    ]);
})->middleware('auth')->name('customer.orders.show');

Route::get('/customer/orders/{reference}/tracking', function (Request $request, string $reference) {
    $user = $request->user();
    $reference = strtoupper($reference);

    $order = Order::query()
        ->where(function ($query) use ($user) {
            $query
                ->where('user_id', $user->id)
                ->orWhere('delivery_address', 'like', '%Email: '.$user->email.'%');
        })
        ->latest()
        ->get()
        ->first(fn (Order $order) => hash_equals($order->order_reference, $reference));

    abort_unless($order, 404);

    return response()->json([
        'reference' => $order->order_reference,
        'status' => $order->status,
        'statusLabel' => ucfirst($order->status),
        'trackingNumber' => $order->tracking_number ?: 'Awaiting tracking',
        'updatedAt' => $order->updated_at->format('F d, Y h:i A'),
    ]);
})->middleware('auth')->name('customer.orders.tracking');

Route::post('/orders', function (Request $request) {
    if (! $request->user() || $request->user()->is_admin) {
        return response()->json([
            'message' => 'Please log in before placing your order.',
        ], 401);
    }

    $validated = $request->validate([
        'surname' => ['required', 'string', 'max:120'],
        'firstName' => ['required', 'string', 'max:120'],
        'contact' => ['required', 'string', 'max:40'],
        'email' => [
            'required',
            'email',
            'max:160',
            ...($request->user()
                ? [Rule::unique('users', 'email')->ignore($request->user()->id)]
                : []),
        ],
        'region' => ['required', 'string', 'max:120'],
        'city' => ['required', 'string', 'max:120'],
        'barangay' => ['required', 'string', 'max:120'],
        'street' => ['required', 'string', 'max:180'],
        'landmark' => ['nullable', 'string', 'max:180'],
        'items' => ['required', 'array', 'min:1'],
        'items.*.productId' => ['nullable', 'integer', 'exists:products,id'],
        'items.*.name' => ['required', 'string', 'max:160'],
        'items.*.category' => ['nullable', 'string', 'max:80'],
        'items.*.scent' => ['nullable', 'string', 'max:160'],
        'items.*.size' => ['required', 'integer', Rule::in(Product::SIZES)],
        'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
    ]);

    $address = collect([
        $validated['street'],
        $validated['barangay'],
        $validated['city'],
        $validated['region'],
        $validated['landmark'] ? 'Landmark: '.$validated['landmark'] : null,
        'Email: '.$validated['email'],
    ])->filter()->implode(', ');

    $request->user()->update([
        'name' => $validated['firstName'].' '.$validated['surname'],
        'surname' => $validated['surname'],
        'first_name' => $validated['firstName'],
        'email' => $validated['email'],
        'contact_number' => $validated['contact'],
        'region' => $validated['region'],
        'city' => $validated['city'],
        'barangay' => $validated['barangay'],
        'street' => $validated['street'],
        'landmark' => $validated['landmark'] ?? null,
    ]);

    $shippingFee = 32;
    $order = DB::transaction(function () use ($request, $validated, $address, $shippingFee) {
        $items = collect($validated['items'])->map(function (array $item) {
            $product = isset($item['productId'])
                ? Product::query()->lockForUpdate()->find($item['productId'])
                : Product::query()->lockForUpdate()->where('name', $item['name'])->first();

            if (! $product || ! $product->is_active) {
                throw ValidationException::withMessages([
                    'items' => "{$item['name']} is no longer available.",
                ]);
            }

            if ($product->stock < $item['quantity']) {
                throw ValidationException::withMessages([
                    'items' => "{$product->name} has only {$product->stock} left in stock.",
                ]);
            }

            $unitPrice = $product->priceForSize((int) $item['size']);
            $quantity = (int) $item['quantity'];

            return [
                'product' => $product,
                'data' => [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'collection' => $product->collection_label,
                    'scent' => $product->scent,
                    'size_ml' => (int) $item['size'],
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                    'line_total' => $unitPrice * $quantity,
                ],
            ];
        });

        $subtotal = $items->sum(fn (array $item) => $item['data']['line_total']);

        $order = Order::create([
            'user_id' => $request->user()->id,
            'customer_name' => $validated['firstName'].' '.$validated['surname'],
            'contact_number' => $validated['contact'],
            'delivery_address' => $address,
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'total' => $subtotal + $shippingFee,
        ]);

        foreach ($items as $item) {
            $order->items()->create($item['data']);
            $item['product']->decrement('stock', $item['data']['quantity']);
        }

        return $order;
    });

    return response()->json([
        'message' => 'Order placed.',
        'order_id' => $order->id,
        'order_reference' => $order->order_reference,
    ], 201);
})->name('orders.store');

Route::prefix('eluze-admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.store');
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
    });

Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');

Route::prefix('eluze-admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::resource('products', ProductController::class)->except(['show', 'destroy']);
        Route::get('/audit-trail', [AuditTrailController::class, 'index'])->name('audit-trail.index');
    });
