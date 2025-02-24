<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\SellerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function login(Request $request)
    {
        \Log::info('Admin login attempt', ['username' => $request->username]);
        
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            $admin = Auth::guard('admin')->user();
            \Log::info('Admin login successful', ['admin_id' => $admin->id]);
            
            return response()->json([
                'success' => true,
                'user' => $admin,
                'message' => 'Login successful'
            ]);
        }

        \Log::warning('Admin login failed', ['username' => $request->username]);
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    public function logout(Request $request)
    {
        try {
            // Clear admin guard
            Auth::guard('admin')->logout();
            
            // Clear session data
            $request->session()->flush();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            // Clear any potential cookies
            \Cookie::forget('laravel_session');
            \Cookie::forget('XSRF-TOKEN');
            
            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUsers()
    {
        try {
            $users = User::all();
            \Log::info('Users fetched:', ['count' => $users->count()]);
            return response()->json($users);
        } catch (\Exception $e) {
            \Log::error('Error fetching users:', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error fetching users'], 500);
        }
    }

    public function getCustomers()
    {
        $customers = Customer::all();
        return response()->json($customers);
    }

    public function getEmployees()
    {
        $employees = Employee::all();
        return response()->json($employees);
    }

    public function getProducts()
    {
        try {
            $products = Product::all();
            \Log::info('Products fetched:', ['count' => $products->count()]);
            return response()->json($products);
        } catch (\Exception $e) {
            \Log::error('Error fetching products:', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error fetching products'], 500);
        }
    }

    public function getOrders()
    {
        try {
            $orders = Order::with(['employee', 'customer'])->get();
            \Log::info('Orders fetched:', ['count' => $orders->count()]);
            return response()->json($orders);
        } catch (\Exception $e) {
            \Log::error('Error fetching orders:', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error fetching orders'], 500);
        }
    }

    public function getOrderDetails()
    {
        $orderDetails = OrderDetail::with('product')->get();
        return response()->json($orderDetails);
    }

    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Begin transaction to ensure all related records are deleted
            \DB::beginTransaction();
            
            try {
                // Delete related seller applications first
                $user->sellerApplications()->delete();
                
                // Delete the user
                $user->delete();
                
                \DB::commit();
                \Log::info('User and related records deleted successfully', ['user_id' => $id]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'User and all related records deleted successfully'
                ]);
            } catch (\Exception $e) {
                \DB::rollBack();
                \Log::error('Error during user deletion transaction', [
                    'user_id' => $id,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Failed to delete user', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeEmployee(Request $request)
    {
        try {
            \Log::info('Received employee creation request:', $request->all());
            
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:employees,email',
                'phone_number' => 'required|string|max:20',
                'notes' => 'nullable|string'
            ]);

            \Log::info('Validation passed, creating employee');
            
            $employee = Employee::create($validated);
            
            \Log::info('Employee created successfully:', ['employee_id' => $employee->employee_id]);
            
            return response()->json([
                'message' => 'Employee created successfully',
                'employee' => $employee
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Validation failed when creating employee:', ['errors' => $e->errors()]);
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating employee:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Error creating employee: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteEmployee($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $employee->delete();
            return response()->json(['message' => 'Employee deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting employee'], 500);
        }
    }

    public function deleteCustomer($id)
    {
        try {
            $customer = Customer::find($id);
            if (!$customer) {
                return response()->json(['message' => 'Customer not found'], 404);
            }

            // Delete all related order details first
            \DB::table('orderdetails')
                ->whereIn('OrderID', function($query) use ($id) {
                    $query->select('OrderID')
                        ->from('orders')
                        ->where('CustomerID', $id);
                })
                ->delete();

            // Delete all related orders
            \DB::table('orders')->where('CustomerID', $id)->delete();

            // Finally delete the customer
            \DB::table('customers')->where('customer_id', $id)->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function assignEmployee(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,OrderID',
            'employee_id' => 'required|exists:employees,employee_id'
        ]);

        try {
            $order = Order::findOrFail($validated['order_id']);
            $order->employee_id = $validated['employee_id'];
            $order->save();

            return response()->json(['message' => 'Employee assigned successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error assigning employee'], 500);
        }
    }

    public function deleteProduct($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            // Delete the product image if it exists
            if ($product->image && Storage::exists('public/' . $product->image)) {
                Storage::delete('public/' . $product->image);
            }
            
            $product->delete();
            return response()->json(['message' => 'Product deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting product'], 500);
        }
    }

    public function getSellerApplications()
    {
        $applications = SellerApplication::all();
        return response()->json($applications);
    }

    public function applyForSeller(Request $request) {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:seller_applications,email|unique:users,email',
                'phone' => 'required|string',
                'address' => 'required|string',
                'password' => 'required|min:6'
            ]);

            // Create seller application
            $application = SellerApplication::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'address' => $validatedData['address'],
                'password' => Hash::make($validatedData['password']),
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Your seller application has been submitted successfully. Please wait for admin approval.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error submitting seller application:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error submitting application: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approveSellerApplication($id)
    {
        try {
            $application = SellerApplication::findOrFail($id);
            
            // Begin transaction
            \DB::beginTransaction();
            
            try {
                // Create new user from application
                $user = User::create([
                    'name' => $application->name,
                    'email' => $application->email,
                    'password' => $application->password, // Already hashed
                    'phone' => $application->phone,
                    'address' => $application->address,
                    'role' => 'seller',
                    'is_approved' => true // Explicitly set to true
                ]);

                // Delete the application since it's approved
                $application->delete();

                \DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Seller application approved successfully'
                ]);
            } catch (\Exception $e) {
                \DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Error approving seller application:', [
                'application_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error approving application: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rejectSellerApplication($id)
    {
        try {
            $application = SellerApplication::findOrFail($id);
            
            // Update the application status to rejected
            $application->status = 'rejected';
            $application->reviewed_at = now();
            $application->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Application rejected successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error rejecting seller application:', [
                'application_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting application: ' . $e->getMessage()
            ], 500);
        }
    }
} 