<template>
    <div class="w-full p-1 sm:px-0">
        <TabGroup>
            <TabList class="flex space-x-1 rounded-xl bg-blue-900/20 p-1">
                <Tab
                    v-for="category in Object.keys(categories)"
                    as="template"
                    :key="category"
                    v-slot="{ selected }"
                >
                    <button :class="[ 'w-full rounded-lg py-2.5 text-sm font-medium leading-5', 'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2', selected
                ? 'bg-white text-blue-700 shadow'
                : 'text-blue-100 hover:bg-white/[0.12] hover:text-white', ]">
                        {{ category }}
                    </button>
                </Tab>
            </TabList>

            <TabPanels class="mt-2">
<!--                    v-for="(posts, idx) in Object.values(categories)"-->
                <TabPanel
                    v-for="(elobjeto, index) in labelsform"
                    :key="index"
                    :class="[ 'rounded-xl bg-white p-3', 'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2', ]"
                >
                    <div class="m-1 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 text-sm font-normal leading-4 ">
                        <div v-for="(names , indx2) in elobjeto" :key="indx2"
                             class="m-2 text-gray-800">
                            <div>
                                <small class="mx-2 text-lg capitalize">{{ labelsform[index][indx2] }} 
                                    noome  
                                    {{ tabsform }} :::
                                    {{ indx2 }}
                                </small>
                                <TextInput v-model="tabsform[(indx2)]"
                                           type="text"
                                           class="block w-4/6 xl:w-full m-2 rounded-lg"
                                           :placeholder="labelsform[index][indx2]"/>
                            </div>
                        </div>
                    </div>
                </TabPanel>
            </TabPanels>
        </TabGroup>
    </div>
</template>

<script setup>
import {ref, toRaw, watch} from 'vue'
import {Tab, TabGroup, TabList, TabPanel, TabPanels} from '@headlessui/vue'
import TextInput from "@/Components/TextInput.vue";
import {onMounted} from 'vue';
import {router, useForm} from "@inertiajs/vue3";
import _, {debounce, pickBy} from "lodash";

const props = defineProps({
    tamano_m2: Number,
})
onMounted(() => {
    // if(!props.buscadorEntero.tamano_m2){
    //     props.buscadorEntero.tamano_m2 = 40
    // }
})

// const justNames = props.titulos.map(names => names['order'])
const justNames = [
    'precio',
    'administracion',
    
    'habitaciones',
    'banos',
    'acepta_mascotas',
    'terraza',
    'pisos_interiores',
    'parqueaderos',
    'usado',
    'inmoviliaria',
    'tamano_m2',
    'contacto_celular',
    'contacto_celular2',
    'estrato',
    'antiguedad',
]

const tabsform = useForm({
    // ...Object.fromEntries(justNames.map(field => [field, '']))}
    precio: '',
    administracion: '',

    habitaciones: '',
    banos: '',
    acepta_mascotas: '',
    terraza: '',
    pisos_interiores: '',
    parqueaderos: '',
    usado: '',
    inmoviliaria: '',
    tamano_m2: '',
    contacto_celular: '',
    contacto_celular2: '',
    estrato: '',
    antiguedad: '',
});

   // tipo_inmueble: 'tipo de inmueble',
        // ventaOarriendo: 'Venta o Arriendo',
        // barrio: 'Barrio',
        // ciudad: 'Ciudad',
        // pais: 'Pais',
const labelsform = [
    {
        precio:'precio',
        administracion:'administracion',
    },
    {
        habitaciones:'habitaciones',
        banos:'baños',
        // acepta_mascotas:'acepta mascotas',
        terraza:'terraza',
        pisos_interiores:'pisos interiores',
        parqueaderos:'parqueaderos'
    },
    {
     
        usado: 'Usado',
        inmoviliaria: 'Inmoviliaria',
        tamano_m2: 'Metros²',
    },
    {
        contacto_celular:'Contacto celular',
        contacto_celular2:'Contacto celular Num2',
    },
    {
        estrato:'estrato',
        antiguedad:'antiguedad',
    },
    
]
function capitalizeFirstLetter(val){
    return String(val).charAt(0).toUpperCase() + String(val).slice(1);
}
const categories = ref({
    Capital: [
        {
            id: 1,
            title: 'tipo_inmueble',
            date: '5h ago',
            labelform: 'Tipo inmueble',
            shareCount: 2,
        },
        {
            id: 2,
            title: "ventaOarriendo",
            date: '2h ago',
            labelform: 3,
            shareCount: 2,
        },
    ],
    Comodidades: [
        {
            id: 1,
            title: 'tipo_inmueble',
            date: '5h ago',
            labelform: 'Tipo inmueble',
            shareCount: 2,
        },
    ],
    Tamaño: [
        {
            id: 1,
            title: 'tipo_inmueble',
            date: '5h ago',
            labelform: 'Tipo inmueble',
            shareCount: 2,
        },
    ],
    Contacto: [
        {
            id: 1,
            title: 'Habitaciones',
            date: 'Jan 7',
            labelform: 29,
            shareCount: 16,
        },
        {
            id: 2,
            title: 'The most innovative things happening in coffee',
            date: 'Mar 19',
            labelform: 24,
            shareCount: 12,
        },
    ],
    Miscelaneos: [
        {
            id: 1,
            title: 'Ask Me Anything: 10 answers to your questions about coffee',
            date: '2d ago',
            labelform: 9,
            shareCount: 5,
        },
        {
            id: 2,
            title: "The worst advice we've ever heard about coffee",
            date: '4d ago',
            labelform: 1,
            shareCount: 2,
        },
    ],
})

console.log("=>(tabs.vue:201) tabsform", tabsform);
watch(() => _.cloneDeep(tabsform),
  debounce(() => {
    const params = pickBy(toRaw(tabsform)); // Elimina la reactividad para obtener un objeto plano.
    console.log("=>(tabs.vue:205) params", params);
    router.get(route("Casa.index"), params, {
      replace: true,
      preserveState: true,
      preserveScroll: true,
    });
}, 150));
</script>
