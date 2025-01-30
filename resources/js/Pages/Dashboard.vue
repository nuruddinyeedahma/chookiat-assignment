<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
    totalVehicles: Number,
    totalUsers: Number,
    recentVehicles: Array,
    isAdmin: Boolean,
});

const stats = [
    { name: 'จำนวนรถทั้งหมด', value: props.totalVehicles, href: route('vehicles.index') },
    { name: 'จำนวนผู้ใช้ทั้งหมด', value: props.totalUsers, href: route('users.index'), adminOnly: true },
];
</script>

<template>
    <AppLayout title="แดชบอร์ด">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                แดชบอร์ด
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats Section -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 mb-8">
                    <Link
                        v-for="stat in stats"
                        :key="stat.name"
                        :href="stat.href"
                        v-show="!stat.adminOnly || isAdmin"
                        class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 hover:bg-gray-50 transition-colors"
                    >
                        <div class="flex items-center">
                            <div class="flex-1">
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    {{ stat.name }}
                                </dt>
                                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                    {{ stat.value }}
                                </dd>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">เมนูลัด</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <Link
                            :href="route('vehicles.create')"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 active:bg-indigo-600 transition"
                        >
                            เพิ่มรถใหม่
                        </Link>
                        <Link
                            v-if="isAdmin"
                            :href="route('users.create')"
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-200 active:bg-green-600 transition"
                        >
                            เพิ่มผู้ใช้ใหม่
                        </Link>
                    </div>
                </div>

                <!-- Recent Vehicles -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">รถล่าสุด</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ยี่ห้อ</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รุ่น</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ปี</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ราคา</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สต็อก</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="vehicle in recentVehicles" :key="vehicle.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ vehicle.make }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ vehicle.model }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ vehicle.year }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ vehicle.price }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ vehicle.stock }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
