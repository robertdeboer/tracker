<script lang="ts" setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Card from "primevue/card";
import InputSwitch from "primevue/inputswitch";
import { onMounted, PropType, ref, shallowRef, watch } from "vue";
import { getProjects } from "@/Queries/Dashboard";
import { ProjectInterface } from "@/Types/Project";
import route from "ziggy-js";
import { router } from "@inertiajs/vue3";
import SuccessButton from "@/Components/Jetstream/SuccessButton.vue";
import ProjectModel from "@/Components/Project/ProjectModel.vue";

const props = defineProps({
    title: {
        type: String as PropType<string>,
        required: true,
    },
});

const isLoading = shallowRef<boolean>(true);
const projects = ref<Array<ProjectInterface>>([]);
const showInActiveProjects = shallowRef<boolean>(false);
const showModel = shallowRef<number>(0);
/**
 * Get the projects for the dashboard
 */
const getData = async function () {
    console.log("get data");
    isLoading.value = true;
    projects.value = await getProjects(showInActiveProjects.value);
    isLoading.value = false;
};
onMounted(() => getData());
watch(showInActiveProjects, () => getData());
</script>

<template>
    <AppLayout :title="props.title">
        <div class="py-12">
            <div class="sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-3">
                        <div
                            class="p-6 lg:p-8 bg-white border-b border-gray-200"
                        >
                            <p class="text-3xl font-bold dark:text-white">
                                {{ $t("page.dashboard.heading") }}
                            </p>
                            <SuccessButton
                                v-if="can('Edit Project')"
                                class="mr-1 float-right"
                                @click="showModel++"
                            >
                                <span class="pi pi-plus-circle mr-1" />
                                {{ $t("page.dashboard.buttons.create") }}
                            </SuccessButton>
                            <div class="flex align-items-center">
                                <InputSwitch
                                    v-model="showInActiveProjects"
                                    name="showInActiveProjects"
                                />
                                <label for="showInActiveProjects" class="ml-2"
                                    >Show Closed</label
                                >
                            </div>
                        </div>
                        <Card
                            v-for="project in projects"
                            :key="project.id"
                            class="m-3 float-left"
                            style="
                                height: 10em;
                                width: 25em;
                                background-color: #cacccf;
                            "
                        >
                            <template #title>
                                <div
                                    class="cursor-pointer"
                                    @click="
                                        router.visit(
                                            route('project', project.id),
                                        )
                                    "
                                >
                                    <h1 style="color: #1173c2">
                                        <span class="pi pi-bars text-gray-500"
                                            >&nbsp;</span
                                        >
                                        {{ project.name }}
                                    </h1>
                                    <hr style="color: black" />
                                </div>
                            </template>
                            <template #content>
                                <p class="text-center">
                                    Customer: {{ project.customer.first_name }}
                                    {{ project.customer.last_name }}
                                </p>
                                <p class="font-bold text-center">
                                    {{ project.hours_used }} /
                                    {{ project.hours_ordered }}
                                </p>
                            </template>
                        </Card>
                        <Card
                            v-show="projects.length < 1 && !isLoading"
                            class="m-3 float-left"
                            style="
                                height: 15em;
                                width: 25em;
                                background-color: #cacccf;
                            "
                        >
                            <template #title>
                                <h1>
                                    <span class="pi pi-bars">&nbsp;</span> Your
                                    First Project
                                </h1>
                                <hr style="color: black" />
                            </template>
                            <template #content>
                                <p>
                                    You do not have any projects at this time.
                                    Please check back again or contact your
                                    project manager if you feel this is not
                                    right.
                                </p>
                            </template>
                        </Card>
                        <Card
                            v-show="isLoading && projects.length < 1"
                            class="m-3 float-left"
                            style="
                                height: 15em;
                                width: 25em;
                                background-color: #cacccf;
                            "
                        >
                            <template #title>
                                <h1>
                                    <span class="pi pi-bars">&nbsp;</span>
                                    Getting Projects....
                                </h1>
                                <hr style="color: black" />
                            </template>
                            <template #content>
                                <p>One minute while we grab your projects.</p>
                            </template>
                        </Card>
                    </div>
                </div>
            </div>
            <ProjectModel :show-modal="showModel" @updated="getData" />
        </div>
    </AppLayout>
</template>
