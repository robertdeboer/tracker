<script setup lang="ts">
import SuccessButton from "@/Components/Jetstream/SuccessButton.vue";
import OrderTable from "@/Components/Order/OrderTable.vue";
import OrderModal from "@/Components/Order/OrderModal.vue";
import { shallowRef } from "vue";
import { PropType } from "vue/dist/vue";

const props = defineProps({
    projectId: {
        type: Number as PropType<number>,
        required: true,
    },
});
const orderId = shallowRef<number | null>(null);
const refresh = shallowRef<number>(0);
const showModal = shallowRef<number>(0);
const editOrder = async function (id: number) {
    orderId.value = id;
    showModal.value++;
};
</script>

<template>
    <div>
        <SuccessButton
            v-if="can('Edit Orders')"
            class="ml-8 mb-2"
            @click="showModal++"
        >
            <span class="pi pi-plus-circle mr-1" />
            {{ $t(`page.order.button.create`) }}
        </SuccessButton>
    </div>
    <OrderTable
        :project-id="props.projectId"
        :refresh="refresh"
        :hide-project-column="true"
        @edit-order="editOrder"
    />
    <OrderModal
        :show-modal="showModal"
        :order-id="orderId"
        :project-id="props.projectId.toString()"
        @updated="refresh++"
    />
</template>
