<script lang="ts" setup>
import {PropType} from "vue";

const props = defineProps({
    type: {
        type: String as PropType<string>,
        required: false,
        default: 'submit',
    },
    icon: {
        type: String as PropType<string>,
        required: false,
        default: ''
    },
    action: {
        type: Function as PropType<Function>,
        required: true
    }

});
const buttonClasses = `inline-flex items-center justify-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-900 focus:ring-offset-2 transition ease-in-out duration-150`
</script>

<template>
    <button :type="props.type" :class="buttonClasses + ` ${props.icon}`" @click="props.action()">
        <slot/>
    </button>
</template>
