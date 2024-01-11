<script setup lang="ts">
import { PropType, shallowRef } from "vue";
import SuccessButton from "@/Components/Jetstream/SuccessButton.vue";
import TimeEntryTable from "@/Components/TimeEntry/TimeEntryTable.vue";
import TimeEntryModal from "@/Components/TimeEntry/TimeEntryModal.vue";

const props = defineProps({
    projectId: {
        required: true,
        type: Number as PropType<number>,
    },
    workItemId: {
        required: false,
        type: Number as PropType<number>,
        default: null,
    },
});
const emits = defineEmits(["updated", "close"]);
const tableRefresh = shallowRef<number>(0);
const showModal = shallowRef<number>(0);
const timeEntryId = shallowRef<number | null>(null);
const createTimeEntry = function () {
    timeEntryId.value = null;
    showModal.value++;
};
const editTimeEntry = function (id: number) {
    timeEntryId.value = id;
    showModal.value++;
};
</script>

<template>
    <div>
        <div>
            <SuccessButton
                v-if="can('Edit Time Entry')"
                class="ml-8 mb-2"
                @click="createTimeEntry"
            >
                <span class="pi pi-plus-circle mr-1" />
                {{ $t(`page.time.button.create`) }}
            </SuccessButton>
        </div>
        <TimeEntryTable
            :refresh-table="tableRefresh"
            :work-item-id="props.workItemId"
            @updated="emits('updated')"
            @edit="editTimeEntry"
        />
        <TimeEntryModal
            :show-modal="showModal"
            :time-entry-id="timeEntryId"
            :work-item-id="props.workItemId"
            @updated="
                tableRefresh++;
                emits('updated');
            "
        />
    </div>
</template>
