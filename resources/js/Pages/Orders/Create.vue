<script setup>
import { computed, onMounted, ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import axios from 'axios'

const products = ref([])
const quantities = ref({})
const loading = ref(true)
const submitting = ref(false)

const error = ref(null)
const validationErrors = ref({})

const fetchProducts = async () => {
    loading.value = true
    error.value = null

    try {
        const response = await axios.get('/api/products')

        products.value = response.data.data

        products.value.forEach((product) => {
            quantities.value[product.id] = 0
        })
    } catch (err) {
        error.value = 'Unable to load products.'
        console.error(err)
    } finally {
        loading.value = false
    }
}

const increaseQuantity = (product) => {
    const currentQuantity = quantities.value[product.id] || 0

    if (currentQuantity < product.stockQuantity) {
        quantities.value[product.id] = currentQuantity + 1
    }
}

const decreaseQuantity = (product) => {
    const currentQuantity = quantities.value[product.id] || 0

    if (currentQuantity > 0) {
        quantities.value[product.id] = currentQuantity - 1
    }
}

const orderItems = computed(() => {
    return products.value
        .filter((product) => {
            return (quantities.value[product.id] || 0) > 0
        })
        .map((product) => ({
            product_id: product.id,
            quantity: quantities.value[product.id],
        }))
})

const orderTotal = computed(() => {
    return products.value.reduce((total, product) => {
        const quantity = quantities.value[product.id] || 0

        return total + Number(product.price) * quantity
    }, 0)
})

const placeOrder = async () => {
    if (orderItems.value.length === 0) {
        error.value = 'Please select at least one product.'

        return
    }

    submitting.value = true
    error.value = null
    validationErrors.value = {}

    try {
        const response = await axios.post('/api/orders', {
            items: orderItems.value,
        })

        const orderId = response.data.data.id

        router.visit(route('orders.show', orderId))
    } catch (err) {
        if (err.response?.status === 422) {
            validationErrors.value =
                err.response.data.errors || {}

            error.value =
                err.response.data.message ||
                'Please check your order details.'
        } else if (err.response?.status === 409) {
            error.value =
                err.response.data.message ||
                'There is not enough stock available.'

            const productId = err.response.data.productId

            if (productId) {
                const product = products.value.find(
                    (product) => product.id === productId
                )

                if (product) {
                    product.stockQuantity =
                        err.response.data.available
                }
            }
        } else {
            error.value =
                'Something went wrong while creating the order.'
        }

        console.error(err)
    } finally {
        submitting.value = false
    }
}

onMounted(() => {
    fetchProducts()
})
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8">
                <Link
                    :href="route('products.index')"
                    class="text-sm font-medium text-blue-600 hover:text-blue-800"
                >
                    ← Back to Products
                </Link>

                <h1 class="mt-4 text-3xl font-bold text-gray-900">
                    Create Order
                </h1>

                <p class="mt-2 text-gray-600">
                    Select products and quantities for your order.
                </p>
            </div>

            <!-- Loading -->
            <div
                v-if="loading"
                class="rounded-lg bg-white p-8 text-center shadow"
            >
                Loading products...
            </div>

            <template v-else>

                <!-- Error -->
                <div
                    v-if="error"
                    class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-700"
                >
                    {{ error }}
                </div>

                <!-- Products -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="divide-y divide-gray-200">

                        <div
                            v-for="product in products"
                            :key="product.id"
                            class="flex items-center justify-between p-6"
                        >
                            <div>
                                <h2 class="font-semibold text-gray-900">
                                    {{ product.name }}
                                </h2>

                                <p class="mt-1 text-sm text-gray-500">
                                    {{ product.category }}
                                    · {{ product.sku }}
                                </p>

                                <p class="mt-2 font-medium text-gray-900">
                                    €{{ Number(product.price).toFixed(2) }}
                                </p>

                                <p
                                    class="mt-1 text-sm"
                                    :class="
                                        product.stockQuantity > 0
                                            ? 'text-green-600'
                                            : 'text-red-600'
                                    "
                                >
                                    Stock:
                                    {{ product.stockQuantity }}
                                </p>
                            </div>

                            <!-- Quantity controls -->
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    :disabled="
                                        quantities[product.id] === 0
                                    "
                                    class="h-9 w-9 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-50"
                                    @click="decreaseQuantity(product)"
                                >
                                    -
                                </button>

                                <span
                                    class="w-8 text-center font-medium"
                                >
                                    {{ quantities[product.id] || 0 }}
                                </span>

                                <button
                                    type="button"
                                    :disabled="
                                        quantities[product.id] >=
                                        product.stockQuantity
                                    "
                                    class="h-9 w-9 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-50"
                                    @click="increaseQuantity(product)"
                                >
                                    +
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Order summary -->
                <div
                    class="mt-6 flex items-center justify-between rounded-lg bg-white p-6 shadow"
                >
                    <div>
                        <p class="text-sm text-gray-500">
                            Order Total
                        </p>

                        <p class="text-2xl font-bold text-gray-900">
                            €{{ orderTotal.toFixed(2) }}
                        </p>
                    </div>

                    <button
                        type="button"
                        :disabled="
                            submitting ||
                            orderItems.length === 0
                        "
                        class="rounded-md bg-blue-600 px-6 py-3 font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                        @click="placeOrder"
                    >
                        <span v-if="submitting">
                            Placing Order...
                        </span>

                        <span v-else>
                            Place Order
                        </span>
                    </button>
                </div>

            </template>

        </div>
    </div>
</template>