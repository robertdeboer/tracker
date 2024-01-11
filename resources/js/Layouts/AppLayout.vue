<script lang="ts" setup>
import ConfirmDialog from "primevue/confirmdialog";
import {Head} from '@inertiajs/vue3'
import MainNav from '@/Components/Nav/MainNav.vue'
import PageHeader from '@/Components/Page/PageHeader.vue'
import {PropType} from "vue";
import Toast from "primevue/toast";

defineProps({
    title: {
        type: String as PropType<string>,
        require: true
    },
})
</script>

<template>
    <div>
        <ConfirmDialog></ConfirmDialog>
        <Toast></Toast>
        <Head :title="title"/>
        <div class="min-h-screen bg-gray-200">
            <PageHeader :title="title"/>
            <MainNav/>
            <main>
                <slot :title="title"/>
            </main>
        </div>
    </div>
</template>
