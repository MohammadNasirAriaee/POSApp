<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
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
        $products = Product::active()->get();

        if ($products->isEmpty()) {
            return; // Can't seed orders without products
        }

        // Let's create 15 dummy orders over the last 30 days
        for ($i = 0; $i < 15; $i++) {
            // 75% chance of a named customer, when there are any to pick from.
            $customer = ($customers->isNotEmpty() && rand(0, 3) > 0)
                ? $customers->random()
                : null;
            $employee = $employees->isNotEmpty() ? $employees->random() : null;

            // Generate 1 to 4 random items
            $numItems = min(rand(1, 4), $products->count());
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
            $tax = round($subtotal * $taxRate, 2);
            $discount = rand(0, 2) === 0 ? rand(1, 5) : 0; // 33% chance of discount

            $total = max(0, $subtotal + $tax - $discount);

            $date = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            $statuses = [
                Order::STATUS_COMPLETED,
                Order::STATUS_COMPLETED,
                Order::STATUS_COMPLETED,
                Order::STATUS_COMPLETED,
                Order::STATUS_PENDING,
                Order::STATUS_CANCELLED,
            ];

            // Timestamps are not mass assignable, so they are set on the
            // instance; assigning them before save stops Eloquent from
            // replacing them with the current time.
            $order = new Order([
                'customer_id' => $customer?->id,
                'employee_id' => $employee?->id,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'tendered' => $total,
                'payment_method' => rand(0, 1) ? 'cash' : 'card',
                'status' => $statuses[array_rand($statuses)],
            ]);
            $order->created_at = $date;
            $order->updated_at = $date;
            $order->save();

            foreach ($itemsData as $itemData) {
                $item = new OrderItem($itemData);
                $item->order_id = $order->id;
                $item->created_at = $date;
                $item->updated_at = $date;
                $item->save();
            }
        }
    }
}
