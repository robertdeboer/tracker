<script setup lang="ts">
import { PropType } from "vue/dist/vue";
import { ref, shallowRef, watch } from "vue";
import Calendar from "primevue/calendar";
import Dialog from "primevue/dialog";
import { useToast } from "primevue/usetoast";
import { required } from "@vuelidate/validators";
import { greaterThanZero } from "@/Validators/Generic";
import { useVuelidate } from "@vuelidate/core";
import { isEmpty, isString } from "lodash";
import { TimeEntryInterface, TimeEntryWriteInterface } from "@/Types/TimeEntry";
import {
    createTimeEntry,
    getTimeEntry,
    updateTimeEntry,
} from "@/Queries/TimeEntry";
import TextInput from "@/Components/Jetstream/TextInput.vue";
import InputError from "@/Components/Jetstream/InputError.vue";
import InputLabel from "@/Components/Jetstream/InputLabel.vue";
import SuccessButton from "@/Components/Jetstream/SuccessButton.vue";
import DangerButton from "@/Components/Jetstream/DangerButton.vue";
import { DateTime } from "luxon";

const props = defineProps({
    showModal: {
        required: true,
        type: Number as PropType<number>,
    },
    timeEntryId: {
        required: false,
        type: Number as PropType<number | null>,
        default: null,
    },
    workItemId: {
        required: true,
        type: Number as PropType<number>,
    },
});
const emits = defineEmits(["updated"]);
const formTitle = shallowRef<string>("Create Time Entry");
const toast = useToast();
const timeEntryForm = ref<TimeEntryWriteInterface>({
    id: null,
    hours: null,
    date: DateTime.now().toFormat("yyyy-MM-dd"),
    note: null,
});
const timeEntryFormValidation = {
    hours: { required, greaterThanZero },
    date: { required },
};
const v$ = useVuelidate(timeEntryFormValidation, timeEntryForm);
const isVisible = shallowRef<boolean>(false);
/**
 * Cancel the modal
 */
const cancel = function () {
    isVisible.value = false;
    resetForm();
};
/**
 * Get time entry data
 */
const getData = async function () {
    if (isEmpty(props.timeEntryId)) {
        return;
    }
    try {
        const entry = await getTimeEntry(props.timeEntryId);
        resetForm(entry);
        isVisible.value = true;
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: error,
            life: 5000,
        });
    }
};
/**
 * Reset the form
 */
const resetForm = function (timeEntry?: TimeEntryInterface) {
    v$.value.$reset();
    timeEntryForm.value.id = timeEntry?.id ?? null;
    timeEntryForm.value.hours = timeEntry?.hours ?? null;
    const date = timeEntry?.date
        ? DateTime.fromSQL(timeEntry.date)
        : DateTime.now();
    timeEntryForm.value.date = date.toFormat("yyyy-MM-dd");
    timeEntryForm.value.note = timeEntry?.note ?? null;
    formTitle.value = isEmpty(timeEntryForm.value.id)
        ? "Create Time Entry"
        : "Edit Time Entry";
};
/**
 * Save the time entry
 */
const save = async function () {
    try {
        if (!isString(timeEntryForm.value.date)) {
            timeEntryForm.value.date = DateTime.fromJSDate(
                timeEntryForm.value.date,
                {
                    zone: "utc",
                },
            ).toFormat("yyyy-MM-dd");
        }
        if (isEmpty(timeEntryForm.value.id)) {
            // eslint-disable-next-line @typescript-eslint/no-unused-vars
            const { id, ...form } = timeEntryForm.value;
            await createTimeEntry(form, props.workItemId);
        } else {
            await updateTimeEntry(timeEntryForm.value);
        }
        isVisible.value = false;
        resetForm();
        emits("updated");
        toast.add({
            severity: "success",
            summary: "Success",
            detail: "Time Entry Saved",
            life: 2000,
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
watch(
    () => props.showModal,
    () => {
        getData();
        isVisible.value = true;
    },
);
watch(
    () => props.timeEntryId,
    () => getData(),
);
</script>

<template>
    <Dialog
        :visible="isVisible"
        :style="{ width: '40vw' }"
        modal
        @update:visible="cancel"
    >
        <h3 class="text-2xl font-bold dark:text-white mb-2">{{ formTitle }}</h3>
        <div class="m-3">
            <InputLabel for="hours"
                >{{ $t(`form.time_entry.hours`) }}
                <span class="text-red-800">*</span></InputLabel
            >
            <TextInput
                id="hours"
                v-model.number="timeEntryForm.hours"
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
            <InputLabel for="date"
                >{{ $t(`form.time_entry.date`) }}
                <span class="text-red-800">*</span></InputLabel
            >
            <Calendar
                id="date"
                v-model="timeEntryForm.date"
                :class="{
                    'p-invalid': v$.date.$error,
                }"
                date-format="yy-mm-dd"
            />
            <InputError
                message="This is required."
                class="mt-2"
                :hidden="v$.date.$errors.length < 1"
            />
        </div>
        <div class="m-3">
            <InputLabel for="date">{{ $t(`form.time_entry.note`) }}</InputLabel>
            <TextInput
                id="note"
                v-model.trim="timeEntryForm.note"
                type="text"
                :class="{
                    'mt-1': true,
                    block: true,
                    'w-full': true,
                }"
            />
        </div>
        <template #footer>
            <SuccessButton class="mr-1" @click="save"> Save</SuccessButton>
            <DangerButton @click="cancel"> Cancel</DangerButton>
        </template>
    </Dialog>
</template>
