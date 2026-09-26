<script setup>
import Card from '../../../Components/Card.vue';
import TextInput from '../../../Components/TextInput.vue';
import { Link } from '@inertiajs/vue3';
import { Save, X } from 'lucide-vue-next';

defineProps({
    // The Inertia useForm instance owned by the create/edit page.
    form: {
        type: Object,
        required: true,
    },
    submitLabel: {
        type: String,
        default: 'Save Customer',
    },
});

const emit = defineEmits(['submit']);
</script>

<template>
    <Card>
        <form @submit.prevent="emit('submit')" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <TextInput
                    label="First Name"
                    v-model="form.first_name"
                    :error="form.errors.first_name"
                    maxlength="255"
                    required
                />
                <TextInput
                    label="Last Name"
                    v-model="form.last_name"
                    :error="form.errors.last_name"
                    maxlength="255"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <TextInput
                    label="Email Address"
                    type="email"
                    autocomplete="email"
                    maxlength="255"
                    v-model="form.email"
                    :error="form.errors.email"
                />
                <TextInput
                    label="Phone Number"
                    type="tel"
                    autocomplete="tel"
                    maxlength="20"
                    v-model="form.phone"
                    :error="form.errors.phone"
                />
            </div>

            <div>
                <label for="customer-address" class="block text-sm font-semibold text-surface-700 mb-1.5">Mailing Address</label>
                <textarea
                    id="customer-address"
                    v-model="form.address"
                    rows="3"
                    autocomplete="street-address"
                    class="metronic-input"
                    :class="[form.errors.address ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-500 bg-rose-50/30' : '']"
                    :aria-invalid="form.errors.address ? 'true' : 'false'"
                ></textarea>
                <p v-if="form.errors.address" class="mt-1.5 text-xs font-medium text-rose-600">{{ form.errors.address }}</p>
            </div>

            <div class="pt-6 border-t border-surface-100 flex items-center justify-end gap-3">
                <Link :href="route('customers.index')" class="metronic-btn metronic-btn-light">
                    <X class="w-4 h-4" /> Cancel
                </Link>
                <button type="submit" class="metronic-btn metronic-btn-primary" :disabled="form.processing">
                    <Save class="w-4 h-4" /> {{ submitLabel }}
                </button>
            </div>
        </form>
    </Card>
</template>
