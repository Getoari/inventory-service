<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'
import { Link } from '@inertiajs/vue3'

const products = ref([])
const loading = ref(true)
const error = ref(null)

const fetchProducts = async () => {
    loading.value = true
    error.value = null

    try {
        const response = await axios.get('/api/products')

        products.value = response.data.data
    } catch (err) {
        error.value = 'Unable to load products.'
        console.error(err)
    } finally {
        loading.value = false
    }
}

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
    </div>
</template>