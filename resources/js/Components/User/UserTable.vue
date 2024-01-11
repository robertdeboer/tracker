<script lang="ts" setup>
import ConfirmationModal from "@/Components/Jetstream/ConfirmationModal.vue";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import Dropdown from "primevue/dropdown";
import InputText from "primevue/inputtext";
import { debounce, isNull } from "lodash";
import { DeleteUser, GetUsersList } from "@/Queries/User";
import { FilterMatchMode } from "primevue/api";
import { onMounted, PropType, reactive, ref, shallowRef, watch } from "vue";
import { TablePaginationInterface, TableSortInterface } from "@/Types/Page";
import { useToast } from "primevue/usetoast";
import SuccessButton from "@/Components/Jetstream/SuccessButton.vue";
import Tag from "primevue/tag";
import DangerButton from "@/Components/Jetstream/DangerButton.vue";
import { UserInterface } from "@/Types/User";
import { getRoleOptions } from "@/Queries/Permissions";

const props = defineProps({
    userId: {
        type: Number as PropType<number>,
        required: false,
        default: null,
    },
    refresh: {
        type: Number as PropType<number>,
        required: false,
        default: 0,
    },
});

const emits = defineEmits(["editUser"]);

const showConfirmation = shallowRef<boolean>(false);
const filters = ref({
    email: { value: null, matchMode: FilterMatchMode.CONTAINS },
    first_name: { value: null, matchMode: FilterMatchMode.CONTAINS },
    last_name: { value: null, matchMode: FilterMatchMode.CONTAINS },
    role: { value: null, matchMode: FilterMatchMode.EQUALS },
});
const isLoading = shallowRef<boolean>(true);
const users = ref<Array<UserInterface>>([]);
const totalUsers = shallowRef<number>(0);
const roles = ref([]);
const userIdToDelete = shallowRef<null | number>(null);
const pagination = reactive<TablePaginationInterface>({
    numberOfRows: 10,
    page: 0,
});
const sort = reactive<TableSortInterface>({
    column: "EMAIL",
    order: "ASC",
});
const toast = useToast();
/**
 * Cancel the user deletion request
 */
const cancelUserDelete = function () {
    showConfirmation.value = false;
    userIdToDelete.value = null;
};
/**
 * Confirm user deletion
 * @param id<number>
 */
const confirmUserDelete = function (id: number) {
    userIdToDelete.value = id;
    showConfirmation.value = true;
};
/**
 * Delete an order
 */
const deleteUser = async function () {
    if (!isNull(userIdToDelete.value)) {
        try {
            await DeleteUser(userIdToDelete.value);
            toast.add({
                severity: "success",
                summary: "Success",
                detail: "User Deleted",
                life: 5000,
            });
            showConfirmation.value = false;
            await getData();
        } catch (error) {
            toast.add({
                severity: "error",
                summary: "Error",
                detail: error,
                life: 5000,
            });
        }
    }
};
/**
 * Get the table data
 */
const getData = async function () {
    try {
        const {
            data,
            paginatorInfo: { perPage, total },
        } = await GetUsersList(
            pagination.numberOfRows,
            pagination.page + 1,
            sort.column,
            sort.order,
            {
                email: filters.value.email.value,
                first_name: filters.value.first_name.value,
                last_name: filters.value.last_name.value,
                role: filters.value.role.value,
            },
        );
        users.value = data;
        totalUsers.value = total;
        pagination.numberOfRows = perPage;
        isLoading.value = false;
    } catch (error) {
        users.value = [];
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
    () => getData(),
);
/**
 * On table mount, get orders
 */
onMounted(async () => {
    isLoading.value = true;
    await getData();
    try {
        roles.value = await getRoleOptions();
    } catch (error) {
        // Do nothing
    }
});
</script>

<template>
    <ConfirmationModal
        :show="showConfirmation"
        @close="showConfirmation = false"
    >
        <template #title> Confirm User Delete </template>
        <template #content>
            Are you sure you want to delete this user?
        </template>
        <template #footer>
            <DangerButton class="mr-1" @click="deleteUser()">
                Yes
            </DangerButton>

            <SuccessButton class="mr-1" @click="cancelUserDelete()">
                No
            </SuccessButton>
        </template>
    </ConfirmationModal>
    <DataTable
        ref="dt"
        v-model:filters="filters"
        :value="users"
        lazy
        datakey="id"
        striped-rows
        paginator
        :first="0"
        :rows="10"
        :total-records="totalUsers"
        :loading="isLoading"
        :rows-per-page-options="[10, 20, 30]"
        :global-filter-fields="['first_name', 'last_name', 'email', 'role']"
        filter-display="row"
        @page="onPage($event)"
        @sort="onSort($event)"
    >
        <template #empty> No users found.</template>
        <Column
            field="first_name"
            header="First Name"
            :sortable="true"
            :show-filter-menu="false"
        >
            <template #body="{ data: { first_name } }">
                {{ first_name }}
            </template>
            <template #filter="{ filterModel, filterCallback }">
                <InputText
                    v-model.lazy.trim="filterModel.value"
                    type="text"
                    class="p-column-filter"
                    placeholder="Search by First Name"
                    @change="filterCallback()"
                    @update:model-value="onFilter"
                />
            </template>
        </Column>
        <Column
            field="last_name"
            header="Last Name"
            :sortable="true"
            :show-filter-menu="false"
        >
            <template #body="{ data: { last_name } }">
                {{ last_name }}
            </template>
            <template #filter="{ filterModel, filterCallback }">
                <InputText
                    v-model.lazy.trim="filterModel.value"
                    type="text"
                    class="p-column-filter"
                    placeholder="Search by Last Name"
                    @change="filterCallback()"
                    @update:model-value="onFilter"
                />
            </template>
        </Column>
        <Column
            field="email"
            header="Email"
            :sortable="true"
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
                    placeholder="Search by Email"
                    @change="filterCallback()"
                    @update:model-value="onFilter"
                />
            </template>
        </Column>
        <Column
            field="role"
            header="Role"
            :sortable="false"
            sort-field="role_id"
            :show-filter-menu="false"
        >
            <template #body="{ data: { roles: userRoles } }">
                <Tag
                    v-if="userRoles.length"
                    :value="$t(`user.roles.${userRoles[0].name}`)"
                    severity="success"
                    :class="userRoles[0].name.replaceAll(' ', '')"
                />
                <Tag v-else value="Unassigned" />
            </template>
            <template #filter="{ filterModel, filterCallback }">
                <Dropdown
                    v-model="filterModel.value"
                    :options="roles"
                    class="p-column-filter"
                    :show-clear="true"
                    option-label="name"
                    option-value="code"
                    @change="filterCallback()"
                    @update:model-value="onFilter"
                >
                </Dropdown>
            </template>
        </Column>
        <Column field="actions" :sortable="false">
            <template #body="{ data: { id } }">
                <SuccessButton class="mr-1" @click="emits('editUser', id)">
                    <span class="pi pi-pencil mr-1" />
                    {{ $t("page.user.button.edit") }}
                </SuccessButton>
                &nbsp;
                <DangerButton class="mr-1" @click="confirmUserDelete(id)">
                    <span class="pi pi-trash mr-1" />
                    {{ $t("page.user.button.delete") }}
                </DangerButton>
            </template>
        </Column>
    </DataTable>
</template>

<style scoped>
.SuperAdmin {
    background-color: #ff0000ff;
}
.Admin {
    background-color: #ffa500ff;
}
.AdminReadOnly {
    background-color: #ffa500ff;
    border: 2px solid black;
}
.Customer {
    background-color: #008000ff;
}
.Engineer {
    background-color: #0000ffff;
}
.ProjectManager {
    background-color: #808080ff;
}
</style>
