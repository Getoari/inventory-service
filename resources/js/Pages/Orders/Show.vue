<script setup>
import { onMounted, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
    orderId: {
        type: [Number, String],
        required: true,
    },
})

const order = ref(null)
const loading = ref(true)
const error = ref(null)

const fetchOrder = async () => {
    try {
        const response = await axios.get(
            `/api/orders/${props.orderId}`
        )

        order.value = response.data.data
    } catch (err) {
        error.value = 'Unable to load the order.'
        console.error(err)
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    fetchOrder()
})
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">

            <!-- Loading -->
            <div
                v-if="loading"
                class="rounded-lg bg-white p-8 text-center shadow"
            >
                Loading order...
            </div>

            <!-- Error -->
            <div
                v-else-if="error"
                class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700"
            >
                {{ error }}
            </div>

            <!-- Order -->
            <div
                v-else-if="order"
                class="space-y-6"
            >
                <!-- Confirmation -->
                <div
                    class="rounded-lg bg-white p-8 text-center shadow"
                >
                    <div
                        class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100"
                    >
                        <span class="text-xl text-green-600">
                            ✓
                        </span>
                    </div>

                    <h1
                        class="mt-4 text-3xl font-bold text-gray-900"
                    >
                        Order Confirmed!
                    </h1>

                    <p class="mt-2 text-gray-600">
                        Order #{{ order.id }} has been successfully
                        placed.
                    </p>
                </div>

                <!-- Order items -->
                <div
                    class="overflow-hidden rounded-lg bg-white shadow"
                >
                    <div class="p-6">
                        <h2
                            class="text-lg font-semibold text-gray-900"
                        >
                            Order Details
                        </h2>
                    </div>

                    <div
                        class="divide-y divide-gray-200 border-t border-gray-200"
                    >
                        <div
                            v-for="item in order.items"
                            :key="item.id"
                            class="flex items-center justify-between p-6"
                        >
                            <div>
                                <p
                                    class="font-medium text-gray-900"
                                >
                                    {{ item.productName }}
                                </p>

                                <p
                                    class="mt-1 text-sm text-gray-500"
                                >
                                    {{ item.quantity }}
                                    ×
                                    €{{ Number(item.unitPrice).toFixed(2) }}
                                </p>
                            </div>

                            <p
                                class="font-semibold text-gray-900"
                            >
                                €{{ Number(item.subtotal).toFixed(2) }}
                            </p>
                        </div>
                    </div>

                    <!-- Total -->
                    <div
                        class="flex items-center justify-between border-t border-gray-200 p-6"
                    >
                        <span
                            class="text-lg font-semibold text-gray-900"
                        >
                            Total
                        </span>

                        <span
                            class="text-2xl font-bold text-gray-900"
                        >
                            €{{ Number(order.total).toFixed(2) }}
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-center">
                    <Link
                        :href="route('products.index')"
                        class="rounded-md bg-blue-600 px-6 py-3 font-semibold text-white transition hover:bg-blue-700"
                    >
                        Back to Products
                    </Link>
                </div>
            </div>

        </div>
    </div>
</template>