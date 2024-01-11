<script setup lang="ts">
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import ConfirmationModal from "@/Components/Jetstream/ConfirmationModal.vue";
import { onMounted, PropType, reactive, ref, shallowRef, watch } from "vue";
import { WorkItemInterface } from "@/Types/WorkItem";
import { TablePaginationInterface, TableSortInterface } from "@/Types/Page";
import { debounce, isEmpty } from "lodash";
import { FilterMatchMode } from "primevue/api";
import { deleteWorkItem, getWorkItems } from "@/Queries/WorkItem";
import { useToast } from "primevue/usetoast";
import InputText from "primevue/inputtext";
import SuccessButton from "@/Components/Jetstream/SuccessButton.vue";
import DangerButton from "@/Components/Jetstream/DangerButton.vue";
import SecondaryButton from "@/Components/Jetstream/SecondaryButton.vue";
import { DateTime } from "luxon";
import Dropdown from "primevue/dropdown";

const props = defineProps({
    projectId: {
        type: Number as PropType<number>,
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
    refresh: {
        type: Number as PropType<number>,
        required: false,
        default: 0,
    },
});
const emits = defineEmits(["removed", "edit", "view-hours"]);
const filters = ref({
    name: { value: null, matchMode: FilterMatchMode.CONTAINS },
    is_open: { value: null, matchMode: FilterMatchMode.EQUALS },
});
const isLoading = shallowRef<boolean>(true);
const pagination = ref<TablePaginationInterface>({
    numberOfRows: 10,
    page: 0,
});
const showWorkItemConfirmation = shallowRef<boolean>(false);
const sort = reactive<Array<TableSortInterface>>([
    {
        column: "START_DATE",
        order: "ASC",
    },
]);
const toast = useToast();
const totalRecords = shallowRef<number>(0);
const workItems = ref<Array<WorkItemInterface>>([]);
const workItemDeleteId = shallowRef<number>();
/**
 * Delete a work item
 */
const removeWorkItem = async function () {
    showWorkItemConfirmation.value = false;
    if (isEmpty(workItemDeleteId.value)) {
        return;
    }
    try {
        await deleteWorkItem(workItemDeleteId.value);
        toast.add({
            severity: "success",
            summary: "Success",
            detail: "Work item removed.",
            life: 2000,
        });
        emits("removed");
    } catch (e) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: e,
            life: 5000,
        });
    }
};
/**
 * Edit a work item
 * @param id<number>
 */
const editWorkItem = function (id: number) {
    emits("edit", id);
};
/**
 * Get the work items for the given project
 */
const getData = async function () {
    isLoading.value = true;
    try {
        const {
            data,
            paginatorInfo: { perPage, total },
        } = await getWorkItems(
            pagination.value.numberOfRows,
            pagination.value.page + 1,
            {
                project_id: props.projectId,
                start_date: {
                    to: `${props.endDate.toFormat("yyyy-M-dd")} 23:59:59`,
                    from: `${props.startDate.toFormat("yyyy-M-dd")} 00:00:01`,
                },
            },
            sort,
            {
                name: { value: filters.value.name.value ?? null },
                is_open: { value: filters.value.is_open.value ?? null },
            },
        );
        workItems.value = data;
        pagination.value.numberOfRows = perPage;
        totalRecords.value = total;
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: error,
            life: 5000,
        });
    }
    isLoading.value = false;
};
/**
 * On table filtering
 */
const onFilter = debounce(() => {
    getData();
}, 500);
/**
 * On table pagination
 *
 * @param object
 */
const onPage = function ({ page, rows }: { page: number; rows: number }) {
    pagination.value.page = page;
    pagination.value.numberOfRows = rows;
    getData();
};
/**
 * On table sorting
 *
 * @param object
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
    getData();
};
const viewTimeEntries = function (workItemId: number) {
    emits("view-hours", workItemId);
};
onMounted(() => {
    getData();
});
watch(
    () => props.refresh,
    () => getData(),
);
</script>

<template>
    <div>
        <ConfirmationModal
            :show="showWorkItemConfirmation"
            @close="showWorkItemConfirmation = false"
        >
            <template #title> Confirm Work Item Delete</template>
            <template #content>
                Are you sure you want to delete this work item?
            </template>
            <template #footer>
                <DangerButton class="mr-1" @click="removeWorkItem()">
                    Yes
                </DangerButton>

                <SuccessButton
                    class="mr-1"
                    @click="
                        showWorkItemConfirmation = false;
                        workItemDeleteId = null;
                    "
                >
                    No
                </SuccessButton>
            </template>
        </ConfirmationModal>
        <DataTable
            v-model:filters="filters"
            :first="0"
            :global-filter-fields="['email', 'project', 'is_open']"
            :loading="isLoading"
            :rows="10"
            :row-per-page-option="[10, 20, 30]"
            :total-records="totalRecords"
            :value="workItems"
            data-key="id"
            filter-display="row"
            lazy
            paginator
            striped-rows
            :rows-per-page-options="[10, 20, 30]"
            @page="onPage"
            @sort="onSort"
        >
            <template #empty> No work items found.</template>
            <Column header="ID">
                <template
                    #body="{
                        data: {
                            ticket_data: { id },
                        },
                    }"
                >
                    {{ id }}
                </template>
            </Column>
            <Column
                field="name"
                header="Name"
                :sortable="true"
                :show-filter-menu="false"
            >
                <template
                    #body="{
                        data: {
                            name,
                            ticket_data: { url },
                        },
                    }"
                >
                    <a
                        v-if="url"
                        :href="url"
                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
                        target="_blank"
                        >{{ name }}</a
                    >
                    <span v-else>{{ name }}</span>
                </template>
                <template #filter="{ filterModel, filterCallback }">
                    <InputText
                        v-model.lazy.trim="filterModel.value"
                        type="text"
                        class="p-column-filter"
                        @change="filterCallback()"
                        @update:model-value="onFilter"
                    />
                </template>
            </Column>
            <Column
                field="owner"
                header="Engineer"
                :sortable="true"
                sort-field="owner_id"
            >
                <template #body="{ data: { owner } }">
                    {{ owner?.first_name }} {{ owner?.last_name }}
                </template>
            </Column>
            <Column
                field="is_open"
                header="Open"
                :sortable="true"
                sort-field="is_open"
                :show-filter-menu="false"
            >
                <template #filter="{ filterModel, filterCallback }">
                    <Dropdown
                        v-model="filterModel.value"
                        :options="[
                            { name: 'Open', code: true },
                            { name: 'Closed', code: false },
                        ]"
                        class="p-column-filter w-{15}"
                        :show-clear="true"
                        option-label="name"
                        option-value="code"
                        @change="filterCallback()"
                        @update:model-value="onFilter"
                    >
                    </Dropdown>
                </template>
                <template #body="{ data: { is_open } }">
                    <span
                        v-if="!is_open"
                        class="bg-red-600 text-white rounded p-2"
                        >Closed</span
                    >
                    <span
                        v-if="is_open"
                        class="bg-green-600 text-white rounded p-2"
                        >Open</span
                    >
                </template>
            </Column>
            <Column field="start_date" header="Start Date" :sortable="true">
                <template #body="{ data: { start_date } }">
                    {{ DateTime.fromSQL(start_date).toFormat("yyyy-MM-dd") }}
                </template>
            </Column>
            <Column field="end_date" header="End Date">
                <template #body="{ data: { end_date } }">
                    {{
                        !isEmpty(end_date)
                            ? DateTime.fromSQL(end_date).toFormat("yyyy-MM-dd")
                            : ""
                    }}
                </template>
            </Column>
            <Column field="hours" header="Total Hours">
                <template #body="{ data: { hours } }">
                    {{ hours ?? "0" }}
                </template>
            </Column>
            <Column field="actions" header="Actions">
                <template #body="{ data: { id } }">
                    <SecondaryButton
                        v-if="can('View Time Entry')"
                        class="mr-1"
                        @click="viewTimeEntries(id)"
                    >
                        <span class="pi pi-list mr-1" />
                        {{ $t("page.work_item.button.time_entries") }}
                    </SecondaryButton>
                    <SuccessButton
                        v-if="can('Edit Work Item')"
                        class="mr-1"
                        @click="editWorkItem(id)"
                    >
                        <span class="pi pi-pencil mr-1" />
                        {{ $t("page.work_item.button.edit") }}
                    </SuccessButton>
                    <DangerButton
                        v-if="can('Edit Work Item')"
                        class="mr-1"
                        @click="
                            workItemDeleteId = id;
                            showWorkItemConfirmation = true;
                        "
                    >
                        <span class="pi pi-trash mr-1" />
                        {{ $t("page.work_item.button.delete") }}
                    </DangerButton>
                </template>
            </Column>
        </DataTable>
    </div>
</template>
