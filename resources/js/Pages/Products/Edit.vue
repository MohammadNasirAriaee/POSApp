<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import ProductForm from './Partials/ProductForm.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    product: Object,
    categories: Array,
    statusLabels: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
    name: props.product.name,
    sku: props.product.sku,
    description: props.product.description || '',
    category_id: props.product.category_id || '',
    price: props.product.price,
    cost: props.product.cost,
    stock_quantity: props.product.stock_quantity,
    status: props.product.status,
});

const submit = () => {
    form.put(route('products.update', props.product.id));
};
</script>

<template>
    <Head :title="`Edit ${product.name}`" />
    <AppLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-surface-900">Edit Product</h1>
            <p class="text-sm text-surface-500 mt-1">Update inventory details for {{ product.name }}.</p>
        </div>

        <div class="max-w-3xl">
            <ProductForm
                :form="form"
                :categories="categories"
                :status-labels="statusLabels"
                submit-label="Update Product"
                @submit="submit"
            />
        </div>
    </AppLayout>
</template>
