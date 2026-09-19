<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ModelFactoryTest extends TestCase
{
    use RefreshDatabase;

    public static function modelProvider(): array
    {
        return [
            'category' => [Category::class],
            'customer' => [Customer::class],
            'product' => [Product::class],
            'order' => [Order::class],
            'order item' => [OrderItem::class],
        ];
    }

    #[DataProvider('modelProvider')]
    public function test_model_exposes_a_working_factory(string $model): void
    {
        $record = $model::factory()->create();

        $this->assertInstanceOf($model, $record);
        $this->assertDatabaseHas($record->getTable(), ['id' => $record->getKey()]);
    }
}
