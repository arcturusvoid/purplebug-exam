<template>
    <SideBar>
        <div class="flex flex-col">
            <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                    <div class="overflow-hidden">
                        <div class="flex justify-between mb-3">
                            <h3 class="text-xl">Expenses</h3>
                            <h4 class="text-xl">Expense Management > Expenses</h4>
                        </div>
                        <div class="px-5">
                            <table v-if="expenses && expenses.length" class="min-w-full text-left text-sm font-light">
                                <thead class="border-b font-medium dark:border-neutral-500">
                                    <tr>
                                        <th scope="col" class="px-6 py-4">Expense Category</th>
                                        <th scope="col" class="px-6 py-4">Amount</th>
                                        <th scope="col" class="px-6 py-4">Entry Date</th>
                                        <th scope="col" class="px-6 py-4">Created at</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="expense in expenses" :key="expense.id"
                                        @click="toggleModal(editMode = true, expense)"
                                        class="border-b dark:border-neutral-500 hover:bg-gray-200">
                                        <td class="whitespace-nowrap px-6 py-4 font-medium"> {{ expense?.expense_category == null ? 'No Category' : expense?.expense_category.name }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">${{ expense?.amount }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ expense?.entry_date }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ formatDate(expense?.created_at) }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <div v-else class="flex justify-center">
                                <div v-if="expenses == null">
                                    <Loading />
                                </div>
                                <div v-else>
                                    Empty table
                                </div>

                            </div>

                        </div>


                        <div class="mt-12 flex justify-end">
                            <button @click="toggleModal"
                                class="bg-gray-500 hover:bg-gray-600 text-white py-1 px-4 border border-gray-800 rounded">
                                Add Expense
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SideBar>

    <div v-if="showToast">
        <Toast message="Operation Success!" />
    </div>

    <Modal v-if="showModal" :title="isEditMode ? 'Update Expense' : 'Add Expense'">
        <form @submit.prevent="isEditMode ? handleEditExpense($event) : handleAddExpense($event)">

            <!-- Hidden input to store the ID -->
            <input type="hidden" v-if="isEditMode" v-model="form.id">
            <div class="flex items-center justify-center">
                <div class="p-5 bg-white rounded-md">
                    <div class="mb-6 flex items-center space-x-5">
                        <label for="expense-category" class="block text-gray-700">Expense Category</label>
                        <select name="expense-category" v-model="form.expenseCategoryId"
                            class="p-2 bg-gray-100 rounded-md w-full" required>
                            <option v-for="expenseCategory in expenseCategories" :key="expenseCategory.id"
                                :value="expenseCategory.id">
                                {{ expenseCategory.name }}
                            </option>
                        </select>
                    </div>

                    <div class="mb-6 flex items-center space-x-5">
                        <label for="amount" class="block text-gray-700">Amount</label>
                        <input type="number" id="amount" class="p-2 bg-gray-100 rounded-md w-full" step="0.01"
                            v-model="form.amount" required>
                    </div>

                    <div class="mb-6 flex items-center space-x-5">
                        <label for="entry-date" class="block text-gray-700">Entry Date</label>
                        <input type="date" id="entry-date" class="p-2 bg-gray-100 rounded-md w-full"
                            v-model="form.entryDate" required>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex justify-between">
                <div class="text-left">

                    <button v-if="isEditMode" @click.prevent="handleDeleteExpense"
                        class="mr-2 px-4 py-2 text-sm rounded text-white bg-red-400 focus:outline-none hover:bg-red-500">Delete</button>
                </div>
                <div class="text-right">
                    <button @click="toggleModal"
                        class="mr-2 px-4 py-2 text-sm rounded text-white bg-gray-400 focus:outline-none hover:bg-gray-500">Cancel</button>

                    <!-- Administrator cant be updated -->
                    <button type="submit"
                        class="mr-2 px-4 py-2 text-sm rounded text-white bg-indigo-400 focus:outline-none hover:bg-indigo-500">
                        {{ isEditMode ? 'Update' : 'Save' }}</button>
                </div>
            </div>
        </form>
    </Modal>
</template>

<script setup>

import axios from 'axios';
import { ref, onMounted } from 'vue';
import dayjs from 'dayjs';

import SideBar from '../components/SideBar.vue';
import Modal from '../components/Modal.vue';
import Loading from '../components/Loading.vue';
import Toast from '../components/Toast.vue';

const expenses = ref(null)
const expenseCategories = ref(null)
const showModal = ref(false);
const isEditMode = ref(false);
const showToast = ref(false);

const form = ref({
    id: null,
    expenseCategoryId: '',
    amount: '',
    entryDate: '',
})

onMounted(async () => {
    loadeExpenses()
    loadeExpenseCategories() 
})

const loadeExpenses = async () => {
    expenses.value = null
    try {
        const response = await axios.get('/api/expense')
        expenses.value = response.data;
    } catch (error) {
        console.error('Error fetching expense categories:', error);
    }
}

const loadeExpenseCategories = async () => {
    expenseCategories.value = null
    try {
        const response = await axios.get('/api/expense-categories')
        expenseCategories.value = response.data;
    } catch (error) {
        console.error('Error fetching expense categories:', error)
    }
}


const formatDate = (dateString) => {
    return dayjs(dateString).format('YYYY-MM-DD')
};

function toggleModal(editMode = false, expenseData = null) {
    showModal.value = !showModal.value
    isEditMode.value = editMode

    if (editMode && expenseData) {
        // Populate the form with the data of the role being edited
        form.value.id = expenseData.id
        form.value.expenseCategoryId = expenseData.expense_category_id
        form.value.amount = expenseData.amount
        form.value.entryDate = expenseData.entry_date

        console.log("ID ", form.value.id)
    } else {
        // Reset the form when opening the modal for adding a new role
        isEditMode.value = false
        resetForm()
    }
}

function resetForm() {
    form.value.id = null;
    form.value.expenseCategoryId = ''
    form.value.amount = ''
    form.value.entryDate = ''
}

const handleAddExpense = async () => {
    try {
        await axios.post('/api/expense', {
            expenseCategoryId: form.value.expenseCategoryId,
            amount: form.value.amount,
            entryDate: form.value.entryDate,
        })
        loadeExpenses()
    } catch (error) {
        console.error("Failed to add new expense", error.message)
    }
    resetForm()
    toggleModal()
    showSuccessToast()
}

const handleEditExpense = async () => {
    try {
        await axios.put(`/api/expense/${form.value.id}`, {
            name: form.value.name,
            expenseCategoryId: form.value.expenseCategoryId,
            amount: form.value.amount,
            entryDate: form.value.entryDate,
        })
        loadeExpenses()
    } catch (error) {
        console.error("Failed to update expense", error.message)
    }
    resetForm()
    toggleModal()
    showSuccessToast()
}

const handleDeleteExpense = async () => {
    try {
        await axios.delete(`/api/expense/${form.value.id}`)
        loadeExpenses()
    } catch (error) {
        console.error("Failed to delete expense", error.message)
    }
    resetForm();
    toggleModal();
    showSuccessToast()
}

const showSuccessToast = (duration = 3000) => {
    showToast.value = true
    setTimeout(() => {
        showToast.value = false
    }, duration)
}
</script>

<style>
table tbody tr {
    cursor: pointer
}
</style>