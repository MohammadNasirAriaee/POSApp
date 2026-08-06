<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import Card from '../../Components/Card.vue';
import TextInput from '../../Components/TextInput.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { Save, X } from 'lucide-vue-next';

const props = defineProps({
    categories: Array,
});

const form = useForm({
    name: '',
    sku: '',
    category_id: '',
    price: '',
    cost: '',
    stock_quantity: 0,
    status: 'active',
});

const submit = () => {
    form.post(route('products.store'));
};
</script>

<template>
    <AppLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-surface-900">Add Product</h1>
            <p class="text-sm text-surface-500 mt-1">Create a new product in your inventory.</p>
        </div>

        <div class="max-w-3xl">
            <Card>
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <TextInput 
                            label="Product Name" 
                            v-model="form.name" 
                            :error="form.errors.name" 
                            required 
                        />
                        <TextInput 
                            label="SKU / Barcode" 
                            v-model="form.sku" 
                            :error="form.errors.sku" 
                            required 
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-surface-700 mb-1.5">Category</label>
                            <select v-model="form.category_id" class="metronic-input">
                                <option value="">Select Category...</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.category_id" class="mt-1.5 text-xs font-medium text-rose-600">{{ form.errors.category_id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-surface-700 mb-1.5">Status</label>
                            <select v-model="form.status" class="metronic-input">
                                <option value="active">Active</option>
                                <option value="draft">Draft</option>
                                <option value="out_of_stock">Out of Stock</option>
                            </select>
                            <p v-if="form.errors.status" class="mt-1.5 text-xs font-medium text-rose-600">{{ form.errors.status }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <TextInput 
                            label="Price ($)" 
                            type="number"
                            step="0.01"
                            v-model="form.price" 
                            :error="form.errors.price" 
                            required 
                        />
                        <TextInput 
                            label="Cost ($)" 
                            type="number"
                            step="0.01"
                            v-model="form.cost" 
                            :error="form.errors.cost" 
                        />
                        <TextInput 
                            label="Stock Quantity" 
                            type="number"
                            v-model="form.stock_quantity" 
                            :error="form.errors.stock_quantity" 
                            required 
                        />
                    </div>

                    <div class="pt-6 border-t border-surface-100 flex items-center justify-end gap-3">
                        <Link :href="route('products.index')" class="metronic-btn metronic-btn-light">
                            <X class="w-4 h-4" /> Cancel
                        </Link>
                        <button type="submit" class="metronic-btn metronic-btn-primary" :disabled="form.processing">
                            <Save class="w-4 h-4" /> Save Product
                        </button>
                    </div>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
