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

interface Props {
    certificateName?: string;
    status?: string;
    error?: string;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Certificate settings',
        href: '/settings/certificate',
    },
];

const form = useForm({
    certificate: null as File | null,
});

const page = usePage();
const successMessage = page.props.flash?.success;
const errorMessage = page.props.flash?.error;

const handleFileChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    form.certificate = input.files && input.files.length > 0 ? input.files[0] : null;
};

const submit = () => {
    form.post(route('certificate.upload'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();

            const input = document.getElementById('certificate') as HTMLInputElement;
            if (input) input.value = '';
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Certificate settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Certificate Upload" description="Upload a .pem certificate file" />

                <!-- Success message -->
                <div v-if="successMessage" class="text-sm text-green-600">
                    {{ successMessage }}
                </div>

                <!-- Error message -->
                <div v-if="errorMessage" class="text-sm text-red-600">
                    {{ errorMessage }}
                </div>

                <!-- Current certificate -->
                <div v-if="certificateName" class="flex items-center space-x-2">
                    <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-sm text-gray-600">{{ certificateName }}</span>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="certificate">Certificate (.pem)</Label>
                        <Input
                            id="certificate"
                            type="file"
                            accept=".pem"
                            class="mt-1 block w-full"
                            @change="handleFileChange"
                        />
                        <InputError class="mt-2" :message="form.errors.certificate" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing || !form.certificate">Upload</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Uploaded.</p>
                        </Transition>
                    </div>
                </form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>