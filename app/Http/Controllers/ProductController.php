<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSeller;
use App\Models\ProductVariant;
use App\Models\ProductVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $products = $vendor->products()
            ->with('brand', 'category')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => (string) $product->id,
                    'title' => (string) $product->title,
                    'slug' => (string) $product->slug,
                    'price' => (string) ($product->pivot->price ?? '0'),
                    'stock' => (int) ($product->pivot->stock ?? 0),
                    'is_active' => (bool) $product->is_active,
                    'created_at' => $product->created_at?->toIso8601String() ?? '',
                    'brand' => $product->brand ? [
                        'id' => (string) $product->brand->id,
                        'name' => $product->brand->name,
                    ] : null,
                    'category' => $product->category ? [
                        'id' => (string) $product->category->id,
                        'name' => $product->category->name,
                    ] : null,
                ];
            })
            ->values()
            ->all();

        $categories = Category::orderBy('name')->get()->map(function ($category) {
            return [
                'id' => (string) $category->id,
                'name' => $category->name,
            ];
        })->values()->all();

        $brands = Brand::orderBy('name')->get()->map(function ($brand) {
            return [
                'id' => (string) $brand->id,
                'name' => $brand->name,
            ];
        })->values()->all();

        return Inertia::render('product/index', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'vendor' => [
                'name' => $vendor->name,
                'address' => $vendor->address,
                'email' => $vendor->user->email ?? null,
                'phone' => $vendor->user->phone ?? null,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required_without:new_brand_name|exists:brands,id',
            'new_brand_name' => 'required_without:brand_id|string|max:255',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'stock' => 'required|integer|min:0',
            'dispatch_days' => 'nullable|integer|min:0',
            'shipping_type' => 'nullable|string',
            'shipping_time' => 'nullable|integer|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'variants' => 'nullable|array',
            'variants.*.sku' => 'required|string|max:255',
            'variants.*.size' => 'nullable|string|max:50',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.weight' => 'nullable|numeric|min:0',
            'images' => 'nullable|array|max:4',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'videos' => 'nullable|array|max:4',
            'videos.*.url' => 'required|url',
            'videos.*.title' => 'nullable|string|max:255',
            // Güvenlik Bilgileri
            'safety_information' => 'nullable|string',
            'manufacturer_name' => 'nullable|string|max:255',
            'manufacturer_address' => 'nullable|string',
            'manufacturer_contact' => 'nullable|string|max:255',
            'responsible_party_name' => 'nullable|string|max:255',
            'responsible_party_address' => 'nullable|string',
            'responsible_party_contact' => 'nullable|string|max:255',
            // Ek Bilgiler
            'additional_information' => 'nullable|string',
            'additional_info' => 'nullable|string',
            'tags' => 'nullable|string',
        ]);

        // Handle brand creation if new brand
        $brandId = $validated['brand_id'] ?? null;
        if (isset($validated['new_brand_name']) && !empty($validated['new_brand_name'])) {
            $brandSlug = Str::slug($validated['new_brand_name']);
            $uniqueBrandSlug = $brandSlug;
            $brandCounter = 1;
            while (Brand::where('slug', $uniqueBrandSlug)->exists()) {
                $uniqueBrandSlug = $brandSlug . '-' . $brandCounter;
                $brandCounter++;
            }

            $newBrand = Brand::create([
                'name' => $validated['new_brand_name'],
                'slug' => $uniqueBrandSlug,
                'vendor_id' => $vendor->id,
                'is_vendor_brand' => true,
            ]);
            $brandId = $newBrand->id;
        }

        $slug = Str::slug($validated['title']);
        $uniqueSlug = $slug;
        $counter = 1;
        while (Product::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = $slug . '-' . $counter;
            $counter++;
        }

        // Process additional_info (convert newline-separated string to JSON array)
        $additionalInfoArray = null;
        if (isset($validated['additional_info']) && !empty(trim($validated['additional_info']))) {
            $lines = array_filter(
                array_map('trim', explode("\n", $validated['additional_info'])),
                fn($line) => !empty($line)
            );
            if (!empty($lines)) {
                $additionalInfoArray = json_encode(array_values($lines));
            }
        }

        // Process tags (convert comma-separated string to array)
        $tagsArray = null;
        if (isset($validated['tags']) && !empty(trim($validated['tags']))) {
            $tags = array_filter(
                array_map('trim', explode(',', $validated['tags'])),
                fn($tag) => !empty($tag)
            );
            if (!empty($tags)) {
                $tagsArray = array_values($tags);
            }
        }

        $product = Product::create([
            'vendor_id' => $vendor->id,
            'title' => $validated['title'],
            'slug' => $uniqueSlug,
            'description' => $validated['description'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'category_id' => $validated['category_id'],
            'brand_id' => $brandId,
            'price' => $validated['price'],
            'original_price' => $validated['original_price'] ?? null,
            'discount_price' => $validated['discount_price'] ?? null,
            'stock' => $validated['stock'],
            'shipping_time' => $validated['shipping_time'] ?? null,
            'is_active' => true, // Vendor ürünleri varsayılan olarak aktif, admin onayı sonrası aktif olacak
            // Güvenlik Bilgileri
            'safety_information' => $validated['safety_information'] ?? null,
            'manufacturer_name' => $validated['manufacturer_name'] ?? null,
            'manufacturer_address' => $validated['manufacturer_address'] ?? null,
            'manufacturer_contact' => $validated['manufacturer_contact'] ?? null,
            'responsible_party_name' => $validated['responsible_party_name'] ?? null,
            'responsible_party_address' => $validated['responsible_party_address'] ?? null,
            'responsible_party_contact' => $validated['responsible_party_contact'] ?? null,
            // Ek Bilgiler
            'additional_information' => $validated['additional_information'] ?? null,
            'additional_info' => $additionalInfoArray,
            'tags' => $tagsArray,
        ]);

        ProductSeller::create([
            'product_id' => $product->id,
            'vendor_id' => $vendor->id,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'dispatch_days' => $validated['dispatch_days'] ?? 1,
            'shipping_type' => $validated['shipping_type'] ?? 'standard',
            'is_featured' => false,
        ]);

        // Handle variants
        if (isset($validated['variants']) && is_array($validated['variants'])) {
            foreach ($validated['variants'] as $index => $variantData) {
                // Check if SKU is unique
                $sku = $variantData['sku'];
                $uniqueSku = $sku;
                $skuCounter = 1;
                while (ProductVariant::where('sku', $uniqueSku)->exists()) {
                    $uniqueSku = $sku . '-' . $skuCounter;
                    $skuCounter++;
                }

                $variantPrice = $variantData['price'] ?? $validated['price'];
                $variantValue = $variantData['size'] ?? $variantData['color'] ?? "Varyant " . ($index + 1);

                ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $uniqueSku,
                    'size' => $variantData['size'] ?? null,
                    'color' => $variantData['color'] ?? null,
                    'stock' => $variantData['stock'],
                    'price' => $variantPrice,
                    'original_price' => $validated['price'],
                    'weight' => $variantData['weight'] ?? null,
                    'value' => $variantValue,
                    'is_default' => $index === 0,
                    'is_active' => true,
                ]);
            }
        }

        // Handle image uploads (max 4)
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            // If single file, convert to array
            if (!is_array($images)) {
                $images = [$images];
            }

            // Limit to 4 images
            $images = array_slice($images, 0, 4);

            foreach ($images as $index => $image) {
                if ($image && $image->isValid()) {
                    $path = $image->store('products/images', 'r2');
                    $url = Storage::disk('r2')->url($path);

                    ProductImage::create([
                        'product_id' => $product->id,
                        'url' => $url,
                        'alt' => $validated['title'] . ' - Görsel ' . ($index + 1),
                        'is_main' => $index === 0, // First image is main
                        'order' => $index,
                    ]);
                }
            }
        }

        // Handle video uploads (max 4)
        if ($request->has('videos') && is_array($request->videos)) {
            $videos = array_slice($request->videos, 0, 4);
            foreach ($videos as $index => $video) {
                if (isset($video['url']) && !empty($video['url'])) {
                    // Determine video type from URL
                    $videoType = 'other';
                    if (str_contains($video['url'], 'youtube.com') || str_contains($video['url'], 'youtu.be')) {
                        $videoType = 'youtube';
                    } elseif (str_contains($video['url'], 'vimeo.com')) {
                        $videoType = 'vimeo';
                    }

                    ProductVideo::create([
                        'product_id' => $product->id,
                        'url' => $video['url'],
                        'title' => $video['title'] ?? null,
                        'video_type' => $videoType,
                        'order' => $index,
                        'is_featured' => $index === 0, // First video is featured
                    ]);
                }
            }
        }

        return redirect()->route('product.index')->with('success', 'Ürün başarıyla oluşturuldu.');
    }

    public function update(Request $request, string $id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.application.pending');
        }

        $product = Product::findOrFail($id);

        // Vendor kontrolü
        if ($product->vendor_id !== $vendor->id) {
            abort(403, 'Bu ürüne erişim yetkiniz yok.');
        }

        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $product->update([
            'is_active' => $validated['is_active'],
        ]);

        return redirect()->route('product.index')
            ->with('success', 'Ürün durumu güncellendi.');
    }
}
