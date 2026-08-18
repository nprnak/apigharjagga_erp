import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import { route } from './route';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

window.route = route;

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    progress: {
        color: '#4B5563',
    },
    async setup({ el, App, props, plugin }) {
        const app = createApp({
            render: () => h(App, props),
        });

        app.use(plugin);
        app.config.globalProperties.route = route;

        app.mount(el!);
    },
});
