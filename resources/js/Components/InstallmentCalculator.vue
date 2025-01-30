<script setup>
import { ref, computed, watch } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    initialAmount: {
        type: [Number, String],
        default: ''
    }
});

const loanAmount = ref(props.initialAmount.toString());
const interestRate = ref(0);
const selectedInstallment = ref('');
const error = ref('');
const calculationType = ref('flat'); // 'flat' or 'amortization'

const validateLoanAmount = (event) => {
    const value = event.target.value;

    // Check for invalid characters (- or e) and clear immediately
    if (value.toLowerCase().includes('e') || value.includes('-')) {
        alert('ไม่สามารถป้อนเครื่องหมายลบหรือตัวอักษร');
        loanAmount.value = 0;
        event.target.value = 0;
        return;
    }

    if (value === '') {
        loanAmount.value = 0;
        error.value = '';
        return;
    }

    const number = parseFloat(value);
    if (isNaN(number)) {
        alert('กรุณาใส่ตัวเลขเท่านั้น');
        loanAmount.value = 0;
        event.target.value = 0;
        return;
    }

    error.value = '';
    loanAmount.value = number;
};

const preventInvalidInput = (event) => {
    // Prevent minus sign and 'e' character
    if (event.key === '-' || event.key.toLowerCase() === 'e') {
        alert('ไม่สามารถป้อนเครื่องหมายลบหรือตัวอักษร');
        event.preventDefault();
        loanAmount.value = 0;
    }
};

const validateInterestRate = (event) => {
    const value = event.target.value;

    // Check for invalid characters (- or e) and clear immediately
    if (value.toLowerCase().includes('e') || value.includes('-')) {
        alert('ไม่สามารถป้อนเครื่องหมายลบหรือตัวอักษร');
        interestRate.value = 0;
        event.target.value = 0;
        return;
    }

    if (value === '') {
        interestRate.value = 0;
        return;
    }

    const number = parseFloat(value);
    if (isNaN(number)) {
        alert('กรุณาใส่ตัวเลขเท่านั้น');
        interestRate.value = 0;
        event.target.value = 0;
        return;
    }

    interestRate.value = number;
};

const preventInvalidInputInterest = (event) => {
    // Prevent minus sign and 'e' character
    if (event.key === '-' || event.key.toLowerCase() === 'e') {
        alert('ไม่สามารถป้อนเครื่องหมายลบหรือตัวอักษร');
        event.preventDefault();
        interestRate.value = 0;
    }
};

const calculateFlatRatePayment = (principal, annualRate, months) => {
    const years = months / 12;
    const totalInterest = (principal * (annualRate / 100) * years);
    const totalPayment = principal + totalInterest;
    return totalPayment / months;
};

const calculateAmortizationPayment = (principal, annualRate, months) => {
    const monthlyRate = (annualRate / 100) / 12;
    const payment = principal * (monthlyRate * Math.pow(1 + monthlyRate, months)) /
                   (Math.pow(1 + monthlyRate, months) - 1);
    return payment;
};

const installmentTable = computed(() => {
    // if (!loanAmount.value || !interestRate.value) return []; // ถ้ายังไม่ได้ป้อนจํานวนเงินหรืออัตราดอกเบี้ยหรือเท่ากับ 0

    const table = [];
    for (let months = 12; months <= 84; months += 12) {
        const monthlyPayment = calculationType.value === 'flat'
            ? calculateFlatRatePayment(
                parseFloat(loanAmount.value),
                parseFloat(interestRate.value),
                months
            )
            : calculateAmortizationPayment(
                parseFloat(loanAmount.value),
                parseFloat(interestRate.value),
                months
            );

        table.push({
            months,
            monthlyPayment,
            isHighPayment: monthlyPayment > 5000
        });
    }
    return table;
});

const hasInputValues = computed(() => {
    return loanAmount.value !== '' || interestRate.value !== '';
});

const clearForm = () => {
    loanAmount.value = 0;
    interestRate.value = 0;
    selectedInstallment.value = '';
    error.value = '';
};
</script>

<template>
    <div class="space-y-6">
        <!-- Input Fields -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <div>
                <InputLabel for="loan-amount" value="ยอดสินเชื่อ" />
                <TextInput
                    id="loan-amount"
                    type="number"
                    min="0"
                    step="any"
                    class="mt-1 block w-full"
                    v-model="loanAmount"
                    @input="validateLoanAmount($event)"
                    @keydown="preventInvalidInput"
                />
                <InputError :message="error" class="mt-2" />
            </div>

            <div>
                <InputLabel for="interest-rate" value="อัตราดอกเบี้ย (%)" />
                <TextInput
                    id="interest-rate"
                    v-model="interestRate"
                    type="number"
                    min="0"
                    step="0.01"
                    class="mt-1 block w-full"
                    @input="validateInterestRate($event)"
                    @keydown="preventInvalidInputInterest"
                />
            </div>

            <div>
                <InputLabel for="installment-period" value="ระยะเวลาผ่อนชำระ" />
                <select
                    id="installment-period"
                    v-model="selectedInstallment"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    required
                >
                    <option value="">เลือกระยะเวลา</option>
                    <option v-for="months in [12, 24, 36, 48, 60, 72, 84]" :key="months" :value="months">
                        {{ months }} งวด ({{ Math.floor(months/12) }} {{ months === 12 ? 'ปี' : 'ปี' }})
                    </option>
                </select>
            </div>
        </div>

        <!-- Calculation Type Selection -->
        <div class="flex items-center space-x-4">
            <InputLabel value="วิธีการคำนวณ:" class="mb-0" />
            <div class="flex items-center space-x-2">
                <input
                    type="radio"
                    id="flat-rate"
                    v-model="calculationType"
                    value="flat"
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-full shadow-sm"
                />
                <label for="flat-rate" class="text-sm text-gray-700">แบบคงที่</label>
            </div>
            <div class="flex items-center space-x-2">
                <input
                    type="radio"
                    id="amortization"
                    v-model="calculationType"
                    value="amortization"
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-full shadow-sm"
                />
                <label for="amortization" class="text-sm text-gray-700">แบบลดต้นลดดอก</label>
            </div>
        </div>

        <!-- Clear Button -->
        <div v-if="hasInputValues" class="flex justify-end">
            <PrimaryButton @click="clearForm">
                ล้างข้อมูล
            </PrimaryButton>
        </div>

        <!-- Installment Table -->
        <div v-if="installmentTable.length" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left">จำนวนงวด</th>
                        <th class="px-6 py-3 bg-gray-50 text-left">ค่างวดต่อเดือน</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr
                        v-for="row in installmentTable"
                        :key="row.months"
                        :class="{
                            'bg-indigo-50': selectedInstallment === row.months,
                            'hover:bg-gray-50': selectedInstallment !== row.months
                        }"
                        @click="selectedInstallment = row.months"
                        class="cursor-pointer"
                    >
                        <td class="px-6 py-4">{{ row.months }} งวด</td>
                        <td
                            class="px-6 py-4"
                            :class="{ 'text-red-600': row.isHighPayment }"
                        >
                            ฿{{ row.monthlyPayment.toLocaleString('th-TH', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
