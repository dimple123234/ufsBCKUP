<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Order request received', $request->all());

        try {
            // Validate the request
            $request->validate([
                'checkoutData.name' => 'required',
                'checkoutData.phone' => 'required',
                'checkoutData.address' => 'required',
                'checkoutData.payment' => 'required',
                'cartItems' => 'required|array|min:1',
            ]);

            DB::beginTransaction();

            try {
                Log::info('Processing customer data');
                
                // Always create a new customer record for each order
                $customer = Customer::create([
                    'name' => $request->input('checkoutData.name'),
                    'phone' => $request->input('checkoutData.phone'),
                    'address' => $request->input('checkoutData.address'),
                    'payment' => $request->input('checkoutData.payment'),
                    'card' => $request->input('checkoutData.card') ?: 'None'
                ]);

                Log::info('Customer created', ['customer_id' => $customer->customer_id]);

                // Create the order with customer ID
                $order = new Order();
                $order->CustomerID = $customer->customer_id;
                $order->OrderDate = now();
                $order->save();

                Log::info('Order created', ['order_id' => $order->OrderID]);

                // Create order details for each cart item
                foreach ($request->input('cartItems') as $item) {
                    OrderDetail::create([
                        'OrderID' => $order->OrderID,
                        'product_id' => $item['id'],
                        'product_name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price']
                    ]);
                }

                DB::commit();
                Log::info('Order processed successfully');

                return response()->json([
                    'success' => true,
                    'message' => 'Order processed successfully',
                    'order_id' => $order->OrderID
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error creating customer or order: ' . $e->getMessage());
                Log::error($e->getTraceAsString());

                return response()->json([
                    'success' => false,
                    'message' => 'Error processing order: ' . $e->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order processing failed: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error processing order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($orderId)
    {
        try {
            $order = Order::findOrFail($orderId);
            $orderDetails = OrderDetail::where('OrderID', $orderId)->get();
            $customer = Customer::find($order->CustomerID);

            // Calculate subtotal
            $subtotal = $orderDetails->sum(function ($detail) {
                return $detail->price * $detail->quantity;
            });

            // Fixed delivery fee
            $deliveryFee = 50;

            // Calculate total
            $total = $subtotal + $deliveryFee;

            return response()->json([
                'id' => $order->OrderID,
                'created_at' => $order->OrderDate,
                'status' => $order->Status ?? 'Pending',
                'customer' => [
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'address' => $customer->address,
                    'payment' => $customer->payment
                ],
                'items' => $orderDetails->map(function ($detail) {
                    return [
                        'id' => $detail->OrderDetailID,
                        'product' => [
                            'id' => $detail->product_id,
                            'name' => $detail->product_name,
                            'image_url' => $detail->product ? '/storage/' . $detail->product->image : '/images/default-product.png'
                        ],
                        'quantity' => (int)$detail->quantity,
                        'price' => (float)$detail->price
                    ];
                }),
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $total
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching order details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching order details: ' . $e->getMessage()
            ], 404);
        }
    }

    public function cancel($orderId)
    {
        try {
            DB::beginTransaction();

            // Find the order
            $order = Order::findOrFail($orderId);

            // Get the customer ID before deleting the order
            $customerId = $order->CustomerID;

            // Delete order details first
            OrderDetail::where('OrderID', $orderId)->delete();

            // Delete the order
            $order->delete();

            // Delete the customer record since the order was cancelled
            Customer::where('customer_id', $customerId)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function history()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                Log::error('No authenticated user found');
                return response()->json(['error' => 'User not authenticated'], 401);
            }
            
            Log::info('Fetching orders for user: ' . $user->name);
            
            // Get all orders for this user by their name
            $orders = Order::with(['orderDetails.product', 'customer'])
                ->whereHas('customer', function($query) use ($user) {
                    $query->where('name', $user->name);
                })
                ->orderBy('OrderDate', 'desc')
                ->get();
                
            Log::info('Raw orders count: ' . $orders->count());
            
            if ($orders->isEmpty()) {
                return response()->json([
                    'message' => 'No orders found in your history.',
                    'data' => []
                ]);
            }
            
            $formattedOrders = $orders->map(function ($order) {
                try {
                    if (!$order->orderDetails) {
                        Log::warning('No order details found for order: ' . $order->OrderID);
                        return null;
                    }
                    
                    $items = $order->orderDetails->map(function ($detail) {
                        try {
                            // Get the product image URL
                            $imageUrl = $detail->product 
                                ? '/storage/' . $detail->product->image 
                                : '/images/default-product.png';

                            return [
                                'id' => $detail->OrderDetailID,
                                'product' => [
                                    'name' => $detail->product_name ?? 'Unknown Product',
                                    'image_url' => $imageUrl
                                ],
                                'quantity' => (int)$detail->quantity,
                                'price' => (float)$detail->price
                            ];
                        } catch (\Exception $e) {
                            Log::error('Error processing order detail: ' . $e->getMessage());
                            return null;
                        }
                    })->filter();

                    if ($items->isEmpty()) {
                        Log::warning('No valid items found for order: ' . $order->OrderID);
                        return null;
                    }

                    $total = $items->sum(function ($item) {
                        return $item['price'] * $item['quantity'];
                    });

                    return [
                        'id' => $order->OrderID,
                        'created_at' => $order->OrderDate,
                        'status' => $order->Status ?? 'Pending',
                        'customer' => [
                            'name' => $order->customer->name ?? 'Unknown Customer',
                            'phone' => $order->customer->phone ?? '',
                            'address' => $order->customer->address ?? ''
                        ],
                        'items' => $items->values(),
                        'total' => $total
                    ];
                } catch (\Exception $e) {
                    Log::error('Error processing order ' . $order->OrderID . ': ' . $e->getMessage());
                    Log::error($e->getTraceAsString());
                    return null;
                }
            })
            ->filter()
            ->values();

            Log::info('Formatted orders count: ' . $formattedOrders->count());
            
            if ($formattedOrders->isEmpty()) {
                return response()->json([
                    'message' => 'No valid orders found in your history.',
                    'data' => []
                ]);
            }

            return response()->json($formattedOrders);
            
        } catch (\Exception $e) {
            Log::error('Error in history method: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::error('Previous exception: ' . ($e->getPrevious() ? $e->getPrevious()->getMessage() : 'None'));
            
            return response()->json([
                'error' => 'Error fetching order history: ' . $e->getMessage()
            ], 500);
        }
    }
} 