<script setup>
import axios from 'axios';
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const user = ref(null)
const router = useRouter()

onMounted(async () => {

    try {
        const response = await axios.get('/api/user');
        user.value = response.data;
    } catch (error) {
        console.log('Error fetching user', error.message);
    }

})

const handleLogout = async () => {
    try {
        const response = await axios.post('/logout');
        router.push('/')

    } catch (error) {
        console.log('Error logging out', error.message);
    }
}
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-8">
        <!-- Sidebar -->
        <div class="flex flex-col h-screen p-3 duration-300 bg-gray-800 shadow">
            <div class="space-y-3">
                <div class="flex-1 px-5">
                    <ul class="pt-2 pb-4 space-y-1 text-sm mt-10">
                        <li class="rounded-sm">
                            <div class="flex items-center p-2 space-x-3 rounded-md ">
                                <div v-if="user?.role">
                                    <h2 class="text-gray-100">{{ user?.name }} ({{ user?.role.name }})</h2>
                                </div>
                                <div v-else>
                                    <h2 class="text-gray-100">Loading User...</h2>
                                </div>
                            </div>
                        </li>

                        <li class="rounded-sm hover:bg-gray-700">
                            <router-link :to="{ name: 'Dashboard' }" class="flex items-center p-2 space-x-3 rounded-md">
                                <span class="text-gray-100 font-bold">Dashboard</span>
                        
                            </router-link>
                      
                        </li>
                        <li class="rounded-sm" v-if="user?.role_id == 1">
                            <span class="p-2 space-x-3 rounded-md font-bold text-gray-100">User Management</span>
      
                            <ul class="ml-5 space-y-2 py-1">
                                <li class="p-1 space-x-3 rounded-md hover:bg-gray-700">
                                    <router-link :to="{ name: 'Users' }" class="text-gray-100">
                                        Users
                                    </router-link>
                                </li>
                                <li class="p-1 space-x-3 rounded-md hover:bg-gray-700">
                                    <router-link :to="{ name: 'Roles' }" class="text-gray-100">
                                        Role
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                        <li class="rounded-sm">
                            <span class=" p-1 space-x-3 font-bold rounded-md text-gray-100"> Expense Management </span>
                  
                            <ul class="ml-5 space-y-2 py-1">
                                <li v-if="user?.role_id === 1 || user?.role.name === 'Administrator'" class="p-1 space-x-3 rounded-md hover:bg-gray-700">
                                    <router-link :to="{ name: 'ExpenseCategories' }" class="text-gray-100">
                                        Expense Categories
                                    </router-link>
                                </li>
                                <li class="p-1 space-x-3 rounded-md hover:bg-gray-700">
                                    <router-link :to="{ name: 'Expenses' }" class="text-gray-100">
                                        Expense
                                    </router-link>
                                </li>
                            </ul>

                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-span-7">
            <!-- Navbar -->
            <div class="">
                <div class="inset-x-0 top-0">
                    <div class="md:col-span-2 shadow">
                        <div class="container p-5">
                            <div class="flex justify-end space-x-5 pr-10">
                                <h5 class=" font-bold mr-5">Welcome to Expense Tracker</h5>
                                <div class="flex items-center justify-end space-x-4">
                                    <h5 class=" font-bold"> <a href="#logout" class="text-gray-700"
                                            @click="handleLogout">Logout</a></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="container mx-auto mt-12 p-5">
                <div class="flex-1">
                    <div class="container mx-auto mt-12 shadow p-5">
                        <slot></slot>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

  
  