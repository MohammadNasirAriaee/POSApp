<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import CategoryForm from './Partials/CategoryForm.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    is_active: true,
});

const submit = () => {
    form.post(route('categories.store'));
};
</script>

<template>
    <AppLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-surface-900">Add Category</h1>
            <p class="text-sm text-surface-500 mt-1">Create a new product category.</p>
        </div>

        <div class="max-w-2xl">
            <CategoryForm :form="form" submit-label="Save Category" @submit="submit" />
        </div>
    </AppLayout>
</template>
