<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
        onError: () => {
            if (form.errors.email) {
                form.reset('password');
            }
        },
    });
};
</script>

<template>
    <div>
        <Head title="เข้าสู่ระบบ" />

        <AuthenticationCard>
            <template #logo>
                <AuthenticationCardLogo />
            </template>

            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-700">ระบบจัดการข้อมูลสินเชื่อรถยนต์</h2>
                <p class="text-sm text-gray-600">กรุณาเข้าสู่ระบบเพื่อดำเนินการ</p>
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

                <div class="mt-4">
                    <InputLabel for="password" value="รหัสผ่าน" />
                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full"
                        required
                        autocomplete="current-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="block mt-4">
                    <label class="flex items-center">
                        <Checkbox v-model:checked="form.remember" name="remember" />
                        <span class="ms-2 text-sm text-gray-600">จดจำการเข้าสู่ระบบ</span>
                    </label>
                </div>

                <div class="mt-4">
                    <PrimaryButton class="w-full justify-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        เข้าสู่ระบบ
                    </PrimaryButton>
                </div>
            </form>

            <div class="mt-6 border-t border-gray-200 pt-6">
                <div class="flex justify-between items-center">
                    <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm text-gray-600 hover:text-gray-900">
                        ลืมรหัสผ่าน?
                    </Link>

                    <Link :href="route('register')" class="text-sm text-gray-600 hover:text-gray-900">
                        สมัครสมาชิกใหม่
                    </Link>
                </div>
            </div>
        </AuthenticationCard>
    </div>
</template>
