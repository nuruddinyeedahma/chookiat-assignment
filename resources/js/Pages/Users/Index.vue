<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    users: Array,
    roles: Array,
    currentUser: Object,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    roles: [],
});

const editForm = useForm({
    id: '',
    name: '',
    email: '',
    roles: [],
});

const showingUserModal = ref(false);
const editingUser = ref(false);
const userBeingDeleted = ref(null);
const userBeingReset = ref(null);

const createUser = () => {
    if (form.roles.length === 0) {
        form.setError('roles', 'Please select at least one role');
        return;
    }

    form.post(route('users.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            showingUserModal.value = false;
        },
    });
};

const editUser = (user) => {
    editForm.id = user.id;
    editForm.name = user.name;
    editForm.email = user.email;
    editForm.roles = user.roles.map(role => role.name);
    editingUser.value = true;
};

const updateUser = () => {
    if (editForm.roles.length === 0) {
        editForm.setError('roles', 'Please select at least one role');
        return;
    }

    editForm.put(route('users.update', editForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            editingUser.value = false;
        },
    });
};

const confirmUserDeletion = (user) => {
    userBeingDeleted.value = user;
};

const deleteUser = () => {
    form.delete(route('users.destroy', userBeingDeleted.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            userBeingDeleted.value = null;
        },
    });
};

const resetUserData = (user) => {
    userBeingReset.value = user;
};

const confirmResetData = () => {
    form.post(route('users.reset-data', userBeingReset.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            userBeingReset.value = null;
        },
    });
};

const toggleUserStatus = (user) => {
    form.put(route('users.toggle-status', user.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout title="การจัดการผู้ใช้">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                การจัดการผู้ใช้
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-end mb-6">
                        <PrimaryButton @click="showingUserModal = true">
                            เพิ่มผู้ใช้
                        </PrimaryButton>
                    </div>

                    <!-- Users Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left">ชื่อ</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">อีเมล</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">บทบาท</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">การดำเนินการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="user in users" :key="user.id">
                                    <td class="px-6 py-4">{{ user.name }}</td>
                                    <td class="px-6 py-4">{{ user.email }}</td>
                                    <td class="px-6 py-4">
                                        {{ user.roles.map(role => role.name).join(', ') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <button
                                            class="text-indigo-600 hover:text-indigo-900 mr-4"
                                            @click="editUser(user)"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </button>
                                        <button
                                            v-if="user.id !== props.currentUser.id"
                                            class="text-yellow-600 hover:text-yellow-900 mr-4"
                                            @click="resetUserData(user)"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                            </svg>
                                        </button>
                                        <button
                                            v-if="user.id !== props.currentUser.id"
                                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                            :class="{
                                                'bg-green-600 hover:bg-green-700': user.is_active,
                                                'bg-red-600 hover:bg-red-700': !user.is_active
                                            }"
                                            @click="toggleUserStatus(user)"
                                        >
                                            <span
                                                class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                                :class="{
                                                    'translate-x-5': user.is_active,
                                                    'translate-x-0': !user.is_active
                                                }"
                                            >
                                                <span
                                                    class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity"
                                                    :class="{
                                                        'opacity-0 duration-100 ease-out': user.is_active,
                                                        'opacity-100 duration-200 ease-in': !user.is_active
                                                    }"
                                                    aria-hidden="true"
                                                >
                                                    <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 12 12">
                                                        <path d="M7 6V3m0 0L5 5m2-2l2 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <span
                                                    class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity"
                                                    :class="{
                                                        'opacity-100 duration-200 ease-in': user.is_active,
                                                        'opacity-0 duration-100 ease-out': !user.is_active
                                                    }"
                                                    aria-hidden="true"
                                                >
                                                    <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 12 12">
                                                        <path d="M5 6v3m0 0l2-2m-2 2L3 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                            </span>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create User Modal -->
        <DialogModal :show="showingUserModal" @close="showingUserModal = false">
            <template #title>
                เพิ่มผู้ใช้
            </template>

            <template #content>
                <div class="space-y-6">
                    <div>
                        <InputLabel for="name" value="ชื่อ" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="email" value="อีเมล" />
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="password" value="รหัสผ่าน" />
                        <TextInput
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel value="บทบาท" />
                        <div class="mt-2 space-y-2">
                            <div v-for="role in roles" :key="role.name">
                                <label class="flex items-center">
                                    <Checkbox v-model:checked="form.roles" :value="role.name" />
                                    <span class="ml-2 text-sm text-gray-600">{{ role.name }}</span>
                                </label>
                            </div>
                        </div>
                        <InputError :message="form.errors.roles" class="mt-2" />
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="showingUserModal = false">
                    ยกเลิก
                </SecondaryButton>
                <PrimaryButton
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="createUser"
                >
                    สร้าง
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Edit User Modal -->
        <DialogModal :show="editingUser" @close="editingUser = false">
            <template #title>
                แก้ไขผู้ใช้
            </template>

            <template #content>
                <div class="space-y-6">
                    <div>
                        <InputLabel for="edit-name" value="ชื่อ" />
                        <TextInput
                            id="edit-name"
                            v-model="editForm.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="editForm.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="edit-email" value="อีเมล" />
                        <TextInput
                            id="edit-email"
                            v-model="editForm.email"
                            type="email"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="editForm.errors.email" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel value="บทบาท" />
                        <div class="mt-2 space-y-2">
                            <div v-for="role in roles" :key="role.name">
                                <label class="flex items-center">
                                    <Checkbox v-model:checked="editForm.roles" :value="role.name" />
                                    <span class="ml-2 text-sm text-gray-600">{{ role.name }}</span>
                                </label>
                            </div>
                        </div>
                        <InputError :message="editForm.errors.roles" class="mt-2" />
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="editingUser = false">
                    ยกเลิก
                </SecondaryButton>
                <PrimaryButton
                    class="ml-3"
                    :class="{ 'opacity-25': editForm.processing }"
                    :disabled="editForm.processing"
                    @click="updateUser"
                >
                    อัปเดต
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Delete User Confirmation Modal -->
        <DialogModal :show="userBeingDeleted" @close="userBeingDeleted = null">
            <template #title>
                ลบผู้ใช้
            </template>

            <template #content>
                คุณแน่ใจหรือไม่ว่าต้องการลบผู้ใช้นี้?
            </template>

            <template #footer>
                <SecondaryButton @click="userBeingDeleted = null">
                    ยกเลิก
                </SecondaryButton>
                <DangerButton
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="deleteUser"
                >
                    ลบ
                </DangerButton>
            </template>
        </DialogModal>

        <!-- Reset User Data Confirmation Modal -->
        <DialogModal :show="userBeingReset" @close="userBeingReset = null">
            <template #title>
                รีเซ็ตข้อมูลผู้ใช้
            </template>

            <template #content>
                คุณแน่ใจหรือไม่ที่จะรีเซ็ตข้อมูลของผู้ใช้นี้?
            </template>

            <template #footer>
                <SecondaryButton @click="userBeingReset = null">
                    ยกเลิก
                </SecondaryButton>
                <DangerButton
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="confirmResetData"
                >
                    รีเซ็ต
                </DangerButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>
