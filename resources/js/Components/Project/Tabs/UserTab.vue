<script setup lang="ts">
import DangerButton from "@/Components/Jetstream/DangerButton.vue";
import Dialog from "primevue/dialog";
import Dropdown from "primevue/dropdown";
import InputError from "@/Components/Jetstream/InputError.vue";
import InputLabel from "@/Components/Jetstream/InputLabel.vue";
import ProjectUserTable from "@/Components/Project/ProjectUserTable.vue";
import SuccessButton from "@/Components/Jetstream/SuccessButton.vue";
import { integer, required } from "@vuelidate/validators";
import { onMounted, PropType, ref, shallowRef } from "vue";
import { assignProjectUser, getNonProjectUsers } from "@/Queries/ProjectUser";
import { useToast } from "primevue/usetoast";
import { useVuelidate } from "@vuelidate/core";
import { DropdownInterface } from "@/Types/Page";

const props = defineProps({
    projectId: {
        type: Number as PropType<number>,
        required: true,
    },
});
const assignUserForm = ref({
    user_id: null,
});
const assignUserFormRules = {
    user_id: { required, integer },
};
const refresh = shallowRef<number>(0);
const showModal = shallowRef<boolean>(false);
const toast = useToast();
const users = ref<Array<DropdownInterface>>([]);
const v$ = useVuelidate(assignUserFormRules, assignUserForm);
/**
 * Cancel the modal
 */
const cancelModal = function () {
    showModal.value = false;
};
/**
 * Get component data
 */
const getAvailableUsers = async function () {
    try {
        const result = await getNonProjectUsers(props.projectId);
        users.value = [];
        result.forEach((user) => {
            users.value.push({
                code: user.id,
                name: `${user.first_name} ${user.last_name} - ${user.roles[0].name}`,
            });
        });
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: error,
            life: 5000,
        });
    }
};
const resetForm = function () {
    assignUserForm.value.user_id = null;
    v$.value.$reset();
};
/**
 * Save the modal
 */
const save = async function () {
    await v$.value.$validate();
    if (v$.value.$errors.length) {
        return;
    }
    try {
        await assignProjectUser(props.projectId, assignUserForm.value.user_id);
        toast.add({ severity: "success", detail: "User assigned", life: 5000 });
        refresh.value++;
        showModal.value = false;
        resetForm();
        await getAvailableUsers();
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: error,
            life: 5000,
        });
    }
    showModal.value = false;
};
onMounted(() => {
    getAvailableUsers();
});
</script>
<template>
    <div>
        <div>
            <SuccessButton
                v-if="can('Edit Project')"
                class="ml-8 mb-2"
                @click="showModal = true"
            >
                <span class="pi pi-plus-circle mr-1" />
                {{ $t("page.user.button.assign") }}
            </SuccessButton>
        </div>
        <ProjectUserTable
            :project-id="props.projectId"
            :refresh="refresh"
            @removed="getAvailableUsers"
        />
        <Dialog
            :visible="showModal"
            :style="{ width: '40vw' }"
            modal
            @update:visible="cancelModal"
        >
            <form id="assign-project-id" @submit.prevent>
                <h3 class="text-3xl font-bold dark:text-white mb-2">
                    Assign User
                </h3>
                <div class="m-3">
                    <InputLabel for="project_customer"
                        >User <span class="text-red-800">*</span></InputLabel
                    >
                    <Dropdown
                        v-model="assignUserForm.user_id"
                        :options="users"
                        option-label="name"
                        option-value="code"
                        style="width: 100%"
                        :class="{
                            'border-red-600': v$.user_id.$error,
                        }"
                    />
                    <InputError
                        message="This is required."
                        class="mt-2"
                        :hidden="v$.user_id.$errors.length < 1"
                    />
                </div>
            </form>
            <template #footer>
                <SuccessButton class="mr-1" @click="save"> Save </SuccessButton>
                <DangerButton @click="cancelModal"> Cancel </DangerButton>
            </template>
        </Dialog>
    </div>
</template>
