<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="ลืมรหัสผ่าน" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-700">ระบบจัดการข้อมูลรถยนต์</h2>
            <p class="text-sm text-gray-600">รีเซ็ตรหัสผ่าน</p>
        </div>

        <div class="mb-4 text-sm text-gray-600">
            หากคุณลืมรหัสผ่าน ไม่ต้องกังวล เพียงกรอกที่อยู่อีเมลของคุณ ระบบจะส่งลิงก์สำหรับรีเซ็ตรหัสผ่านใหม่ไปยังอีเมลของคุณ
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="อีเมล" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    ส่งลิงก์รีเซ็ตรหัสผ่าน
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>
