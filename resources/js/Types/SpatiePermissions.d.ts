/* eslint-disable @typescript-eslint/no-unused-vars */
import Vue from 'vue';

declare module 'vue/types/vue' {
    interface Vue {
        can: (permission: string) => boolean
        is: (role: string) => boolean
    }
}
