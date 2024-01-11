<script lang="ts" setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import OrderModal from "@/Components/Order/OrderModal.vue";
import { PropType, shallowRef } from "vue";
import OrderTable from "@/Components/Order/OrderTable.vue";
import SuccessButton from "@/Components/Jetstream/SuccessButton.vue";
import SecondaryButton from "@/Components/Jetstream/SecondaryButton.vue";

defineProps({
    title: {
        type: String as PropType<string>,
        required: true,
    },
});

const orderId = shallowRef<number | null>(null);
const refreshTable = shallowRef<number>(0);
const showModel = shallowRef<number>(0);

/**
 * Create an order
 */
const createOrder = function () {
    orderId.value = null;
    showModel.value++;
};
/**
 * Edit an order
 * @param id
 */
const editOrder = function (id: number) {
    orderId.value = id;
    showModel.value++;
};
</script>

<template>
    <AppLayout :title="title">
        <div class="py-12">
            <div class="sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                        <div class="flex justify-content-between mb-3">
                            <SuccessButton
                                v-if="can('Edit Orders')"
                                class="mr-1"
                                @click="createOrder"
                            >
                                <span class="pi pi-plus-circle mr-1" />
                                Create
                            </SuccessButton>
                            <SecondaryButton
                                class="mr-1"
                                @click="refreshTable++"
                            >
                                <span class="pi pi-refresh mr-1" />
                                Refresh
                            </SecondaryButton>
                        </div>
                        <OrderTable
                            :refresh="refreshTable"
                            @edit-order="editOrder"
                        />
                    </div>
                </div>
            </div>
            <OrderModal
                :show-modal="showModel"
                :order-id="orderId"
                @updated="refreshTable++"
            />
        </div>
    </AppLayout>
</template>
