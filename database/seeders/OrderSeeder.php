<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::all();
        $employees = Employee::all();
        $products = Product::where('status', 'active')->get();

        if ($products->isEmpty()) {
            return; // Can't seed orders without products
        }

        // Let's create 15 dummy orders over the last 30 days
        for ($i = 0; $i < 15; $i++) {
            $customer = rand(0, 3) > 0 ? $customers->random() : null; // 75% chance of having a customer
            $employee = $employees->count() > 0 ? $employees->random() : null;
            
            // Generate 1 to 4 random items
            $numItems = rand(1, 4);
            $orderProducts = $products->random($numItems);
            
            $subtotal = 0;
            $itemsData = [];

            foreach ($orderProducts as $product) {
                $quantity = rand(1, 3);
                $lineTotal = $product->price * $quantity;
                $subtotal += $lineTotal;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $quantity,
                    'subtotal' => $lineTotal,
                ];
            }

            $taxRate = 0.05; // 5%
            $tax = $subtotal * $taxRate;
            $discount = rand(0, 2) === 0 ? rand(1, 5) : 0; // 33% chance of discount
            
            $total = max(0, $subtotal + $tax - $discount);
            
            $date = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            $statuses = ['completed', 'completed', 'completed', 'completed', 'pending', 'cancelled'];

            $order = Order::create([
                'customer_id' => $customer ? $customer->id : null,
                'employee_id' => $employee ? $employee->id : null,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => rand(0, 1) ? 'Cash' : 'Card',
                'status' => $statuses[array_rand($statuses)],
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            foreach ($itemsData as $itemData) {
                $itemData['order_id'] = $order->id;
                $itemData['created_at'] = $date;
                $itemData['updated_at'] = $date;
                OrderItem::create($itemData);
            }
        }
    }
}
