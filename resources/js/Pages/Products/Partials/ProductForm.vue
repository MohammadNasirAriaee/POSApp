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
    categories: {
        type: Array,
        default: () => [],
    },
    statusLabels: {
        type: Object,
        default: () => ({}),
    },
    submitLabel: {
        type: String,
        default: 'Save Product',
    },
});

const emit = defineEmits(['submit']);
</script>

<template>
    <Card>
        <form @submit.prevent="emit('submit')" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <TextInput
                    label="Product Name"
                    v-model="form.name"
                    :error="form.errors.name"
                    required
                />
                <TextInput
                    label="SKU / Barcode"
                    v-model="form.sku"
                    :error="form.errors.sku"
                    required
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="product-category" class="block text-sm font-semibold text-surface-700 mb-1.5">Category</label>
                    <select id="product-category" v-model="form.category_id" class="metronic-input">
                        <option value="">Select Category...</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                    <p v-if="form.errors.category_id" class="mt-1.5 text-xs font-medium text-rose-600">{{ form.errors.category_id }}</p>
                </div>
                <div>
                    <label for="product-status" class="block text-sm font-semibold text-surface-700 mb-1.5">Status</label>
                    <select id="product-status" v-model="form.status" class="metronic-input">
                        <option
                            v-for="(label, value) in statusLabels"
                            :key="value"
                            :value="value"
                        >
                            {{ label }}
                        </option>
                    </select>
                    <p v-if="form.errors.status" class="mt-1.5 text-xs font-medium text-rose-600">{{ form.errors.status }}</p>
                </div>
            </div>

            <div>
                <label for="product-description" class="block text-sm font-semibold text-surface-700 mb-1.5">Description</label>
                <textarea
                    id="product-description"
                    v-model="form.description"
                    rows="3"
                    :maxlength="2000"
                    class="metronic-input"
                    :class="[form.errors.description ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-500 bg-rose-50/30' : '']"
                    :aria-invalid="form.errors.description ? 'true' : 'false'"
                    placeholder="Optional product details"
                ></textarea>
                <p v-if="form.errors.description" class="mt-1.5 text-xs font-medium text-rose-600">{{ form.errors.description }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <TextInput
                    label="Price ($)"
                    type="number"
                    step="0.01"
                    min="0"
                    inputmode="decimal"
                    v-model="form.price"
                    :error="form.errors.price"
                    required
                />
                <TextInput
                    label="Cost ($)"
                    type="number"
                    step="0.01"
                    min="0"
                    inputmode="decimal"
                    v-model="form.cost"
                    :error="form.errors.cost"
                />
                <TextInput
                    label="Stock Quantity"
                    type="number"
                    min="0"
                    v-model="form.stock_quantity"
                    :error="form.errors.stock_quantity"
                    required
                />
            </div>

            <div class="pt-6 border-t border-surface-100 flex items-center justify-end gap-3">
                <Link :href="route('products.index')" class="metronic-btn metronic-btn-light">
                    <X class="w-4 h-4" /> Cancel
                </Link>
                <button type="submit" class="metronic-btn metronic-btn-primary" :disabled="form.processing">
                    <Save class="w-4 h-4" /> {{ submitLabel }}
                </button>
            </div>
        </form>
    </Card>
</template>
