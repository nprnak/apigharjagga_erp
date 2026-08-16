import { defineConfig } from '@vueform/vueform'
import en from '@vueform/vueform/locales/en'
import vueform from '@vueform/vueform/themes/vueform'

export default defineConfig({
    theme: vueform,
    locales: {
        en,
    },
    locale: 'en',
})