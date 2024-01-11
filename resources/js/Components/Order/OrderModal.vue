<script setup lang="ts">
import Calendar from "primevue/calendar";
import Dialog from "primevue/dialog";
import DangerButton from "@/Components/Jetstream/DangerButton.vue";
import Dropdown from "primevue/dropdown";
import InputError from "@/Components/Jetstream/InputError.vue";
import InputLabel from "@/Components/Jetstream/InputLabel.vue";
import TextInput from "@/Components/Jetstream/TextInput.vue";
import SuccessButton from "@/Components/Jetstream/SuccessButton.vue";
import { CreateOrder, GetOrder, UpdateOrder } from "@/Queries/Order";
import {
    decimal,
    email,
    integer,
    minLength,
    required,
} from "@vuelidate/validators";
import { greaterThanZero, orderReferenceNumber } from "@/Validators/Generic";
import { GetProjectNamesOptionList } from "@/Queries/Project";
import { onMounted, ref, shallowRef, watch } from "vue";
import { useVuelidate } from "@vuelidate/core";
import { useToast } from "primevue/usetoast";
import { OrderInterface, OrderWriteInterface } from "@/Types/Order";
import { isEmpty, isNull, isString } from "lodash";
import { DropdownInterface } from "@/Types/Page";
import { DateTime } from "luxon";
import { PropType } from "vue/dist/vue";

const props = defineProps({
    showModal: {
        required: false,
        type: Number as PropType<number>,
        default: 0,
    },
    title: {
        required: false,
        type: String as PropType<string>,
        default: "Create an Order",
    },
    orderId: {
        required: false,
        type: Number as PropType<number>,
        default: null,
    },
    projectId: {
        required: false,
        type: String as PropType<string>,
        default: null,
    },
});
const emits = defineEmits(["updated"]);
const form = ref<OrderWriteInterface>({
    id: null,
    email: null,
    reference_number: null,
    project_id: props.projectId ?? null,
    hours: 0,
    date: DateTime.now().toFormat("yyyy-MM-dd"),
});
const projects = ref<Array<DropdownInterface>>([]);
const rules = {
    email: { required, email, $autoDirty: true },
    reference_number: {
        required,
        minLength: minLength(5),
        orderReferenceNumber,
    },
    project_id: { required, integer },
    hours: { required, decimal, greaterThanZero },
    date: { required },
};
const toast = useToast();
const isVisible = shallowRef<boolean>(false);
const v$ = useVuelidate(rules, form);

/**
 * Cancel the model
 */
const cancel = function () {
    isVisible.value = false;
    resetForm();
};
/**
 * Get projects for the project dropdown
 */
const getProjectOptions = async function () {
    projects.value = await GetProjectNamesOptionList(false);
};
/**
 * Reset the form & validation
 */
const resetForm = function (order?: OrderInterface) {
    form.value.id = order?.id ?? null;
    form.value.email = order?.email ?? null;
    form.value.reference_number = order?.reference_number ?? null;
    form.value.project_id = order?.project?.id ?? props.projectId ?? null;
    form.value.hours = order?.hours ?? 0;
    const date = order?.date ? DateTime.fromSQL(order.date) : DateTime.now();
    form.value.date = date.toFormat("yyyy-MM-dd");
    v$.value.$reset();
};
/**
 * Save the form
 */
const save = async function () {
    await v$.value.$validate();
    if (v$.value.$errors.length) {
        return;
    }
    try {
        if (!isString(form.value.date)) {
            form.value.date = DateTime.fromJSDate(form.value.date, {
                zone: "utc",
            }).toFormat("yyyy-MM-dd");
        }
        let message = "Order Created.";
        if (isNull(form.value.id)) {
            await CreateOrder(form.value);
        } else {
            await UpdateOrder(form.value);
            message = "Order Updated";
        }
        toast.add({
            severity: "success",
            summary: "Success",
            detail: message,
            life: 15000,
        });
        isVisible.value = false;
        emits("updated");
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: error,
            life: 15000,
        });
    }
};
/**
 * When the model is first loaded, get the projects
 */
onMounted(() => {
    getProjectOptions();
});
/**
 * Watch for a changes
 * A change in the order id indicates if this is a new order or a current order
 */
watch(
    () => props.orderId,
    async (orderId: number | null) => {
        if (isEmpty(orderId)) {
            resetForm();
            return;
        }
        try {
            const order = await GetOrder(orderId);
            resetForm(order);
        } catch (error) {
            toast.add({
                severity: "error",
                summary: "Error",
                detail: error,
                life: 15000,
            });
        }
    },
);
watch(
    () => props.showModal,
    () => {
        isVisible.value = true;
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
        <form id="editOrder" @submit.prevent>
            <h3 class="text-3xl font-bold dark:text-white mb-2">{{ title }}</h3>
            <div class="m-3">
                <InputLabel for="reference_number">
                    {{ $t("form.order.number") }}
                    <span class="text-red-800">*</span></InputLabel
                >
                <TextInput
                    id="reference_number"
                    ref="reference_numberInput"
                    v-model.trim="form.reference_number"
                    type="text"
                    :class="{
                        'mt-1': true,
                        block: true,
                        'w-full': true,
                        'border-red-600': v$.reference_number.$error,
                    }"
                />
                <InputError
                    message="This is required and may only contain letters, numbers, spaces, dashes, and underscores."
                    class="mt-2"
                    :hidden="v$.reference_number.$errors.length < 1"
                />
            </div>
            <div class="m-3">
                <InputLabel for="email"
                    >{{ $t("form.order.email") }}
                    <span class="text-red-800">*</span></InputLabel
                >
                <TextInput
                    id="email"
                    ref="emailInput"
                    v-model="form.email"
                    type="email"
                    :class="{
                        'mt-1': true,
                        block: true,
                        'w-full': true,
                        'border-red-600': v$.reference_number.$error,
                    }"
                />
                <InputError
                    message="This is required and must be a valid email."
                    class="mt-2"
                    :hidden="v$.email.$errors.length < 1"
                />
            </div>
            <div class="m-3">
                <InputLabel for="hours"
                    >{{ $t("form.order.hours") }}
                    <span class="text-red-800">*</span></InputLabel
                >
                <TextInput
                    id="hours"
                    ref="hoursInput"
                    v-model.number="form.hours"
                    type="number"
                    :class="{
                        'mt-1': true,
                        block: true,
                        'w-full': true,
                        'border-red-600': v$.hours.$error,
                    }"
                />
                <InputError
                    message="This is required and must be greater than zero."
                    class="mt-2"
                    :hidden="v$.hours.$errors.length < 1"
                />
            </div>
            <div class="m-3">
                <InputLabel for="date">
                    {{ $t("form.order.date") }}
                    <span class="text-red-800">*</span></InputLabel
                >
                <Calendar
                    id="date"
                    ref="dateInput"
                    v-model="form.date"
                    :class="{
                        'p-invalid': v$.date.$error,
                    }"
                    date-format="yy-mm-dd"
                />
                <InputError
                    message="This is required."
                    class="mt-2"
                    :hidden="v$.hours.$errors.length < 1"
                />
            </div>
            <div class="m-3">
                <InputLabel for="project_id">
                    {{ $t("form.order.project") }}
                    <span class="text-red-800">*</span>
                </InputLabel>
                <Dropdown
                    v-model="form.project_id"
                    :options="projects"
                    option-label="name"
                    placeholder="Select a Project"
                    option-value="code"
                    :class="{
                        'w-full': true,
                        'border-red-600': v$.reference_number.$error,
                    }"
                />
                <InputError
                    message="This is required."
                    class="mt-2"
                    :hidden="v$.project_id.$errors.length < 1"
                />
            </div>
        </form>
        <template #footer>
            <SuccessButton class="mr-1" @click="save"> Save </SuccessButton>
            <DangerButton @click="cancel"> Cancel </DangerButton>
        </template>
    </Dialog>
</template>
