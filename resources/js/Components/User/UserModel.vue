<script setup lang="ts">
import Dialog from "primevue/dialog";
import DangerButton from "@/Components/Jetstream/DangerButton.vue";
import Dropdown from "primevue/dropdown";
import InputError from "@/Components/Jetstream/InputError.vue";
import InputLabel from "@/Components/Jetstream/InputLabel.vue";
import TextInput from "@/Components/Jetstream/TextInput.vue";
import SuccessButton from "@/Components/Jetstream/SuccessButton.vue";
import { email, integer, minLength, required } from "@vuelidate/validators";
import { onMounted, PropType, ref, shallowRef, watch } from "vue";
import { useVuelidate } from "@vuelidate/core";
import { useToast } from "primevue/usetoast";
import { isEmpty, isNull } from "lodash";
import { UserWriteInterface } from "@/Types/User";
import { RoleOptionsInterface } from "@/Types/Permission";
import { CreateUser, GetUser, UpdateUser } from "@/Queries/User";
import { assingRoleToUser, getRoleOptions } from "@/Queries/Permissions";

const emits = defineEmits(["updated"]);
const props = defineProps({
    showModal: {
        required: true,
        type: Number as PropType<number>,
        default: 0,
    },
    title: {
        required: false,
        type: String as PropType<string>,
        default: "Create a User",
    },
    userId: {
        required: false,
        type: Number as PropType<number | null>,
        default: null,
    },
});

const form = ref<UserWriteInterface>({
    id: null,
    first_name: null,
    last_name: null,
    email: null,
    role_id: null,
});
const isVisible = shallowRef<boolean>(false);
const roles = ref<Array<RoleOptionsInterface>>([]);
const rules = {
    first_name: { required, minLength: minLength(2) },
    last_name: { required, minLength: minLength(2) },
    email: { required, email, $autoDirty: true },
    role_id: { required, integer },
};
const toast = useToast();
const v$ = useVuelidate(rules, form);

/**
 * Cancel the model
 */
const cancel = function () {
    isVisible.value = false;
    resetForm();
};
/**
 * Reset the form & validation
 */
const resetForm = function () {
    form.value.id = null;
    form.value.first_name = null;
    form.value.last_name = null;
    form.value.email = null;
    form.value.role_id = null;
    v$.value.$reset();
};

const getData = async function () {
    roles.value = await getRoleOptions();
};
/**
 * Save the form
 */
const save = async function () {
    await v$.value.$validate();
    if (v$.value.$errors.length) {
        return;
    }
    let message = "User Created.";
    try {
        const { id, ...data } = form.value;
        if (isNull(id)) {
            const { id } = await CreateUser(data);
            form.value.id = id;
        } else {
            await UpdateUser(form.value);
            message = "User Updated.";
        }
        await assingRoleToUser(form.value.id, form.value.role_id);
        isVisible.value = false;
        toast.add({
            severity: "success",
            summary: "Success",
            detail: message,
            life: 1500,
        });
        resetForm();
        emits("updated");
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: error,
            life: 1500,
        });
    }
};
/**
 * When the model is first loaded, get the projects
 */
onMounted(() => {
    getData();
});
/**
 * Watch for a changes
 * A change in the order id indicates if this is a new order or a current order
 */
watch(
    () => props.showModal,
    () => (isVisible.value = true),
);
watch(
    () => props.userId,
    async (first: number | null) => {
        if (isEmpty(first)) {
            resetForm();
            return;
        }
        try {
            const user = await GetUser(first);
            form.value.id = user.id;
            form.value.email = user.email;
            form.value.first_name = user.first_name;
            form.value.last_name = user.last_name;
            form.value.role_id =
                user.roles?.length > 0 ? user.roles[0].id : null;
        } catch (error) {
            toast.add({
                severity: "error",
                summary: "Error",
                detail: error,
                life: 15000,
            });
            return;
        }
    },
);
</script>

<template>
    <Dialog
        :visible="isVisible"
        :style="{ width: '40vw' }"
        modal
        @update:visible="cancel"
    >
        <form id="userOrder" @submit.prevent>
            <h3 class="text-3xl font-bold dark:text-white mb-2">{{ title }}</h3>
            <div class="m-3">
                <InputLabel for="first_name">
                    {{ $t("form.user.first_name") }}
                    <span class="text-red-800">*</span>
                </InputLabel>
                <TextInput
                    id="first_name"
                    ref="first_nameInput"
                    v-model.trim="form.first_name"
                    type="text"
                    :class="{
                        'mt-1': true,
                        block: true,
                        'w-full': true,
                        'border-red-600': v$.first_name.$error,
                    }"
                />
                <InputError
                    message="This is required and may only contain letters, numbers."
                    class="mt-2"
                    :hidden="v$.first_name.$errors.length < 1"
                />
            </div>
            <div class="m-3">
                <InputLabel for="last_name">
                    {{ $t("form.user.last_name") }}
                    <span class="text-red-800">*</span>
                </InputLabel>
                <TextInput
                    id="last_name"
                    ref="last_nameInput"
                    v-model.trim="form.last_name"
                    type="text"
                    :class="{
                        'mt-1': true,
                        block: true,
                        'w-full': true,
                        'border-red-600': v$.last_name.$error,
                    }"
                />
                <InputError
                    message="This is required and may only contain letters, numbers."
                    class="mt-2"
                    :hidden="v$.last_name.$errors.length < 1"
                />
            </div>
            <div class="m-3">
                <InputLabel for="email">
                    {{ $t("form.user.email") }}
                    <span class="text-red-800">*</span>
                </InputLabel>
                <TextInput
                    id="email"
                    ref="emailInput"
                    v-model="form.email"
                    type="email"
                    :class="{
                        'mt-1': true,
                        block: true,
                        'w-full': true,
                        'border-red-600': v$.email.$error,
                    }"
                />
                <InputError
                    message="This is required and must be a valid email."
                    class="mt-2"
                    :hidden="v$.email.$errors.length < 1"
                />
            </div>
            <div class="m-3">
                <InputLabel for="role_id">
                    {{ $t("form.user.role") }}
                    <span class="text-red-800">*</span>
                </InputLabel>
                <Dropdown
                    v-model="form.role_id"
                    :options="roles"
                    option-label="name"
                    placeholder="Select a Role"
                    option-value="code"
                    :class="{
                        'w-full': true,
                        'border-red-600': v$.role_id.$error,
                    }"
                />
                <InputError
                    message="This is required."
                    class="mt-2"
                    :hidden="v$.role_id.$errors.length < 1"
                />
            </div>
        </form>
        <template #footer>
            <SuccessButton class="mr-1" @click="save"> Save</SuccessButton>
            <DangerButton @click="cancel"> Cancel</DangerButton>
        </template>
    </Dialog>
</template>
