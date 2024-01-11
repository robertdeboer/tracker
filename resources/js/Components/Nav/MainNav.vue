<script lang="ts" setup>
import Dropdown from "@/Components/Jetstream/Dropdown.vue";
import DropdownLink from "@/Components/Jetstream/DropdownLink.vue";
import NavLink from "@/Components/Jetstream/NavLink.vue";
import route from "ziggy-js";
import { onMounted, ref } from "vue";
import { ProjectInterface } from "@/Types/Project";
import { getProjectsForNavigation } from "@/Queries/Dashboard";

const projects = ref<Array<ProjectInterface>>([]);

/**
 * Get all available projects
 */
const getProjects = async function () {
    try {
        projects.value = await getProjectsForNavigation();
    } catch (error) {
        projects.value = [];
    }
};

onMounted(() => {
    getProjects();
});
</script>

<template>
    <nav class="bg-white border-b border-gray-100">
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="hidden sm:flex sm:items-center">
                    <div class="relative">
                        <NavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            {{ $t("navigation.dashboard") }}
                        </NavLink>
                    </div>
                    <div class="ml-3 relative">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <span class="inline-flex rounded-md">
                                    <button
                                        type="button"
                                        class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-lg font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out"
                                    >
                                        {{ $t("navigation.projects") }}
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
                                                d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                                            />
                                        </svg>
                                    </button>
                                </span>
                            </template>
                            <template #content>
                                <DropdownLink
                                    v-for="project in projects"
                                    :key="project.id"
                                    :href="route('project', project.id)"
                                >
                                    {{ project.name }}
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                    <div v-if="can('View Orders')" class="ml-3 relative">
                        <NavLink
                            :href="route('orders')"
                            :active="route().current('orders')"
                        >
                            {{ $t("navigation.orders") }}
                        </NavLink>
                    </div>
                    <div v-if="can('Manage System')" class="ml-3 relative">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <span class="inline-flex rounded-md">
                                    <button
                                        type="button"
                                        class="inline-flex items-center px-3 py-2 border border-transparent leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150"
                                    >
                                        {{ $t("navigation.system") }}
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
                                                d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                                            />
                                        </svg>
                                    </button>
                                </span>
                            </template>
                            <template #content>
                                <div
                                    class="block px-4 py-2 text-xs text-gray-400"
                                >
                                    Manage {{ $t("navigation.system") }}
                                </div>
                                <DropdownLink :href="route('users')">
                                    {{ $t("navigation.users") }}
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>
