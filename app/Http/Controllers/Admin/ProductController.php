<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'collection' => ['nullable', Rule::in(array_keys(Product::COLLECTIONS))],
            'stock' => ['nullable', Rule::in(['low', 'out'])],
        ]);

        $products = Product::query()
            ->search($validated['search'] ?? null)
            ->when($validated['collection'] ?? null, fn ($query, $collection) => $query->where('collection', $collection))
            ->when(($validated['stock'] ?? null) === 'low', fn ($query) => $query->whereColumn('stock', '<=', 'low_stock_threshold')->where('stock', '>', 0))
            ->when(($validated['stock'] ?? null) === 'out', fn ($query) => $query->where('stock', '<=', 0))
            ->orderByRaw("CASE collection WHEN 'women' THEN 1 WHEN 'men' THEN 2 WHEN 'unisex' THEN 3 ELSE 4 END")
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', [
            'collections' => Product::COLLECTIONS,
            'filters' => $validated,
            'products' => $products,
        ]);
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'collections' => Product::COLLECTIONS,
            'product' => new Product([
                'collection' => 'women',
                'price_10ml' => 850,
                'price_30ml' => 1350,
                'price_50ml' => 1850,
                'price_75ml' => 2350,
                'price_100ml' => 2850,
                'stock' => 0,
                'low_stock_threshold' => 5,
                'is_active' => true,
            ]),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $product = Product::create($this->validatedProduct($request));

        AuditLog::record($request, 'product.created', "Created product {$product->name}.", $product);

        return redirect()->route('admin.products.index')->with('status', 'Product created.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.form', [
            'collections' => Product::COLLECTIONS,
            'product' => $product,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $before = $product->only(['name', 'collection', 'stock', 'low_stock_threshold', 'is_active']);

        $product->update($this->validatedProduct($request, $product));

        AuditLog::record($request, 'product.updated', "Updated product {$product->name}.", $product, [
            'before' => $before,
            'after' => $product->only(['name', 'collection', 'stock', 'low_stock_threshold', 'is_active']),
        ]);

        return redirect()->route('admin.products.index')->with('status', 'Product updated.');
    }

    protected function validatedProduct(Request $request, ?Product $product = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:160', Rule::unique('products', 'name')->ignore($product)],
            'collection' => ['required', Rule::in(array_keys(Product::COLLECTIONS))],
            'scent' => ['nullable', 'string', 'max:160'],
            'inspiration' => ['nullable', 'string', 'max:180'],
            'price_10ml' => ['required', 'integer', 'min:1', 'max:999999'],
            'price_30ml' => ['required', 'integer', 'min:1', 'max:999999'],
            'price_50ml' => ['required', 'integer', 'min:1', 'max:999999'],
            'price_75ml' => ['required', 'integer', 'min:1', 'max:999999'],
            'price_100ml' => ['required', 'integer', 'min:1', 'max:999999'],
            'stock' => ['required', 'integer', 'min:0', 'max:999999'],
            'low_stock_threshold' => ['required', 'integer', 'min:0', 'max:999999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
