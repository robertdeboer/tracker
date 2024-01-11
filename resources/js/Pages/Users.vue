<script lang="ts" setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { PropType, shallowRef } from "vue";
import UserModel from "@/Components/User/UserModel.vue";
import UserTable from "@/Components/User/UserTable.vue";
import SuccessButton from "@/Components/Jetstream/SuccessButton.vue";
import SecondaryButton from "@/Components/Jetstream/SecondaryButton.vue";

defineProps({
    title: {
        type: String as PropType<string>,
        required: true,
    },
});

const userId = shallowRef<number | null>(null);
const refreshTable = shallowRef<number>(0);
const showModal = shallowRef<number>(0);

/**
 * Create a user
 */
const createUser = function () {
    userId.value = null;
    showModal.value++;
};
/**
 * Edit an order
 * @param id
 */
const editUser = function (id: number) {
    userId.value = id;
    showModal.value++;
};
</script>

<template>
    <AppLayout :title="title">
        <div class="py-12">
            <div class="sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                        <div class="flex justify-content-between mb-3">
                            <SuccessButton class="mr-1" @click="createUser">
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
                        <UserTable
                            :refresh="refreshTable"
                            @edit-user="editUser"
                        />
                    </div>
                </div>
            </div>
            <UserModel
                :show-modal="showModal"
                :user-id="userId"
                @updated="refreshTable++"
            />
        </div>
    </AppLayout>
</template>
