<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import {reactive, watchEffect} from 'vue';
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";

const props = defineProps({
    users: Number,
    roles: Number,
    rolesNameds: Object,
    numberPermissions: Number,
    Obtenidos: Object,
    Busqueda: Object,
})

const data = reactive({
    params: {
        pregunta: ''
    },
})
const dashboard = [
    // 'Informes',
    // 'roles',
];
const dashLinks = [
    // 'Informes',
    // 'roles',
];
const downloadAnexos = () => {
    window.open('downloadAnexos', '_blank')
}
</script>


<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <Breadcrumb :title="'Resumen'" :breadcrumbs="[]"/>
        <div class="space-y-4">
            <div class="grid gap-6 shadow-sm py-1 sm:grid-cols-1 dark:text-gray-100">
                <div class="mx-auto">
                    <p class="my-4 text-xl font-bold text-center">texto
                    </p>
                </div>
            </div>
            
<!--                v-show="can(['isadmin'])"-->
            <div 
                 class="block mx-4"> 
                <InputLabel for="Quincena" value="Quincena" class="block"/>
                <v-select
                    v-model="data.params.pregunta"
                    label="texto"
                    :options="[{value:1,texto:'Simon bolivar'},{value:2,texto:'Alameda'}]"
                    required class="dark:bg-gray-400 xs:hidden lg:min-w-[240px]"
                ></v-select>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
