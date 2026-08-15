import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import Vueform from '@vueform/vueform';
import vueformConfig from '../../vueform.config';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    progress: {
        color: '#4B5563',
    },
    setup({ el, App, props, plugin }) {
        createApp({
            render: () => h(App, props),
        })
            .use(plugin)
            .use(Vueform, vueformConfig)
            .mount(el!);
    },
});
