<script setup>
import AppLayout from "../../Layouts/AppLayout.vue";
import Card from "../../Components/Card.vue";
import DataTable from "../../Components/DataTable.vue";
import Pagination from "../../Components/Pagination.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Plus, Edit2, Trash2, Search, X } from "lucide-vue-next";
import { useDebouncedSearch } from "../../Composables/useDebouncedSearch";

const props = defineProps({
    customers: Object,
    search: String,
});

const form = useForm({});
const { searchQuery, clearSearch } = useDebouncedSearch(
    "customers.index",
    props.search,
);

const deleteCustomer = (customer) => {
    if (confirm(`Delete ${customer.name}?`)) {
        form.delete(route("customers.destroy", customer.id));
    }
};
</script>

<template>
    <Head title="Customers" />
    <AppLayout>
        <div
            class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-surface-900">
                    Customers
                </h1>
                <p class="text-sm text-surface-500 mt-1">
                    Manage your customer database and contact info.
                </p>
            </div>
            <Link
                :href="route('customers.create')"
                class="metronic-btn metronic-btn-primary shrink-0"
            >
                <Plus class="w-4 h-4" /> Add Customer
            </Link>
        </div>

        <Card>
            <template #header>
                <div class="relative w-full max-w-sm">
                    <label for="customer-search" class="sr-only"
                        >Search customers</label
                    >
                    <div
                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-surface-400"
                    >
                        <Search class="w-4 h-4" />
                    </div>
                    <input
                        id="customer-search"
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search by name, email, or phone..."
                        class="w-full rounded-lg border border-surface-200 bg-white pl-9 pr-8 py-2 text-sm text-surface-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500"
                    />
                    <button
                        v-if="searchQuery"
                        @click="clearSearch"
                        aria-label="Clear customer search"
                        title="Clear search"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-surface-400 hover:text-surface-600"
                    >
                        <X class="w-4 h-4" />
                    </button>
                </div>
            </template>

            <DataTable
                :headers="['Name', 'Contact Info', 'Orders', 'Joined Date', 'Actions']"
                :items="customers.data"
                emptyMessage="No customers found matching your criteria."
            >
                <template #rows="{ items }">
                    <tr
                        v-for="customer in items"
                        :key="customer.id"
                        class="hover:bg-surface-50/50 transition-colors"
                    >
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-xs shrink-0"
                                >
                                    {{
                                        (customer.first_name?.[0] || "") +
                                        (customer.last_name?.[0] || "")
                                    }}
                                </div>
                                <span class="font-bold text-surface-900">{{
                                    customer.name
                                }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-surface-600">
                            <div v-if="customer.email" class="text-sm">
                                <a
                                    :href="'mailto:' + customer.email"
                                    class="hover:text-primary-600"
                                    >{{ customer.email }}</a
                                >
                            </div>
                            <div v-if="customer.phone" class="text-sm mt-0.5">
                                <a
                                    :href="'tel:' + customer.phone"
                                    class="hover:text-primary-600"
                                    >{{ customer.phone }}</a
                                >
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <Link
                                v-if="customer.orders_count"
                                :href="route('orders.index', { customer_id: customer.id })"
                                :aria-label="`View ${customer.orders_count} orders for ${customer.name}`"
                                class="text-surface-600 font-semibold hover:text-primary-600 hover:underline"
                            >
                                {{ customer.orders_count }}
                            </Link>
                            <span v-else class="text-surface-400">0</span>
                        </td>
                        <td class="py-4 px-6 text-surface-500">
                            {{
                                new Date(
                                    customer.created_at,
                                ).toLocaleDateString()
                            }}
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <Link
                                    :href="route('customers.edit', customer.id)"
                                    aria-label="Edit customer"
                                    class="text-primary-600 hover:text-primary-800 p-1"
                                >
                                    <Edit2 class="w-4 h-4" />
                                </Link>
                                <button
                                    @click="deleteCustomer(customer)"
                                    :disabled="form.processing"
                                    class="text-rose-500 hover:text-rose-700 p-1 disabled:opacity-50"
                                >
                                    <span class="sr-only">Delete {{ customer.name }}</span>
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </DataTable>

            <Pagination :links="customers.links" />
        </Card>
    </AppLayout>
</template>
