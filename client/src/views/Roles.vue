<template>
    <SideBar>
        <div class="flex flex-col">
            <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                    <div class="overflow-hidden">
                        <div class="flex justify-between mb-3">
                            <h3 class="text-xl">Roles</h3>
                            <h4 class="text-xl">User Management > Roles</h4>
                        </div>
                        <div class="px-5">
                            <table v-if="roles && roles.length" class="min-w-full text-left text-sm font-light">
                                <thead class="border-b font-medium dark:border-neutral-500">
                                    <tr>
                                        <th scope="col" class="px-6 py-4">Display Name</th>
                                        <th scope="col" class="px-6 py-4">Description</th>
                                        <th scope="col" class="px-6 py-4">Created at</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="role in roles" :key="role.id" @click="toggleModal(editMode = true, role)"
                                        class="border-b dark:border-neutral-500 hover:bg-gray-200">
                                        <td class="whitespace-nowrap px-6 py-4 font-medium"> {{ role?.name }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ role?.description }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ formatDate(role?.created_at) }}</td>
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
                                Add Role
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

    <Modal v-if="showModal" :title="isEditMode ? 'Update Role' : 'Add Role'">
        <form @submit.prevent="isEditMode ? handleEditRole($event) : handleAddRole($event)">

            <!-- Hidden input to store the ID -->
            <input type="hidden" v-if="isEditMode" v-model="form.id">
            <div class="flex items-center justify-center">
                <div class="p-5 bg-white rounded-md">
                    <div class="mb-6 flex items-center space-x-5">
                        <label for="display-name" class="block text-gray-700">Display Name</label>
                        <input type="text" id="display-name" class="p-2 bg-gray-100 rounded-md w-full" v-model="form.name"
                            required>
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

                    <!-- only deletes if in edit mode and the role is not Administrator -->
                    <button v-if="isEditMode && !(form.id == '1' || form.name == 'Administrator')"
                        @click.prevent="handleDeleteRole"
                        class="mr-2 px-4 py-2 text-sm rounded text-white bg-red-400 focus:outline-none hover:bg-red-500">Delete</button>
                </div>
                <div class="text-right">
                    <button @click="toggleModal"
                        class="mr-2 px-4 py-2 text-sm rounded text-white bg-gray-400 focus:outline-none hover:bg-gray-500">Cancel</button>

                         <!-- Administrator cant be updated -->
                    <button v-if="!(form.id == '1' || form.name == 'Administrator')" type="submit"
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

import axios from 'axios';
import { ref, onMounted } from 'vue';
import dayjs from 'dayjs';

import SideBar from '../components/SideBar.vue';
import Modal from '../components/Modal.vue';
import Loading from '../components/Loading.vue';
import Toast from '../components/Toast.vue';

const roles = ref(null)
const showModal = ref(false);
const isEditMode = ref(false);
const showToast = ref(false);

const form = ref({
    id: null,
    name: '',
    description: ''
})

onMounted(async () => {
    loadRoles()
})

const loadRoles = async () => {
    roles.value = null
    try {
        const response = await axios.get('/api/roles');
        roles.value = response.data;
    } catch (error) {
        console.error('Error fetching roles:', error);
    }
}

const formatDate = (dateString) => {
    return dayjs(dateString).format('YYYY-MM-DD');
};

function toggleModal(editMode = false, roleData = null) {
    showModal.value = !showModal.value;
    isEditMode.value = editMode


    if (editMode && roleData) {
        // Populate the form with the data of the role being edited
        form.value.id = roleData.id;
        form.value.name = roleData.name;
        form.value.description = roleData.description;

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

const handleAddRole = async () => {
    try {
        await axios.post('/api/roles', {
            name: form.value.name,
            description: form.value.description,
        })
        loadRoles()
    } catch (error) {
        console.error("Failed to add new Role", error.message);
    }
    resetForm();
    toggleModal();
    showSuccessToast()
}

const handleEditRole = async () => {
    try {
        await axios.put(`/api/roles/${form.value.id}`, {
            name: form.value.name,
            description: form.value.description,
        })

        loadRoles()

    } catch (error) {
        console.error("Failed to add new Role", error.message);
    }
    resetForm();
    toggleModal();
    showSuccessToast()
}

const handleDeleteRole = async () => {
    try {
        await axios.delete(`/api/roles/${form.value.id}`)
        loadRoles()

    } catch (error) {
        console.error("Failed to delete the Role", error.message);
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