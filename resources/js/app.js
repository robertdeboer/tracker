import "./bootstrap";
import "../css/app.css";
import "primeicons/primeicons.css";
import "v-calendar/style.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import ConfirmationService from "primevue/confirmationservice";
import i18n from "@/i18n/index.ts";
import PrimeVue from "primevue/config";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { setupCalendar } from "v-calendar";
import TailWind from "primevue/passthrough/tailwind";
import ToastService from "primevue/toastservice";
import SpatiePermissions from "@/Plugins/SpatiePermissions.ts";
import { ZiggyVue } from "../../vendor/tightenco/ziggy/dist/vue.m";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue"),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, { unstyled: true, pt: TailWind })
            .use(ToastService)
            .use(ConfirmationService)
            .use(SpatiePermissions)
            .use(i18n)
            .use(setupCalendar, {})
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
