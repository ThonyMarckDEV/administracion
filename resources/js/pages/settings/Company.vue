<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { BreadcrumbItem } from '@/types';
import { ref } from 'vue';

interface Props {
    company: {
        ruc: string;
        razon_social: string;
        nombre_comercial: string;
        address: {
            ubigueo: string;
            departamento: string;
            provincia: string;
            distrito: string;
            urbanizacion: string;
            direccion: string;
            cod_local: string;
        };
    };
    status?: string;
    error?: string;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Company settings',
        href: '/settings/company',
    },
];

const form = useForm({
    ruc: '',
    razon_social: '',
    nombre_comercial: '',
    ubigueo: '',
    departamento: '',
    provincia: '',
    distrito: '',
    urbanizacion: '',
    direccion: '',
    cod_local: '',
});

const page = usePage();

// Populate form with company data when component mounts
form.ruc = page.props.company.ruc;
form.razon_social = page.props.company.razon_social;
form.nombre_comercial = page.props.company.nombre_comercial;
form.ubigeo = page.props.company.address.ubigueo;
form.departamento = page.props.company.address.departamento;
form.provincia = page.props.company.address.provincia;
form.distrito = page.props.company.address.distrito;
form.urbanizacion = page.props.company.address.urbanizacion;
form.direccion = page.props.company.address.direccion;
form.cod_local = page.props.company.address.cod_local;

const successMessage = page.props.flash?.success;
const errorMessage = page.props.flash?.error;

// State to control edit mode
const isEditing = ref(false);

// Toggle edit mode
const toggleEdit = () => {
    isEditing.value = !isEditing.value;
};

const submit = () => {
    form.post(route('company.update'), {
        preserveScroll: true,
        onSuccess: () => {
            isEditing.value = false;
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Company settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Company Details" description="Edit your company information" />

                <!-- Success message -->
                <div v-if="successMessage" class="text-sm text-green-600">
                    {{ successMessage }}
                </div>

                <!-- Error message -->
                <div v-if="errorMessage" class="text-sm text-red-600">
                    {{ errorMessage }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-6">
                        <!-- Row 1: RUC and Razón Social -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- RUC -->
                            <div class="grid gap-2">
                                <Label for="ruc">RUC</Label>
                                <Input
                                    id="ruc"
                                    v-model="form.ruc"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="20123456789"
                                    :disabled="!isEditing"
                                />
                                <InputError class="mt-2" :message="form.errors.ruc" />
                            </div>

                            <!-- Razón Social -->
                            <div class="grid gap-2">
                                <Label for="razon_social">Razón Social</Label>
                                <Input
                                    id="razon_social"
                                    v-model="form.razon_social"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="GREEN SAC"
                                    :disabled="!isEditing"
                                />
                                <InputError class="mt-2" :message="form.errors.razon_social" />
                            </div>
                        </div>

                        <!-- Row 2: Nombre Comercial and Ubigeo -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nombre Comercial -->
                            <div class="grid gap-2">
                                <Label for="nombre_comercial">Nombre Comercial</Label>
                                <Input
                                    id="nombre_comercial"
                                    v-model="form.nombre_comercial"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="GREEN"
                                    :disabled="!isEditing"
                                />
                                <InputError class="mt-2" :message="form.errors.nombre_comercial" />
                            </div>

                            <!-- Ubigeo -->
                            <div class="grid gap-2">
                                <Label for="ubigeo">Ubigeo</Label>
                                <Input
                                    id="ubigeo"
                                    v-model="form.ubigeo"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="150101"
                                    :disabled="!isEditing"
                                />
                                <InputError class="mt-2" :message="form.errors.ubigueo" />
                            </div>
                        </div>

                        <!-- Row 3: Departamento and Provincia -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Departamento -->
                            <div class="grid gap-2">
                                <Label for="departamento">Departamento</Label>
                                <Input
                                    id="departamento"
                                    v-model="form.departamento"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="LIMA"
                                    :disabled="!isEditing"
                                />
                                <InputError class="mt-2" :message="form.errors.departamento" />
                            </div>

                            <!-- Provincia -->
                            <div class="grid gap-2">
                                <Label for="provincia">Provincia</Label>
                                <Input
                                    id="provincia"
                                    v-model="form.provincia"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="LIMA"
                                    :disabled="!isEditing"
                                />
                                <InputError class="mt-2" :message="form.errors.provincia" />
                            </div>
                        </div>

                        <!-- Row 4: Distrito and Urbanización -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Distrito -->
                            <div class="grid gap-2">
                                <Label for="distrito">Distrito</Label>
                                <Input
                                    id="distrito"
                                    v-model="form.distrito"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="LIMA"
                                    :disabled="!isEditing"
                                />
                                <InputError class="mt-2" :message="form.errors.distrito" />
                            </div>

                            <!-- Urbanización -->
                            <div class="grid gap-2">
                                <Label for="urbanizacion">Urbanización</Label>
                                <Input
                                    id="urbanizacion"
                                    v-model="form.urbanizacion"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="-"
                                    :disabled="!isEditing"
                                />
                                <InputError class="mt-2" :message="form.errors.urbanizacion" />
                            </div>
                        </div>

                        <!-- Row 5: Dirección and Código Local -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Dirección -->
                            <div class="grid gap-2">
                                <Label for="direccion">Dirección</Label>
                                <Input
                                    id="direccion"
                                    v-model="form.direccion"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Av. Villa Nueva 221"
                                    :disabled="!isEditing"
                                />
                                <InputError class="mt-2" :message="form.errors.direccion" />
                            </div>

                            <!-- Código Local -->
                            <div class="grid gap-2">
                                <Label for="cod_local">Código Local</Label>
                                <Input
                                    id="cod_local"
                                    v-model="form.cod_local"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="0000"
                                    :disabled="!isEditing"
                                />
                                <InputError class="mt-2" :message="form.errors.cod_local" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button
                            v-if="!isEditing"
                            type="button"
                            variant="outline"
                            @click="toggleEdit"
                        >
                            Edit
                        </Button>
                        <Button
                            v-if="isEditing"
                            type="submit"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Saving...</span>
                            <span v-else>Save</span>
                        </Button>
                        <Button
                            v-if="isEditing"
                            type="button"
                            variant="outline"
                            @click="toggleEdit"
                            :disabled="form.processing"
                        >
                            Cancel
                        </Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                        </Transition>
                    </div>
                </form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>