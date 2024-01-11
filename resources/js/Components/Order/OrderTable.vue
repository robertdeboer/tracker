<script lang="ts" setup>
import ConfirmationModal from "@/Components/Jetstream/ConfirmationModal.vue";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import InputText from "primevue/inputtext";
import { debounce, isNull } from "lodash";
import { DeleteOrder, GetOrderList } from "@/Queries/Order";
import { FilterMatchMode } from "primevue/api";
import { onMounted, PropType, reactive, ref, shallowRef, watch } from "vue";
import { OrderInterface } from "@/Types/Order";
import { TablePaginationInterface, TableSortInterface } from "@/Types/Page";
import { useToast } from "primevue/usetoast";
import SuccessButton from "@/Components/Jetstream/SuccessButton.vue";
import DangerButton from "@/Components/Jetstream/DangerButton.vue";
import { DateTime } from "luxon";

const props = defineProps({
    projectId: {
        type: Number as PropType<number>,
        required: false,
        default: null,
    },
    hideProjectColumn: {
        type: Boolean as PropType<boolean>,
        required: false,
        default: false,
    },
    refresh: {
        type: Number as PropType<number>,
        required: false,
        default: 0,
    },
});

const emit = defineEmits(["editOrder", "deleteOrder"]);

const showConfirmation = shallowRef<boolean>(false);
const filters = ref({
    email: { value: null, matchMode: FilterMatchMode.CONTAINS },
    project: { value: null, matchMode: FilterMatchMode.CONTAINS },
    reference_number: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const isLoading = shallowRef<boolean>(true);
const orders = ref<Array<OrderInterface>>([]);
const orderIdToDelete = shallowRef<null | number>(null);
const pagination = reactive<TablePaginationInterface>({
    numberOfRows: 10,
    page: 0,
});
const sort = reactive<TableSortInterface>({
    column: "CREATED_AT",
    order: "DESC",
});
const toast = useToast();
const totalOrders = ref<number>(0);
/**
 * Cancel the order deletion request
 */
const cancelOrderDelete = function () {
    showConfirmation.value = false;
    orderIdToDelete.value = null;
};
/**
 * Confirm order deletion
 * @param id<number>
 */
const confirmOrderDelete = function (id: number) {
    orderIdToDelete.value = id;
    showConfirmation.value = true;
};
/**
 * Delete an order
 */
const deleteOrder = function () {
    if (!isNull(orderIdToDelete.value)) {
        try {
            DeleteOrder(orderIdToDelete.value);
            toast.add({
                severity: "success",
                summary: "Success",
                detail: "Order Deleted",
                life: 5000,
            });
        } catch (error) {
            toast.add({
                severity: "error",
                summary: "Error",
                detail: error,
                life: 5000,
            });
        }
    }
    showConfirmation.value = false;
    getData();
};
/**
 * Get the table data
 */
const getData = async function () {
    try {
        const {
            data,
            paginatorInfo: { perPage, total },
        } = await GetOrderList(
            pagination.numberOfRows,
            pagination.page + 1,
            sort.column,
            sort.order,
            {
                email: filters.value.email.value,
                project: filters.value.project.value,
                reference_number: filters.value.reference_number.value,
            },
            props.projectId,
        );
        orders.value = data;
        pagination.numberOfRows = perPage;
        totalOrders.value = total;
        isLoading.value = false;
    } catch (error) {
        orders.value = [];
        toast.add({
            severity: "error",
            summary: "Error",
            detail: error,
            life: 5000,
        });
        isLoading.value = false;
    }
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
 * @param page
 * @param rows
 */
const onPage = function ({ page, rows }: { page: number; rows: number }) {
    pagination.page = page;
    pagination.numberOfRows = rows;
    getData();
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
    sort.column = sortField.toUpperCase();
    sort.order = sortOrder == 1 ? "ASC" : "DESC";
    getData();
};

/**
 * Watchers
 */
watch(
    () => props.refresh,
    () => {
        getData();
    },
);
/**
 * On table mount, get orders
 */
onMounted(() => {
    isLoading.value = true;
    getData();
});
</script>

<template>
    <ConfirmationModal
        :show="showConfirmation"
        @close="showConfirmation = false"
    >
        <template #title> Confirm Order Delete</template>
        <template #content>
            Are you sure you want to delete this order?
        </template>
        <template #footer>
            <DangerButton class="mr-1" @click="deleteOrder()">
                Yes
            </DangerButton>

            <SuccessButton class="mr-1" @click="cancelOrderDelete()">
                No
            </SuccessButton>
        </template>
    </ConfirmationModal>
    <DataTable
        v-model:filters="filters"
        :value="orders"
        lazy
        datakey="id"
        striped-rows
        paginator
        :first="0"
        :rows="10"
        :total-records="totalOrders"
        :loading="isLoading"
        :rows-per-page-options="[10, 20, 30]"
        :global-filter-fields="['email', 'project', 'reference_number']"
        filter-display="row"
        @page="onPage($event)"
        @sort="onSort($event)"
    >
        <template #empty> No orders found.</template>
        <Column
            field="reference_number"
            header="Reference Number"
            :sortable="true"
            :show-filter-menu="false"
        >
            <template #filter="{ filterModel, filterCallback }">
                <InputText
                    v-model.lazy.trim="filterModel.value"
                    type="text"
                    class="p-column-filter"
                    @input="filterCallback()"
                    @update:model-value="onFilter"
                />
            </template>
        </Column>
        <Column
            field="project"
            header="Project"
            :sortable="true"
            sort-field="project_id"
            filter-field="project"
            :show-filter-menu="false"
            :hidden="hideProjectColumn"
        >
            <template
                #body="{
                    data: {
                        project: { name },
                    },
                }"
            >
                {{ name }}
            </template>
            <template #filter="{ filterModel, filterCallback }">
                <InputText
                    v-model.lazy.trim="filterModel.value"
                    type="text"
                    class="p-column-filter"
                    @input="filterCallback()"
                    @update:model-value="onFilter"
                />
            </template>
        </Column>
        <Column field="date" header="Date" :sortable="true">
            <template #body="{ data: { date } }">
                {{ DateTime.fromSQL(date).toFormat("yyyy-MM-dd") }}
            </template>
        </Column>
        <Column
            field="email"
            header="Order Email"
            :sortable="true"
            style="min-width: 12rem"
            filter-field="email"
            :show-filter-menu="false"
        >
            <template #body="{ data: { email } }">
                {{ email }}
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
        <Column field="hours" header="Hours" :sortable="true" />
        <Column field="actions" :sortable="false">
            <template #body="{ data: { id } }">
                <SuccessButton
                    v-if="can('Edit Orders')"
                    class="mr-1"
                    @click="emit('editOrder', id)"
                >
                    <span class="pi pi-pencil mr-1" />
                    {{ $t(`page.order.button.edit`) }}
                </SuccessButton>
                &nbsp;
                <DangerButton
                    v-if="can('Edit Orders')"
                    class="mr-1"
                    @click="confirmOrderDelete(id)"
                >
                    <span class="pi pi-trash mr-1" />
                    {{ $t(`page.order.button.delete`) }}
                </DangerButton>
            </template>
        </Column>
    </DataTable>
</template>
