<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import CustomerForm from './Partials/CustomerForm.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    customer: Object,
});

const form = useForm({
    first_name: props.customer.first_name,
    last_name: props.customer.last_name || '',
    email: props.customer.email || '',
    phone: props.customer.phone || '',
    address: props.customer.address || '',
});

const submit = () => {
    form.put(route('customers.update', props.customer.id));
};
</script>

<template>
    <AppLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-surface-900">Edit Customer</h1>
            <p class="text-sm text-surface-500 mt-1">Update profile for {{ customer.name }}.</p>
        </div>

        <div class="max-w-2xl">
            <CustomerForm :form="form" submit-label="Update Customer" @submit="submit" />
        </div>
    </AppLayout>
</template>
