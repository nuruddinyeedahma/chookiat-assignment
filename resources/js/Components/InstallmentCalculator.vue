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
const interestRate = ref('');
const selectedInstallment = ref(null);
const error = ref('');

const validateLoanAmount = (value) => {
    if (value === '') {
        loanAmount.value = '';
        error.value = '';
        return;
    }

    const number = parseFloat(value);
    if (isNaN(number) || number < 0) {
        error.value = 'Please enter a valid positive number';
        loanAmount.value = '';
        return;
    }

    error.value = '';
    loanAmount.value = number;
};

const calculateMonthlyPayment = (principal, annualRate, months) => {
    const monthlyRate = (annualRate / 100) / 12;
    const payment = principal * (monthlyRate * Math.pow(1 + monthlyRate, months)) /
                   (Math.pow(1 + monthlyRate, months) - 1);
    return payment;
};

const installmentTable = computed(() => {
    if (!loanAmount.value || !interestRate.value) return [];

    const table = [];
    for (let months = 12; months <= 84; months += 12) {
        const monthlyPayment = calculateMonthlyPayment(
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
    loanAmount.value = '';
    interestRate.value = '';
    selectedInstallment.value = null;
    error.value = '';
};
</script>

<template>
    <div class="space-y-6">
        <!-- Input Fields -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <InputLabel for="loan-amount" value="Loan Amount" />
                <TextInput
                    id="loan-amount"
                    v-model="loanAmount"
                    type="text"
                    class="mt-1 block w-full"
                    @input="validateLoanAmount($event.target.value)"
                    required
                />
                <InputError :message="error" class="mt-2" />
            </div>

            <div>
                <InputLabel for="interest-rate" value="Interest Rate (%)" />
                <TextInput
                    id="interest-rate"
                    v-model="interestRate"
                    type="number"
                    step="0.01"
                    class="mt-1 block w-full"
                    required
                />
            </div>
        </div>

        <!-- Clear Button -->
        <div v-if="hasInputValues" class="flex justify-end">
            <PrimaryButton @click="clearForm">
                Clear
            </PrimaryButton>
        </div>

        <!-- Installment Table -->
        <div v-if="installmentTable.length" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left">Number of Installments</th>
                        <th class="px-6 py-3 bg-gray-50 text-left">Monthly Payment</th>
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
                        <td class="px-6 py-4">{{ row.months }} months</td>
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
