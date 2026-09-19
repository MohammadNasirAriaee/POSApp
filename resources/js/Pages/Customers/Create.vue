<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import CustomerForm from './Partials/CustomerForm.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    address: '',
});

const submit = () => {
    form.post(route('customers.store'));
};
</script>

<template>
    <AppLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-surface-900">Add Customer</h1>
            <p class="text-sm text-surface-500 mt-1">Create a new customer profile.</p>
        </div>

        <div class="max-w-2xl">
            <CustomerForm :form="form" submit-label="Save Customer" @submit="submit" />
        </div>
    </AppLayout>
</template>
