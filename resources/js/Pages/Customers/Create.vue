<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import Card from '../../Components/Card.vue';
import TextInput from '../../Components/TextInput.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { Save, X } from 'lucide-vue-next';

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
            <Card>
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <TextInput 
                            label="First Name" 
                            v-model="form.first_name" 
                            :error="form.errors.first_name" 
                            required 
                        />
                        <TextInput 
                            label="Last Name" 
                            v-model="form.last_name" 
                            :error="form.errors.last_name" 
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <TextInput 
                            label="Email Address" 
                            type="email"
                            v-model="form.email" 
                            :error="form.errors.email" 
                        />
                        <TextInput 
                            label="Phone Number" 
                            v-model="form.phone" 
                            :error="form.errors.phone" 
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-surface-700 mb-1.5">Mailing Address</label>
                        <textarea 
                            v-model="form.address"
                            rows="3"
                            class="metronic-input"
                            :class="[form.errors.address ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-500 bg-rose-50/30' : '']"
                        ></textarea>
                        <p v-if="form.errors.address" class="mt-1.5 text-xs font-medium text-rose-600">{{ form.errors.address }}</p>
                    </div>

                    <div class="pt-6 border-t border-surface-100 flex items-center justify-end gap-3">
                        <Link :href="route('customers.index')" class="metronic-btn metronic-btn-light">
                            <X class="w-4 h-4" /> Cancel
                        </Link>
                        <button type="submit" class="metronic-btn metronic-btn-primary" :disabled="form.processing">
                            <Save class="w-4 h-4" /> Save Customer
                        </button>
                    </div>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
