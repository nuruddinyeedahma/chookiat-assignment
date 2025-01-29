<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
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
</script>

<template>
    <AppLayout title="User Management">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                User Management
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-end mb-6">
                        <PrimaryButton @click="showingUserModal = true">
                            Add User
                        </PrimaryButton>
                    </div>

                    <!-- Users Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left">Name</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">Email</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">Roles</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">Actions</th>
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
                                            Edit
                                        </button>
                                        <button
                                            class="text-red-600 hover:text-red-900"
                                            @click="confirmUserDeletion(user)"
                                        >
                                            Delete
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
                Create User
            </template>

            <template #content>
                <div class="space-y-6">
                    <div>
                        <InputLabel for="name" value="Name" />
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
                        <InputLabel for="email" value="Email" />
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
                        <InputLabel for="password" value="Password" />
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
                        <InputLabel value="Roles" />
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
                    Cancel
                </SecondaryButton>
                <PrimaryButton
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="createUser"
                >
                    Create
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Edit User Modal -->
        <DialogModal :show="editingUser" @close="editingUser = false">
            <template #title>
                Edit User
            </template>

            <template #content>
                <div class="space-y-6">
                    <div>
                        <InputLabel for="edit-name" value="Name" />
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
                        <InputLabel for="edit-email" value="Email" />
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
                        <InputLabel value="Roles" />
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
                    Cancel
                </SecondaryButton>
                <PrimaryButton
                    class="ml-3"
                    :class="{ 'opacity-25': editForm.processing }"
                    :disabled="editForm.processing"
                    @click="updateUser"
                >
                    Update
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Delete User Confirmation Modal -->
        <DialogModal :show="userBeingDeleted" @close="userBeingDeleted = null">
            <template #title>
                Delete User
            </template>

            <template #content>
                Are you sure you want to delete this user?
            </template>

            <template #footer>
                <SecondaryButton @click="userBeingDeleted = null">
                    Cancel
                </SecondaryButton>
                <DangerButton
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="deleteUser"
                >
                    Delete
                </DangerButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>
