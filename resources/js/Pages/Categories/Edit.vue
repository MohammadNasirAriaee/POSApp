<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import CategoryForm from './Partials/CategoryForm.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    category: Object,
});

const form = useForm({
    name: props.category.name,
    description: props.category.description || '',
    is_active: Boolean(props.category.is_active),
});

const submit = () => {
    form.put(route('categories.update', props.category.id));
};
</script>

<template>
    <Head :title="`Edit ${category.name}`" />
    <AppLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-surface-900">Edit Category</h1>
            <p class="text-sm text-surface-500 mt-1">Update existing category details.</p>
        </div>

        <div class="max-w-2xl">
            <CategoryForm :form="form" submit-label="Update Category" @submit="submit" />
        </div>
    </AppLayout>
</template>
