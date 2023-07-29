<template>
    <SideBar>
        <div class="flex flex-col">
            <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                    <div class="overflow-hidden">
                        <div class="flex justify-between mb-3">
                            <h3 class="text-xl">Users</h3>
                            <h4 class="text-xl">User Management > Users</h4>
                        </div>
                        <div class="px-5">
                            <table v-if="users && users.length" class="min-w-full text-left text-sm font-light">
                                <thead class="border-b font-medium dark:border-neutral-500">
                                    <tr>
                                        <th scope="col" class="px-6 py-4">Name</th>
                                        <th scope="col" class="px-6 py-4">Email Address</th>
                                        <th scope="col" class="px-6 py-4">Role</th>
                                        <th scope="col" class="px-6 py-4">Created at</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user in users" :key="user.id" @click="toggleModal(editMode = true, user)"
                                        class="border-b dark:border-neutral-500 hover:bg-gray-200">
                                        <td class="whitespace-nowrap px-6 py-4 font-medium"> {{ user?.name }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ user?.email }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ user?.role == null? 'No Role' : user?.role.name }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">{{ formatDate(user?.created_at) }}</td>
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
                                Add User
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

    <Modal v-if="showModal" :title="isEditMode ? 'Update User' : 'Add User'">
        <form @submit.prevent="isEditMode ? handleEditUser($event) : handleAddUser($event)">

            <!-- Hidden input to store the ID -->
            <input type="hidden" v-if="isEditMode" v-model="form.id">
            <div class="flex items-center justify-center">
                <div class="p-5 bg-white rounded-md">
                    <div class="mb-6 flex items-center space-x-5">
                        <label for="name" class="block text-gray-700">Name</label>
                        <input type="text" id="name" class="p-2 bg-gray-100 rounded-md w-full" v-model="form.name"
                            required>
                    </div>

                    <div class="mb-6 flex items-center space-x-5">
                        <label for="email" class="block text-gray-700">Email</label>
                        <input type="email" id="email" class="p-2 bg-gray-100 rounded-md w-full" v-model="form.email"
                            required>
                    </div>

                    <div class="mb-6 flex items-center space-x-5">
                        <label for="role" class="block text-gray-700">Role</label>
                        <select id="role" v-model="form.role_id" class="p-2 bg-gray-100 rounded-md w-full" required>
                            <option v-for="role in roles" :key="role.id" :value="role.id" :selected="role.id == 1">{{ role.name }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex justify-between">
                <div class="text-left">

                    <!-- only deletes if in edit mode and the role is not Administrator -->
                    <button v-if="isEditMode && !(form.id == '1' || form.name == 'Administrator')"
                        @click.prevent="handleDeleteUser"
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

const users = ref(null)
const roles = ref(null)
const showModal = ref(false);
const isEditMode = ref(false);
const showToast = ref(false);

const form = ref({
    id: null,
    name: '',
    email: '',
    role_id: '',
})

onMounted(async () => {
    loadUsers()
    loadRoles()
})

const loadUsers = async () => {
    users.value = null
    try {
        const response = await axios.get('/api/users');
        users.value = response.data;
    } catch (error) {
        console.error('Error fetching users:', error);
    }
}

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

function toggleModal(editMode = false, userData = null) {
    showModal.value = !showModal.value;
    isEditMode.value = editMode


    if (editMode && userData) {
        // Populate the form with the data of the role being edited
        form.value.id = userData.id;
        form.value.name = userData.name;
        form.value.email = userData.email;
        form.value.role_id = userData.role_id

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
    form.value.email = '';
    form.value.role_id = '';
}

const handleAddUser = async () => {
    try {
        await axios.post('/api/users', {
            name: form.value.name,
            email: form.value.email,
            role_id: form.value.role_id,
            password: 'password',
        })
        loadUsers()
    } catch (error) {
        console.error("Failed to add new Role", error.message);
    }
    resetForm();
    toggleModal();
    showSuccessToast()
}

const handleEditUser = async () => {
    try {
        await axios.put(`/api/users/${form.value.id}`, {
            name: form.value.name,
            email: form.value.email,
            role_id: form.value.role_id,
        })
        loadUsers()
    } catch (error) {
        console.error("Failed to add new Role", error.message);
    }
    resetForm();
    toggleModal();
    showSuccessToast()
}

const handleDeleteUser = async () => {
    try {
        await axios.delete(`/api/users/${form.value.id}`)
        loadUsers()
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