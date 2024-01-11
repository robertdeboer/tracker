<script setup lang="ts">
import Column from "primevue/column";
import ConfirmationModal from "@/Components/Jetstream/ConfirmationModal.vue";
import DangerButton from "@/Components/Jetstream/DangerButton.vue";
import DataTable from "primevue/datatable";
import SuccessButton from "@/Components/Jetstream/SuccessButton.vue";
import PrimaryButton from "@/Components/Jetstream/PrimaryButton.vue";
import { onMounted, PropType, reactive, ref, shallowRef, watch } from "vue";
import {
    deleteTimeEntry,
    getTimeEntries,
    rebateTimeEntry,
} from "@/Queries/TimeEntry";
import { useToast } from "primevue/usetoast";
import { TimeEntryInterface } from "@/Types/TimeEntry";
import { TablePaginationInterface, TableSortInterface } from "@/Types/Page";
import { isEmpty } from "lodash";
import { DateTime } from "luxon";

const props = defineProps({
    refreshTable: {
        required: false,
        type: Number as PropType<number>,
        default: 0,
    },
    workItemId: {
        required: true,
        type: Number as PropType<number>,
        default: 0,
    },
});
const emits = defineEmits(["updated", "edit"]);
const isTableLoading = ref<boolean>(false);
const pagination = reactive<TablePaginationInterface>({
    numberOfRows: 10,
    page: 0,
});
const sort = reactive<Array<TableSortInterface>>([
    {
        column: "ID",
        order: "ASC",
    },
]);
const timeEntries = ref<Array<TimeEntryInterface>>([]);
const timeEntryId = shallowRef<number | null>(null);
const totalEntries = shallowRef<number>(0);
const toast = useToast();
const showDeleteConfirmModal = shallowRef<boolean>(false);
const showRebateConfirmModal = shallowRef<boolean>(false);
const cancelDelete = function () {
    timeEntryId.value = null;
    showDeleteConfirmModal.value = false;
};
const cancelRebate = function () {
    timeEntryId.value = null;
    showRebateConfirmModal.value = false;
};
const confirmDelete = function (id: number) {
    timeEntryId.value = id;
    showDeleteConfirmModal.value = true;
};
const confirmRebate = function (id: number) {
    timeEntryId.value = id;
    showRebateConfirmModal.value = true;
};
const deleteEntry = async function () {
    showDeleteConfirmModal.value = false;
    if (isEmpty(timeEntryId.value)) {
        return;
    }
    try {
        await deleteTimeEntry(timeEntryId.value);
        timeEntryId.value = null;
        toast.add({
            severity: "success",
            summary: "success",
            detail: "Time entry deleted.",
            life: 2000,
        });
        await getEntries();
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
const getEntries = async function () {
    if (props.workItemId == 0) {
        return;
    }
    try {
        isTableLoading.value = true;
        const {
            data,
            paginatorInfo: { total },
        } = await getTimeEntries(
            props.workItemId,
            pagination.numberOfRows,
            pagination.page + 1,
            sort,
        );
        timeEntries.value = data;
        totalEntries.value = total;
        isTableLoading.value = false;
    } catch (error) {
        isTableLoading.value = false;
        toast.add({
            severity: "error",
            summary: "Error",
            detail: error,
            life: 5000,
        });
    }
};
const rebateEntry = async function () {
    showRebateConfirmModal.value = false;
    if (isEmpty(timeEntryId.value)) {
        return;
    }
    try {
        await rebateTimeEntry(timeEntryId.value);
        timeEntryId.value = null;
        toast.add({
            severity: "success",
            summary: "success",
            detail: "Time entry rebated.",
            life: 2000,
        });
        await getEntries();
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
 * Component load
 */
onMounted(() => {
    getEntries();
});
/**
 * On table pagination
 *
 * @param page
 * @param rows
 */
const onPage = function ({ page, rows }: { page: number; rows: number }) {
    pagination.page = page;
    pagination.numberOfRows = rows;
    getEntries();
};
/**
 * On table sorting
 *
 * @param sortField
 * @param sortOrder
 */
const onSort = function ({
    sortField,
    sortOrder,
}: {
    sortField: string;
    sortOrder: number;
}) {
    sort[0].column = sortField.toUpperCase();
    sort[0].order = sortOrder == 1 ? "ASC" : "DESC";
    getEntries();
};
watch(
    () => props.workItemId,
    () => getEntries(),
);
watch(
    () => props.refreshTable,
    () => getEntries(),
);
</script>

<template>
    <div>
        <ConfirmationModal
            :show="showDeleteConfirmModal"
            @close="showDeleteConfirmModal = false"
        >
            <template #content>
                Are you sure you want to delete this time entry?
            </template>
            <template #footer>
                <SuccessButton class="mr-1" @click="deleteEntry">
                    Delete
                </SuccessButton>
                <DangerButton @click="cancelDelete"> Cancel</DangerButton>
            </template>
        </ConfirmationModal>
        <ConfirmationModal
            :show="showRebateConfirmModal"
            @close="showRebateConfirmModal = false"
        >
            <template #content>
                Are you sure you want to rebate this time entry?
            </template>
            <template #footer>
                <SuccessButton class="mr-1" @click="rebateEntry">
                    Rebate
                </SuccessButton>
                <DangerButton @click="cancelRebate"> Cancel</DangerButton>
            </template>
        </ConfirmationModal>
        <DataTable
            :value="timeEntries"
            data-key="id"
            striped-rows
            table-style="min-width: 50rem"
            :scrollable="true"
            scroll-height="500px"
            lazy
            paginator
            :first="0"
            :rows="10"
            :total-records="totalEntries"
            :rows-per-page-options="[10, 20, 30]"
            :loading="isTableLoading"
            @page="onPage($event)"
            @sort="onSort($event)"
        >
            <template #empty>No Time Entries</template>
            <column field="id" :sortable="true" header="Entry" class="w-1">
            </column>
            <column
                field="author"
                :sortable="true"
                header="Author"
                class="w-40"
                sort-field="author_id"
            >
                <template
                    #body="{
                        data: {
                            author: { first_name, last_name },
                        },
                    }"
                >
                    {{ first_name }} {{ last_name }}
                </template>
            </column>
            <column field="hours" :sortable="true" header="Hours" class="w-1">
                <template #body="{ data: { hours } }">
                    {{ Math.abs(hours) }}
                </template>
            </column>
            <column field="date" :sortable="true" header="Date" class="w-40">
                <template #body="{ data: { date } }">
                    {{ DateTime.fromSQL(date).toFormat("yyyy-MM-dd") }}
                </template>
            </column>
            <column field="note" :sortable="false" header="Note"></column>
            <column
                field="actions"
                :sortable="false"
                header="Actions"
                class="w-96"
            >
                <template #body="{ data: { id, hours } }">
                    <SuccessButton
                        v-if="can('Edit Time Entry')"
                        class="mr-1"
                        @click="emits('edit', id)"
                    >
                        <span class="pi pi-pencil mr-1" />
                        {{ $t(`page.time.button.edit`) }}
                    </SuccessButton>
                    <DangerButton
                        v-if="can('Edit Time Entry')"
                        class="mr-1"
                        @click="confirmDelete(id)"
                    >
                        <span class="pi pi-trash mr-1" />
                        {{ $t(`page.time.button.delete`) }}
                    </DangerButton>
                    <PrimaryButton
                        v-if="can('Edit Time Entry') && hours > 0"
                        class="mr-1"
                        @click="confirmRebate(id)"
                    >
                        <span class="pi pi-refresh mr-1" />
                        {{ $t(`page.time.button.rebate`) }}
                    </PrimaryButton>
                </template>
            </column>
        </DataTable>
    </div>
</template>
