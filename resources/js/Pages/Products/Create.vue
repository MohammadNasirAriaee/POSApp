<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import ProductForm from './Partials/ProductForm.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    categories: Array,
    statusLabels: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
    name: '',
    sku: '',
    description: '',
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
    <Head title="Add Product" />
    <AppLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-surface-900">Add Product</h1>
            <p class="text-sm text-surface-500 mt-1">Create a new product in your inventory.</p>
        </div>

        <div class="max-w-3xl">
            <ProductForm
                :form="form"
                :categories="categories"
                :status-labels="statusLabels"
                submit-label="Save Product"
                @submit="submit"
            />
        </div>
    </AppLayout>
</template>
