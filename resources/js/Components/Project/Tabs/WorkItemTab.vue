<script setup lang="ts">
import { PropType, shallowRef, watch } from "vue";
import SuccessButton from "@/Components/Jetstream/SuccessButton.vue";
import WorkItemModal from "@/Components/WorkItem/WorkItemModal.vue";
import WorkItemTable from "@/Components/WorkItem/WorkItemTable.vue";
import { DateTime } from "luxon";

const props = defineProps({
    projectId: {
        type: Number,
        required: true,
    },
    startDate: {
        type: DateTime,
        required: true,
    },
    endDate: {
        type: DateTime,
        required: true,
    },
    showModal: {
        type: Number as PropType<number>,
        required: false,
        default: 0,
    },
});
const emits = defineEmits(["updated", "view-hours"]);
const refresh = shallowRef<number>(0);
const showWorkItemModel = shallowRef<number>(0);
const workItemId = shallowRef<number | null>(null);
/**
 * Edit a work item
 * @param id<number>
 */
const editWorkItem = function (id: number) {
    workItemId.value = id;
    showWorkItemModel.value++;
};
/**
 * Cancel the work item modal
 */
const cancelWorkItemModal = function () {
    workItemId.value = null;
};
/**
 * A work item has been created or edited
 */
const workItemUpdated = function () {
    workItemId.value = null;
    refresh.value++;
    emits("updated");
};
const viewHours = function (workItemId: number) {
    emits("view-hours", workItemId);
};
watch(
    () => props.showModal,
    () => refresh.value++,
);
</script>

<template>
    <div>
        <SuccessButton
            v-if="can('Edit Work Item')"
            class="ml-8 mb-2"
            @click="showWorkItemModel++"
        >
            <span class="pi pi-plus-circle mr-1" />
            {{ $t("page.work_item.button.create") }}
        </SuccessButton>
    </div>
    <WorkItemTable
        :project-id="projectId"
        class="w-full"
        :start-date="startDate"
        :end-date="endDate"
        :refresh="refresh"
        @removed="workItemUpdated"
        @edit="editWorkItem"
        @view-hours="viewHours"
    />
    <WorkItemModal
        :show-modal="showWorkItemModel"
        :project-id="props.projectId"
        :work-item-id="workItemId"
        @cancel="cancelWorkItemModal"
        @updated="refresh++"
    />
</template>
