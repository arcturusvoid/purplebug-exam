<template>
    <SideBar>
        <div class="flex flex-col">
            <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                    <div class="overflow-hidden">
                        <div class="flex justify-between mb-3">
                            <h3 class="text-xl">Expense Categories</h3>
                            <h4 class="text-xl">Expense Management > Expense Categories</h4>
                        </div>
                        <div class="px-5">
                            <table v-if="expenseCategories && expenseCategories.length"
                                class="min-w-full text-left text-sm font-light">
                                <thead class="border-b font-medium dark:border-neutral-500">
                                    <tr>
                                        <th scope="col" class="px-6 py-4">Display Name</th>
                                        <th scope="col" class="px-6 py-4">Description</th>
                                        <th scope="col" class="px-6 py-4">Created at</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="expenseCategory in expenseCategories" :key="expenseCategory.id"
                                        @click="toggleModal(editMode = true, expenseCategory)"
                                        class="border-b dark:border-neutral-500 hover:bg-gray-200">
                                        <td class="whitespace-nowrap px-6 py-4 font-medium"> {{ expenseCategory?.name }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ expenseCategory?.description }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ formatDate(expenseCategory?.created_at)
                                        }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <div v-else class="flex justify-center">
                                <Loading />
                            </div>

                        </div>


                        <div class="mt-12 flex justify-end">
                            <button @click="toggleModal"
                                class="bg-gray-500 hover:bg-gray-600 text-white py-1 px-4 border border-gray-800 rounded">
                                Add Category
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

    <Modal v-if="showModal" :title="isEditMode ? 'Update Category' : 'Add Category'">
        <form @submit.prevent="isEditMode ? handleEditExpenseCategory($event) : handleAddExpenseCategory($event)">

            <!-- Hidden input to store the ID -->
            <input type="hidden" v-if="isEditMode" v-model="form.id">
            <div class="flex items-center justify-center">
                <div class="p-5 bg-white rounded-md">
                    <div class="mb-6 flex items-center space-x-5">
                        <label for="name" class="block text-gray-700">Display Name</label>
                        <input type="text" id="name" class="p-2 bg-gray-100 rounded-md w-full" v-model="form.name" required>
                    </div>

                    <div class="mb-6 flex items-center space-x-5">
                        <label for="description" class="block text-gray-700">Description</label>
                        <input type="text" id="description" class="p-2 bg-gray-100 rounded-md w-full"
                            v-model="form.description" required>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex justify-between">
                <div class="text-left">

                    <button v-if="isEditMode" @click.prevent="handleDeleteExpenseCategory"
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
import checkUserCredentials from '../composables/checkUserCredentials'
checkUserCredentials()

import axios from 'axios'
import { ref, onMounted } from 'vue'
import dayjs from 'dayjs'

import SideBar from '../components/SideBar.vue'
import Modal from '../components/Modal.vue'
import Loading from '../components/Loading.vue'
import Toast from '../components/Toast.vue'

const expenseCategories = ref(null)
const showModal = ref(false)
const isEditMode = ref(false)
const showToast = ref(false)

const form = ref({
    id: null,
    name: '',
    description: '',
})

onMounted(async () => {
    loadExpenseCategories()
})

const loadExpenseCategories = async () => {
    expenseCategories.value = null
    try {
        const response = await axios.get('/api/expense-categories')
        expenseCategories.value = response.data
    } catch (error) {
        console.error('Error fetching expense categories:', error)
    }
}

const formatDate = (dateString) => {
    return dayjs(dateString).format('YYYY-MM-DD');
};

function toggleModal(editMode = false, expenseCategoryData = null) {
    showModal.value = !showModal.value;
    isEditMode.value = editMode

    if (editMode && expenseCategoryData) {
        // Populate the form with the data of the role being edited
        form.value.id = expenseCategoryData.id;
        form.value.name = expenseCategoryData.name;
        form.value.description = expenseCategoryData.description;

        console.log("ID ", form.value.id);
    } else {
        // Reset the form when opening the modal for adding a new role
        isEditMode.value = false
        resetForm();
    }
}

function resetForm() {
    form.value.id = null;
    form.value.name = '';
    form.value.description = '';
}

const handleAddExpenseCategory = async () => {
    try {
        await axios.post('/api/expense-categories', {
            name: form.value.name,
            description: form.value.description,
        })
        loadExpenseCategories()
    } catch (error) {
        console.error("Failed to add new expense category", error.message);
    }
    resetForm();
    toggleModal();
    showSuccessToast()
}

const handleEditExpenseCategory = async () => {
    try {
        await axios.put(`/api/expense-categories/${form.value.id}`, {
            name: form.value.name,
            description: form.value.description,
        })
        loadExpenseCategories()
    } catch (error) {
        console.error("Failed to update expense category", error.message);
    }
    resetForm();
    toggleModal();
    showSuccessToast()
}

const handleDeleteExpenseCategory = async () => {
    try {
        await axios.delete(`/api/expense-categories/${form.value.id}`)
        loadExpenseCategories()
    } catch (error) {
        console.error("Failed to delete expense category", error.message);
    }
    resetForm();
    toggleModal();
    showSuccessToast()
}

const showSuccessToast = (duration = 3000) => {
    showToast.value = true;
    setTimeout(() => {
        showToast.value = false;
    }, duration);
}
</script>

<style>
table tbody tr {
    cursor: pointer;
}
</style>