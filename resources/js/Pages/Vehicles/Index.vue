<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import InstallmentCalculator from '@/Components/InstallmentCalculator.vue';

const props = defineProps({
    vehicles: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    make: '',
    model: '',
    year: '',
    color: '',
    price: '',
    stock: '',
    description: '',
});

const editForm = useForm({
    id: '',
    make: '',
    model: '',
    year: '',
    color: '',
    price: '',
    stock: '',
    description: '',
});

const showingVehicleModal = ref(false);
const editingVehicle = ref(false);
const vehicleBeingDeleted = ref(null);
const showingCalculator = ref(false);
const selectedVehicle = ref(null);

const createVehicle = () => {
    form.post(route('vehicles.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            showingVehicleModal.value = false;
        },
    });
};

const editVehicle = (vehicle) => {
    editForm.id = vehicle.id;
    editForm.make = vehicle.make;
    editForm.model = vehicle.model;
    editForm.year = vehicle.year;
    editForm.color = vehicle.color;
    editForm.price = vehicle.price;
    editForm.stock = vehicle.stock;
    editForm.description = vehicle.description;
    editingVehicle.value = true;
};

const updateVehicle = () => {
    editForm.put(route('vehicles.update', editForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            editingVehicle.value = false;
        },
    });
};

const confirmVehicleDeletion = (vehicle) => {
    vehicleBeingDeleted.value = vehicle;
};

const deleteVehicle = () => {
    form.delete(route('vehicles.destroy', vehicleBeingDeleted.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            vehicleBeingDeleted.value = null;
        },
    });
};

const showCalculator = (vehicle) => {
    selectedVehicle.value = vehicle;
    showingCalculator.value = true;
};
</script>

<template>
    <AppLayout title="รายการรถยนต์">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                รายการรถยนต์
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-end mb-6">
                        <PrimaryButton @click="showingVehicleModal = true">
                            เพิ่มรถยนต์
                        </PrimaryButton>
                    </div>

                    <!-- Vehicles Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left">ยี่ห้อ</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">รุ่น</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">ปี</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">สี</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">ราคา</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">จำนวน</th>
                                    <th class="px-6 py-3 bg-gray-50 text-center">การดำเนินการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="vehicle in vehicles.data" :key="vehicle.id">
                                    <td class="px-6 py-4">{{ vehicle.make }}</td>
                                    <td class="px-6 py-4">{{ vehicle.model }}</td>
                                    <td class="px-6 py-4">{{ vehicle.year }}</td>
                                    <td class="px-6 py-4">{{ vehicle.color }}</td>
                                    <td class="px-6 py-4">฿{{ Number(vehicle.price).toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</td>
                                    <td class="px-6 py-4">{{ vehicle.stock }}</td>
                                    <td class="px-6 py-4">
                                        <button
                                            class="text-indigo-600 hover:text-indigo-900 mr-4"
                                            @click="showCalculator(vehicle)"
                                        >
                                            คำนวณค่างวด
                                        </button>
                                        <button
                                            class="text-indigo-600 hover:text-indigo-900 mr-4"
                                            @click="editVehicle(vehicle)"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </button>
                                        <button
                                            class="text-red-600 hover:text-red-900"
                                            @click="confirmVehicleDeletion(vehicle)"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6" v-if="vehicles.last_page > 1">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-700">
                                แสดง {{ vehicles.from }}-{{ vehicles.to }} จาก {{ vehicles.total }} รายการ
                            </div>
                            <div class="flex space-x-2">
                                <Link
                                    v-for="page in vehicles.links"
                                    :key="page.label"
                                    :href="page.url"
                                    :class="[
                                        'px-4 py-2 border rounded-md text-sm',
                                        page.active
                                            ? 'bg-indigo-600 text-white border-indigo-600'
                                            : 'text-gray-700 hover:bg-gray-50',
                                        !page.url && 'opacity-50 cursor-not-allowed'
                                    ]"
                                    v-html="page.label"
                                    :preserve-scroll="true"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Vehicle Modal -->
        <DialogModal :show="showingVehicleModal" @close="showingVehicleModal = false">
            <template #title>
                เพิ่มรถยนต์
            </template>

            <template #content>
                <div class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <InputLabel for="make" value="ยี่ห้อ" />
                            <TextInput
                                id="make"
                                v-model="form.make"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.make" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="model" value="รุ่น" />
                            <TextInput
                                id="model"
                                v-model="form.model"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.model" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="year" value="ปี" />
                            <TextInput
                                id="year"
                                v-model="form.year"
                                type="number"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.year" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="color" value="สี" />
                            <TextInput
                                id="color"
                                v-model="form.color"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.color" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="price" value="ราคา" />
                            <TextInput
                                id="price"
                                v-model="form.price"
                                type="number"
                                step="0.01"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.price" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="stock" value="จำนวน" />
                            <TextInput
                                id="stock"
                                v-model="form.stock"
                                type="number"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.stock" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="description" value="รายละเอียด" />
                        <textarea
                            id="description"
                            v-model="form.description"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                            rows="3"
                        />
                        <InputError :message="form.errors.description" class="mt-2" />
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="showingVehicleModal = false">
                    ยกเลิก
                </SecondaryButton>
                <PrimaryButton
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="createVehicle"
                >
                    บันทึก
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Edit Vehicle Modal -->
        <DialogModal :show="editingVehicle" @close="editingVehicle = false">
            <template #title>
                แก้ไขรถยนต์
            </template>

            <template #content>
                <div class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <InputLabel for="edit-make" value="ยี่ห้อ" />
                            <TextInput
                                id="edit-make"
                                v-model="editForm.make"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="editForm.errors.make" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="edit-model" value="รุ่น" />
                            <TextInput
                                id="edit-model"
                                v-model="editForm.model"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="editForm.errors.model" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="edit-year" value="ปี" />
                            <TextInput
                                id="edit-year"
                                v-model="editForm.year"
                                type="number"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="editForm.errors.year" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="edit-color" value="สี" />
                            <TextInput
                                id="edit-color"
                                v-model="editForm.color"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="editForm.errors.color" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="edit-price" value="ราคา" />
                            <TextInput
                                id="edit-price"
                                v-model="editForm.price"
                                type="number"
                                step="0.01"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="editForm.errors.price" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="edit-stock" value="จำนวน" />
                            <TextInput
                                id="edit-stock"
                                v-model="editForm.stock"
                                type="number"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="editForm.errors.stock" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="edit-description" value="รายละเอียด" />
                        <textarea
                            id="edit-description"
                            v-model="editForm.description"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                            rows="3"
                        />
                        <InputError :message="editForm.errors.description" class="mt-2" />
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="editingVehicle = false">
                    ยกเลิก
                </SecondaryButton>
                <PrimaryButton
                    class="ml-3"
                    :class="{ 'opacity-25': editForm.processing }"
                    :disabled="editForm.processing"
                    @click="updateVehicle"
                >
                    บันทึก
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Delete Vehicle Confirmation Modal -->
        <DialogModal :show="vehicleBeingDeleted" @close="vehicleBeingDeleted = null">
            <template #title>
                ลบรถยนต์
            </template>

            <template #content>
                คุณแน่ใจหรือไม่ที่จะลบรถยนต์คันนี้?
            </template>

            <template #footer>
                <SecondaryButton @click="vehicleBeingDeleted = null">
                    ยกเลิก
                </SecondaryButton>
                <DangerButton
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="deleteVehicle"
                >
                    ลบ
                </DangerButton>
            </template>
        </DialogModal>

        <!-- Installment Calculator Modal -->
        <DialogModal :show="showingCalculator" @close="showingCalculator = false">
            <template #title>
                คำนวณค่างวดสำหรับ {{ selectedVehicle?.make }} {{ selectedVehicle?.model }}
            </template>

            <template #content>
                <InstallmentCalculator
                    v-if="selectedVehicle"
                    :initial-amount="selectedVehicle.price"
                />
            </template>

            <template #footer>
                <SecondaryButton @click="showingCalculator = false">
                    ปิด
                </SecondaryButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>
