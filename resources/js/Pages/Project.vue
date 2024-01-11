<script lang="ts" setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Calendar from "primevue/calendar";
import ConfirmationModal from "@/Components/Jetstream/ConfirmationModal.vue";
import DangerButton from "@/Components/Jetstream/DangerButton.vue";
import Dialog from "primevue/dialog";
import Dropdown from "@/Components/Jetstream/Dropdown.vue";
import DropdownLink from "@/Components/Jetstream/DropdownLink.vue";
import HoursChart from "@/Components/Project/HoursChart.vue";
import TabView from "primevue/tabview";
import TabPanel from "primevue/tabpanel";
import SecondaryButton from "@/Components/Jetstream/SecondaryButton.vue";
import SuccessButton from "@/Components/Jetstream/SuccessButton.vue";
import {
    defineAsyncComponent,
    onMounted,
    PropType,
    ref,
    shallowRef,
    watch,
} from "vue";
import { ProjectInterface } from "@/Types/Project";
import { DeleteProject, GetProject, getProjectLog } from "@/Queries/Project";
import { DateTime } from "luxon";
import { SimpleDateRange } from "v-calendar/dist/types/src/utils/date/range";
import { useToast } from "primevue/usetoast";
import { router } from "@inertiajs/vue3";
import route from "ziggy-js";
import { emailSummary } from "@/Queries/Reports";

const OrderTab = defineAsyncComponent(
    () => import("@/Components/Project/Tabs/OrderTab.vue"),
);
const ProjectModel = defineAsyncComponent(
    () => import("@/Components/Project/ProjectModel.vue"),
);
const UserTab = defineAsyncComponent(
    () => import("@/Components/Project/Tabs/UserTab.vue"),
);
const WorkItemTab = defineAsyncComponent(
    () => import("@/Components/Project/Tabs/WorkItemTab.vue"),
);
const TimeEntryTab = defineAsyncComponent(
    () => import("@/Components/Project/Tabs/TimeEntryTab.vue"),
);

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    projectId: {
        type: Number as PropType<number>,
        required: true,
    },
    projectName: {
        type: String,
        required: true,
    },
});
const activeTab = shallowRef<number>(0);
const currentWorkItemId = shallowRef<number>(0);
const dateRange = ref<SimpleDateRange>({
    start: DateTime.now().minus({ days: 60 }).toJSDate(),
    end: DateTime.now().toJSDate(),
});
const refresh = shallowRef<number>(0);
const project = ref<ProjectInterface>();
const showCalendarModel = shallowRef<boolean>(false);
const showDeleteProjectConfirmationDialog = shallowRef<boolean>(false);
const showProjectModel = shallowRef<number>(0);
const toast = useToast();
/**
 * Delete the project
 */
const deleteProject = async function () {
    try {
        await DeleteProject(props.projectId);
        showDeleteProjectConfirmationDialog.value = false;
        router.get(route("dashboard"));
    } catch (error) {
        toast.add({ severity: "error", detail: error, life: 5000 });
    }
};
/**
 * Get the projects for the dashboard
 */
const getData = async function () {
    try {
        project.value = await GetProject(props.projectId);
    } catch (error) {
        toast.add({ severity: "error", detail: error, life: 5000 });
    }
};
/**
 * Set the date range
 * @param range<number>
 */
const setDateRange = function (range: number) {
    const start = DateTime.now();
    const days = range * 30;
    dateRange.value.end = start.toJSDate();
    dateRange.value.start = start.minus({ days: days }).toJSDate();
};
/**
 * Edit the project
 */
const editProject = function () {
    showProjectModel.value++;
};
/**
 * Email the summary
 */
const sendProjectSummary = async function () {
    await emailSummary(
        props.projectId,
        DateTime.fromJSDate(dateRange.value.start),
        DateTime.fromJSDate(dateRange.value.end),
    );
    toast.add({ severity: "success", detail: "Email queued.", life: 5000 });
};
/**
 * Get the project log
 */
const exportProjectLog = async function () {
    const data = await getProjectLog(props.projectId);
    const fileName =
        project.value?.name.replace(" ", "_") +
        "_" +
        DateTime.now().toLocaleString(DateTime.DATE_SHORT) +
        ".csv";
    const link = document.createElement("a");
    link.href = URL.createObjectURL(
        new Blob([data], { type: "text/csv;charset=utf-8" }),
    );
    link.setAttribute("download", fileName);
    link.click();
    link.remove();
};
/**
 * View hours for a given work item
 * @param workItemId<number>
 */
const viewHours = function (workItemId: number) {
    currentWorkItemId.value = workItemId;
    activeTab.value = 1;
};
onMounted(async function () {
    await getData();
});
watch(refresh, () => getData());
</script>

<template>
    <AppLayout :title="title">
        <ConfirmationModal
            :show="showDeleteProjectConfirmationDialog"
            @close="showDeleteProjectConfirmationDialog = false"
        >
            <template #title> Confirm Project Delete</template>
            <template #content>
                Are you sure you want to delete the project {{ project?.name }}?
            </template>
            <template #footer>
                <DangerButton class="mr-1" @click="deleteProject()">
                    Yes
                </DangerButton>

                <SuccessButton
                    class="mr-1"
                    @click="showDeleteProjectConfirmationDialog = false"
                >
                    No
                </SuccessButton>
            </template>
        </ConfirmationModal>
        <div class="py-12">
            <div class="sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-3">
                        <div class="p-2 bg-white border-b border-gray-200">
                            <span
                                class="text-5xl font-bold dark:text-white pl-5"
                            >
                                {{ projectName ?? project?.name ?? "" }}
                                <SecondaryButton
                                    class="align-middle"
                                    @click="showCalendarModel = true"
                                >
                                    <span class="pi pi-calendar-plus mr-1" />
                                    {{
                                        DateTime.fromJSDate(
                                            dateRange.start,
                                        ).toFormat("yyyy-MM-dd")
                                    }}
                                    &lt;-&gt;
                                    {{
                                        DateTime.fromJSDate(
                                            dateRange.end,
                                        ).toFormat("yyyy-MM-dd")
                                    }}
                                </SecondaryButton>
                            </span>
                            <span class="p-1 float-right">
                                <Dropdown
                                    v-if="can('Run Reports')"
                                    class="m-1 float-left"
                                >
                                    <template #trigger>
                                        <button
                                            type="button"
                                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                                        >
                                            {{
                                                $t(
                                                    "page.project.buttons.summary.main",
                                                )
                                            }}
                                            <svg
                                                class="ml-2 -mr-0.5 h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"
                                                />
                                            </svg>
                                        </button>
                                    </template>
                                    <template #content>
                                        <DropdownLink
                                            as="a"
                                            target="_blank"
                                            :href="
                                                route('project.summary', {
                                                    id: props.projectId,
                                                    start: DateTime.fromJSDate(
                                                        dateRange.start,
                                                    ).toFormat('yyyy-MM-dd'),
                                                    end: DateTime.fromJSDate(
                                                        dateRange.end,
                                                    ).toFormat('yyyy-MM-dd'),
                                                })
                                            "
                                            >{{
                                                $t(
                                                    "page.project.buttons.summary.view",
                                                )
                                            }}</DropdownLink
                                        >
                                        <DropdownLink
                                            as="button"
                                            @click.prevent="sendProjectSummary"
                                            >{{
                                                $t(
                                                    "page.project.buttons.summary.email",
                                                )
                                            }}</DropdownLink
                                        >
                                    </template>
                                </Dropdown>
                                <SecondaryButton
                                    v-if="can('Run Reports')"
                                    class="m-1"
                                    @click="exportProjectLog"
                                >
                                    <span class="pi pi-file-export mr-1" />
                                    {{ $t("page.project.buttons.log") }}
                                </SecondaryButton>
                                <SuccessButton
                                    v-if="can('Edit Project')"
                                    class="m-1"
                                    @click="editProject"
                                >
                                    <span class="pi pi-pencil mr-1" />
                                    {{ $t("page.project.buttons.update") }}
                                </SuccessButton>
                                <DangerButton
                                    v-if="can('Edit Project')"
                                    class="m-1"
                                    @click="
                                        showDeleteProjectConfirmationDialog = true
                                    "
                                >
                                    <span class="pi pi-trash" />&nbsp;
                                </DangerButton>
                            </span>
                        </div>
                        <div class="clear-both" />
                        <div
                            class="border-b border-gray-200 p-3 grid gap-2 grid-flow-row grid-cols-4"
                        >
                            <div
                                class="rounded shadow"
                                style="background-color: #cacccf"
                            >
                                <p class="text-center">
                                    {{ $t("user.roles.Project Manager") }}<br />
                                    <span class="font-bold">
                                        {{
                                            project?.project_manager?.first_name
                                        }}
                                        {{
                                            project?.project_manager?.last_name
                                        }} </span
                                    ><br />
                                    <a
                                        :href="
                                            'mailto:' +
                                            project?.project_manager?.email
                                        "
                                        class="text-blue-500"
                                    >
                                        {{
                                            project?.project_manager?.first_name
                                        }}
                                        {{
                                            project?.project_manager?.last_name
                                        }}
                                    </a>
                                </p>
                            </div>
                            <div
                                class="rounded shadow"
                                style="background-color: #cacccf"
                            >
                                <p class="text-center">
                                    {{ $t("user.roles.Customer") }}<br />
                                    <span class="font-bold"
                                        >{{ project?.customer?.first_name }}
                                        {{ project?.customer?.last_name }}</span
                                    ><br />
                                    <a
                                        :href="
                                            'mailto:' + project?.customer?.email
                                        "
                                        class="text-blue-500"
                                    >
                                        {{ project?.customer?.first_name }}
                                        {{ project?.customer?.last_name }}
                                    </a>
                                </p>
                            </div>
                            <div
                                class="rounded shadow"
                                style="background-color: #cacccf"
                            >
                                <p class="text-center">
                                    Hours Used<br />
                                    <span class="font-bold">{{
                                        project?.hours_used ?? 0
                                    }}</span
                                    ><br />
                                </p>
                            </div>
                            <div
                                class="rounded shadow"
                                style="background-color: #cacccf"
                            >
                                <p class="text-center">
                                    {{ $t("page.project.summary.ordered")
                                    }}<br />
                                    <span class="font-bold">{{
                                        project?.hours_ordered ?? 0
                                    }}</span
                                    ><br />
                                </p>
                            </div>
                        </div>
                        <div class="border-b border-gray-200">
                            <p class="text-2xl font-bold dark:text-white mt-1">
                                {{ $t("page.project.summary.used") }}
                            </p>
                            <div>
                                <HoursChart
                                    :project-id="props.projectId"
                                    :start-date="
                                        DateTime.fromJSDate(dateRange.start)
                                    "
                                    :end-date="
                                        DateTime.fromJSDate(dateRange.end)
                                    "
                                    :refresh="refresh"
                                />
                            </div>
                        </div>
                        <div style="min-height: 300px">
                            <TabView v-model:active-index="activeTab">
                                <TabPanel header="Work Items">
                                    <WorkItemTab
                                        :end-date="
                                            DateTime.fromJSDate(dateRange.end)
                                        "
                                        :start-date="
                                            DateTime.fromJSDate(dateRange.start)
                                        "
                                        :project-id="props.projectId"
                                        :show-modal="refresh"
                                        @updated="refresh++"
                                        @view-hours="viewHours"
                                    />
                                </TabPanel>
                                <TabPanel header="Time">
                                    <TimeEntryTab
                                        :project-id="props.projectId"
                                        :work-item-id="currentWorkItemId"
                                        @updated="refresh++"
                                    />
                                </TabPanel>
                                <TabPanel header="Orders">
                                    <OrderTab :project-id="props.projectId" />
                                </TabPanel>
                                <TabPanel header="Users">
                                    <UserTab :project-id="props.projectId" />
                                </TabPanel>
                            </TabView>
                        </div>
                    </div>
                </div>
            </div>
            <ProjectModel
                :show-modal="showProjectModel"
                title="Update Project"
                :project-id="props.projectId"
                @updated="getData"
            />
            <Dialog
                :visible="showCalendarModel"
                modal
                header="Choose Date Range"
                @update:visible="showCalendarModel = false"
            >
                <form @submit.prevent>
                    <Calendar
                        v-model="dateRange.start"
                        show-icon
                        date-format="yy-mm-dd"
                    />
                    <Calendar
                        v-model="dateRange.end"
                        show-icon
                        date-format="yy-mm-dd"
                        class="float-right"
                    />
                    <hr
                        class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700"
                    />
                    <SecondaryButton
                        class="float-right mr-1"
                        @click="setDateRange(12)"
                    >
                        <span class="pi pi-calendar-plus mr-1" />
                        Last Year
                    </SecondaryButton>
                    <SecondaryButton
                        class="float-right mr-1"
                        @click="setDateRange(6)"
                    >
                        <span class="pi pi-calendar-plus mr-1" />
                        Last 6 Months
                    </SecondaryButton>
                    <SecondaryButton
                        class="float-right mr-1"
                        @click="setDateRange(3)"
                    >
                        <span class="pi pi-calendar-plus mr-1" />
                        Last 3 Months
                    </SecondaryButton>
                </form>
                <template #footer>
                    <SuccessButton
                        class="mr-1"
                        @click="
                            showCalendarModel = false;
                            refresh++;
                        "
                    >
                        Ok
                    </SuccessButton>
                </template>
            </Dialog>
        </div>
    </AppLayout>
</template>
