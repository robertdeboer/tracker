<script setup lang="ts">
import Accordion from "primevue/accordion";
import AccordionTab from "primevue/accordiontab";
import Dialog from "primevue/dialog";
import { onMounted, ref, watch } from "vue";
import { WorkItemInterface, WorkItemWriteInterface } from "@/Types/WorkItem";
import { integer, required, url } from "@vuelidate/validators";
import { isBoolean, ticketId } from "@/Validators/Generic";
import { useToast } from "primevue/usetoast";
import { useVuelidate } from "@vuelidate/core";
import { isNull } from "lodash";
import InputError from "@/Components/Jetstream/InputError.vue";
import InputLabel from "@/Components/Jetstream/InputLabel.vue";
import TextInput from "@/Components/Jetstream/TextInput.vue";
import Calendar from "primevue/calendar";
import InputSwitch from "primevue/inputswitch";
import { UserInterface } from "@/Types/User";
import Dropdown from "primevue/dropdown";
import SuccessButton from "@/Components/Jetstream/SuccessButton.vue";
import DangerButton from "@/Components/Jetstream/DangerButton.vue";
import {
    createWorkItem,
    getAvailableWorkItemOwners,
    getWorkItem,
    updateWorkItem,
} from "@/Queries/WorkItem";
import { DropdownInterface } from "@/Types/Page";
import { DateTime } from "luxon";

const props = defineProps({
    showModal: {
        default: 0,
        required: false,
        type: Number,
    },
    projectId: {
        type: Number,
        required: true,
    },
    workItemId: {
        type: Number,
        required: false,
        default: null,
    },
});
const emits = defineEmits(["cancel", "updated"]);
const form = ref<WorkItemWriteInterface>({
    id: null,
    name: null,
    project_id: props.projectId,
    owner_id: null,
    is_open: true,
    start_date: DateTime.now().toFormat("yyyy-MM-dd"),
    end_date: null,
    ticket_data: {
        id: null,
        url: null,
    },
});
const rules = {
    name: { required },
    project_id: { required, integer },
    owner_id: { required, integer },
    is_open: { required, isBoolean },
    start_date: { required },
    ticket_data: {
        id: { ticketId },
        url: { url },
    },
};
const owners = ref<Array<DropdownInterface>>([]);
const title = ref<string>("Create a Work Item.");
const toast = useToast();
const isVisible = ref<boolean>(false);
const v$ = useVuelidate(rules, form);
/**
 * Cancel the modal
 */
const cancel = function () {
    isVisible.value = false;
    emits("cancel");
    resetForm();
    v$.value.$reset();
};
/**
 * Get order data
 */
const getData = async function () {
    try {
        const list = await getAvailableWorkItemOwners();
        let final = <Array<DropdownInterface>>[];
        list.forEach((user: UserInterface) => {
            final.push({
                code: user.id,
                name: user.first_name + " " + user.last_name,
            });
        });
        owners.value = final;
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
 * Reset the form
 */
const resetForm = function (workItem?: WorkItemInterface) {
    form.value.id = workItem?.id ?? null;
    form.value.name = workItem?.name ?? null;
    form.value.project_id = props.projectId;
    form.value.owner_id = workItem?.owner?.id ?? null;
    form.value.is_open = workItem?.is_open ?? true;
    const startDate = workItem?.start_date
        ? DateTime.fromSQL(workItem.start_date)
        : DateTime.now();
    form.value.start_date = startDate.toFormat("yyyy-MM-dd");
    form.value.end_date = workItem?.end_date
        ? DateTime.fromSQL(workItem.end_date).toFormat("yyyy-MM-dd")
        : null;
    form.value.ticket_data.id = workItem?.ticket_data?.id ?? null;
    form.value.ticket_data.url = workItem?.ticket_data?.url ?? null;
};
/**
 * Save the order
 */
const save = async function () {
    await v$.value.$validate();
    if (v$.value.$errors.length) {
        return;
    }
    const start_date = new Date(form.value.start_date).toISOString();
    form.value.start_date = DateTime.fromISO(start_date).toFormat("yyyy-MM-dd");
    if (!isNull(form.value.end_date)) {
        const end_date = new Date(form.value.end_date).toISOString();
        form.value.end_date = DateTime.fromISO(end_date).toFormat("yyyy-MM-dd");
    }
    const { id, ...workItem } = form.value;
    try {
        let message = "Work Item Updated.";
        if (isNull(id)) {
            await createWorkItem(workItem);
            message = "Work Item Created.";
        } else {
            await updateWorkItem({
                id: id,
                ...workItem,
            });
        }
        isVisible.value = false;
        toast.add({
            severity: "success",
            summary: "Success",
            detail: message,
            life: 15000,
        });
        emits("updated", { refresh: true });
        resetForm();
        v$.value.$reset();
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
 * Get all required data
 */
onMounted(() => {
    getData();
});
/**
 * Watchers
 */
watch(
    () => props.showModal,
    () => {
        isVisible.value = true;
    },
);
watch(
    () => props.projectId,
    async (newProjectId: number) => {
        form.value.project_id = newProjectId;
        try {
            await getData();
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
    () => props.workItemId,
    async (newWorkItemId: number | null) => {
        if (isNull(newWorkItemId)) {
            resetForm();
            title.value = "Create a Work Item.";
            return;
        }
        try {
            const workItem = await getWorkItem(newWorkItemId);
            resetForm(workItem);
            title.value = `Update ${workItem.name}`;
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
</script>

<template>
    <Dialog
        :visible="isVisible"
        :style="{ width: '40vw' }"
        modal
        @update:visible="cancel"
    >
        <form id="editWorkItem" @submit.prevent>
            <h3 class="text-2xl font-bold dark:text-white mb-2">{{ title }}</h3>
            <div class="m-3">
                <InputLabel for="name"
                    >{{ $t("form.work_item.name") }}
                    <span class="text-red-800">*</span></InputLabel
                >
                <TextInput
                    id="name"
                    v-model.trim="form.name"
                    type="text"
                    :class="{
                        'mt-1': true,
                        block: true,
                        'w-full': true,
                        'border-red-600': v$.name.$error,
                    }"
                />
                <InputError
                    message="This is required and may only contain letters, numbers, spaces, dashes, and underscores."
                    class="mt-2"
                    :hidden="v$.name.$errors.length < 1"
                />
            </div>
            <div class="m-3">
                <InputLabel for="is_open"
                    >{{ $t("form.work_item.is_open") }}
                    <span class="text-red-800">*</span></InputLabel
                >
                <InputSwitch v-model="form.is_open" name="is_open" />
            </div>
            <div class="m-3">
                <InputLabel for="start_date"
                    >{{ $t("form.work_item.start") }}
                    <span class="text-red-800">*</span></InputLabel
                >
                <Calendar
                    id="start_date"
                    v-model="form.start_date"
                    :class="{
                        'p-invalid': v$.start_date.$error,
                    }"
                    date-format="yy-mm-dd"
                />
                <InputError
                    message="This is required."
                    class="mt-2"
                    :hidden="v$.start_date.$errors.length < 1"
                />
            </div>
            <div class="m-3">
                <InputLabel for="end_date">{{
                    $t("form.work_item.end")
                }}</InputLabel>
                <Calendar
                    id="end_date"
                    v-model="form.end_date"
                    date-format="yy-mm-dd"
                    mode="date"
                />
            </div>
            <div class="m-3">
                <InputLabel for="owner_id"
                    >{{ $t("form.work_item.owner") }}
                    <span class="text-red-800">*</span></InputLabel
                >
                <Dropdown
                    v-model="form.owner_id"
                    :options="owners"
                    option-label="name"
                    placeholder="Select an Owner"
                    option-value="code"
                    :class="{
                        'w-full': true,
                        'border-red-600': v$.owner_id.$error,
                    }"
                />
                <InputError
                    message="This is required."
                    class="mt-2"
                    :hidden="v$.owner_id.$errors.length < 1"
                />
            </div>
            <Accordion>
                <AccordionTab :header="$t(`form.work_item.ticket`)">
                    <div class="m-3">
                        <InputLabel for="ticket-id">{{
                            $t("form.work_item.ticket_id")
                        }}</InputLabel>
                        <TextInput
                            id="ticket-id"
                            v-model.trim="form.ticket_data.id"
                            type="text"
                            :class="{
                                'mt-1': true,
                                block: true,
                                'w-full': true,
                                'border-red-600': v$.ticket_data.id.$error,
                            }"
                        />
                        <InputError
                            message="This is may only contain letters, numbers, spaces, dashes, periods, and underscores."
                            class="mt-2"
                            :hidden="v$.ticket_data.id.$errors.length < 1"
                        />
                    </div>
                    <div class="m-3">
                        <InputLabel for="ticket-url">{{
                            $t("form.work_item.ticket_url")
                        }}</InputLabel>
                        <TextInput
                            id="ticket-url"
                            v-model.trim="form.ticket_data.url"
                            type="url"
                            :class="{
                                'mt-1': true,
                                block: true,
                                'w-full': true,
                                'border-red-600': v$.ticket_data.url.$error,
                            }"
                        />
                        <InputError
                            message="This is must be a valid URL"
                            class="mt-2"
                            :hidden="v$.ticket_data.url.$errors.length < 1"
                        />
                    </div>
                </AccordionTab>
            </Accordion>
        </form>
        <template #footer>
            <SuccessButton class="mr-1" @click="save"> Save</SuccessButton>
            <DangerButton @click="cancel"> Cancel</DangerButton>
        </template>
    </Dialog>
</template>
