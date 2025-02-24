<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('seller')->get();
        return response()->json($products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'seller_name' => $product->seller_name,
                'seller_profile' => $product->seller ? $product->seller->profile_picture : null
            ];
        }));
    }

    public function myProducts()
    {
        try {
            \Log::info('Fetching products for user: ' . Auth::user()->name);
            
            $products = Product::where('seller_name', Auth::user()->name)->get();
            
            \Log::info('Found products:', ['count' => $products->count()]);
            
            return response()->json($products);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching products: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch products',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            \Log::info('Received product creation request', $request->all());
            
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if (!$request->hasFile('image')) {
                \Log::error('No image file in request');
                return response()->json([
                    'success' => false,
                    'message' => 'No image file uploaded'
                ], 422);
            }

            \Log::info('Image file received', [
                'original_name' => $request->file('image')->getClientOriginalName(),
                'mime_type' => $request->file('image')->getMimeType(),
                'size' => $request->file('image')->getSize()
            ]);

            // Check if storage directory exists and is writable
            $storagePath = storage_path('app/public/products');
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            if (!is_writable($storagePath)) {
                \Log::error('Storage directory is not writable', ['path' => $storagePath]);
                return response()->json([
                    'success' => false,
                    'message' => 'Server storage configuration error'
                ], 500);
            }

            $imagePath = $request->file('image')->store('products', 'public');
            
            if (!$imagePath) {
                \Log::error('Failed to save image file');
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save image'
                ], 500);
            }

            \Log::info('Image saved successfully', ['path' => $imagePath]);

            $product = Product::create([
                'name' => $validatedData['name'],
                'price' => $validatedData['price'],
                'image' => $imagePath,
                'seller_name' => Auth::user()->name
            ]);

            \Log::info('Product created successfully', ['product_id' => $product->id]);

            return response()->json([
                'success' => true,
                'product' => $product
            ]);
        } catch (\Exception $e) {
            \Log::error('Product creation error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating product: ' . $e->getMessage(),
                'details' => app()->environment('local') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
            ], 500);
        }
    }

    public function destroy($id)
    {
        $product = Product::where('seller_name', Auth::user()->name)
                         ->findOrFail($id);
        
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();
        
        return response()->json(['success' => true]);
    }
}
