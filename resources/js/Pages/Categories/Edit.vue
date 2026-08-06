<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import Card from '../../Components/Card.vue';
import TextInput from '../../Components/TextInput.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { Save, X } from 'lucide-vue-next';

const props = defineProps({
    category: Object,
});

const form = useForm({
    name: props.category.name,
    is_active: props.category.is_active == 1,
});

const submit = () => {
    form.put(route('categories.update', props.category.id));
};
</script>

<template>
    <AppLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-surface-900">Edit Category</h1>
            <p class="text-sm text-surface-500 mt-1">Update existing category details.</p>
        </div>

        <div class="max-w-2xl">
            <Card>
                <form @submit.prevent="submit" class="space-y-6">
                    <TextInput 
                        label="Category Name" 
                        v-model="form.name" 
                        :error="form.errors.name" 
                        required 
                    />

                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="is_active" v-model="form.is_active" class="w-4 h-4 text-primary-600 border-surface-300 rounded focus:ring-primary-500">
                        <label for="is_active" class="text-sm font-semibold text-surface-700">Active Category</label>
                    </div>

                    <div class="pt-6 border-t border-surface-100 flex items-center justify-end gap-3">
                        <Link :href="route('categories.index')" class="metronic-btn metronic-btn-light">
                            <X class="w-4 h-4" /> Cancel
                        </Link>
                        <button type="submit" class="metronic-btn metronic-btn-primary" :disabled="form.processing">
                            <Save class="w-4 h-4" /> Update Category
                        </button>
                    </div>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
