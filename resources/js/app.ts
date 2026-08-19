import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    progress: {
        color: '#4B5563',
    },
<<<<<<< HEAD

=======
>>>>>>> 138acd3573af2808cbd4f2365c1f37690e55616c
    async setup({ el, App, props, plugin }) {
        const app = createApp({
            render: () => h(App, props),
        });

        app.use(plugin);

        app.mount(el!);
    },
});
<<<<<<< HEAD

=======
>>>>>>> 138acd3573af2808cbd4f2365c1f37690e55616c
