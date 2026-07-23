<script setup>
import { onMounted, ref, watch } from 'vue'
import axios from 'axios'
import { Link } from '@inertiajs/vue3'

const products = ref([])
const loading = ref(true)
const error = ref(null)

const search = ref('')
const category = ref('')

const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
})

let searchTimeout = null

const fetchProducts = async (page = 1) => {
    loading.value = true
    error.value = null

    try {
        const response = await axios.get('/api/products', {
            params: {
                page,
                per_page: pagination.value.per_page,
                search: search.value || undefined
            },
        })

        products.value = response.data.data
        pagination.value = response.data.meta
    } catch (err) {
        error.value = 'Unable to load products.'
        console.error(err)
    } finally {
        loading.value = false
    }
}

const goToPage = (page) => {
    if (
        page < 1 ||
        page > pagination.value.last_page ||
        page === pagination.value.current_page
    ) {
        return
    }

    fetchProducts(page)
}

watch(search, () => {
    clearTimeout(searchTimeout)

    searchTimeout = setTimeout(() => {
        fetchProducts(1)
    }, 300)
})

watch(category, () => {
    fetchProducts(1)
})

onMounted(() => {
    fetchProducts()
})
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        Products
                    </h1>

                    <p class="mt-2 text-gray-600">
                        View available products and current stock levels.
                    </p>
                </div>

                <Link
                    :href="route('orders.create')"
                    class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700"
                >
                    Create Order
                </Link>
            </div>

            <!-- Filters -->
            <div class="mb-6 rounded-lg bg-white p-6 shadow">
                <div class="grid gap-4 sm:grid-cols-2">
                    <!-- Search -->
                    <div>
                        <label
                            for="search"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Search
                        </label>

                        <input
                            id="search"
                            v-model="search"
                            type="text"
                            placeholder="Search by name, SKU or category..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>
                </div>
            </div>

            <!-- Loading -->
            <div
                v-if="loading"
                class="rounded-lg bg-white p-8 text-center shadow"
            >
                <p class="text-gray-600">
                    Loading products...
                </p>
            </div>

            <!-- Error -->
            <div
                v-else-if="error"
                class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700"
            >
                {{ error }}
            </div>

            <!-- Products -->
            <div
                v-else
                class="overflow-hidden rounded-lg bg-white shadow"
            >
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Product
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    SKU
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Category
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Price
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    Stock
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr
                                v-for="product in products"
                                :key="product.id"
                            >
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="font-medium text-gray-900">
                                        {{ product.name }}
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                    {{ product.sku }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                    {{ product.category }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                    €{{ Number(product.price).toFixed(2) }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <span
                                        :class="[
                                            'inline-flex rounded-full px-2 py-1 text-xs font-semibold',
                                            product.stockQuantity > 0
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-red-100 text-red-800'
                                        ]"
                                    >
                                        {{ product.stockQuantity }}
                                    </span>
                                </td>
                            </tr>

                            <tr v-if="products.length === 0">
                                <td
                                    colspan="5"
                                    class="px-6 py-8 text-center text-gray-500"
                                >
                                    No products found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Pagination -->
        <div
            v-if="pagination.last_page > 1"
            class="flex items-center justify-between border-t border-gray-200 bg-white px-6 py-4"
        >
            <div class="text-sm text-gray-600">
                Showing
                <span class="font-medium">
                    {{ (pagination.current_page - 1) * pagination.per_page + 1 }}
                </span>
                to
                <span class="font-medium">
                    {{
                        Math.min(
                            pagination.current_page * pagination.per_page,
                            pagination.total
                        )
                    }}
                </span>
                of
                <span class="font-medium">
                    {{ pagination.total }}
                </span>
                products
            </div>

            <div class="flex gap-2">
                <button
                    type="button"
                    :disabled="pagination.current_page === 1"
                    class="rounded-md border border-gray-300 px-3 py-2 text-sm hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                    @click="goToPage(pagination.current_page - 1)"
                >
                    Previous
                </button>

                <button
                    type="button"
                    :disabled="
                        pagination.current_page === pagination.last_page
                    "
                    class="rounded-md border border-gray-300 px-3 py-2 text-sm hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                    @click="goToPage(pagination.current_page + 1)"
                >
                    Next
                </button>
            </div>
        </div>
    </div>
</template>