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
        default: 'Save Category',
    },
});

const emit = defineEmits(['submit']);
</script>

<template>
    <Card>
        <form @submit.prevent="emit('submit')" class="space-y-6">
            <TextInput
                label="Category Name"
                v-model="form.name"
                :error="form.errors.name"
                placeholder="e.g. Beverages"
                required
            />

            <div>
                <label for="category-description" class="block text-sm font-semibold text-surface-700 mb-1.5">Description</label>
                <textarea
                    id="category-description"
                    v-model="form.description"
                    rows="3"
                    :maxlength="1000"
                    class="metronic-input"
                    :class="[form.errors.description ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-500 bg-rose-50/30' : '']"
                    :aria-invalid="form.errors.description ? 'true' : 'false'"
                    placeholder="Optional notes about this category"
                ></textarea>
                <p v-if="form.errors.description" class="mt-1.5 text-xs font-medium text-rose-600">{{ form.errors.description }}</p>
            </div>

            <div class="flex items-center gap-3">
                <input
                    type="checkbox"
                    id="is_active"
                    v-model="form.is_active"
                    class="w-4 h-4 text-primary-600 border-surface-300 rounded focus:ring-primary-500"
                />
                <label for="is_active" class="text-sm font-semibold text-surface-700">Active Category</label>
            </div>

            <div class="pt-6 border-t border-surface-100 flex items-center justify-end gap-3">
                <Link :href="route('categories.index')" class="metronic-btn metronic-btn-light">
                    <X class="w-4 h-4" /> Cancel
                </Link>
                <button type="submit" class="metronic-btn metronic-btn-primary" :disabled="form.processing">
                    <Save class="w-4 h-4" /> {{ submitLabel }}
                </button>
            </div>
        </form>
    </Card>
</template>
