<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios';
import { useRouter } from 'vue-router';
const router = useRouter();


const form = ref({
    email: '',
    password: '',
});
const errors = ref([])
const showPassword = ref(false)

// check if the user is logged in, if yes then redirect to dashboard
onMounted(async () => {
    try {
        const response = await axios.get('/api/user')
        router.push('/dashboard');
    } catch (error) {
        if (error.response && error.response.status === 401) {
            console.log('User is not logged in.')
        } else {
            console.error('Fetching the account error:', error.message)
        }
    }
})

const handleLogin = async () => {
    try {

        // If no errors, submit form
        await axios.post('/login', {
            email: form.value.email,
            password: form.value.password
        });

        // Redirect to the dashboard on successful login
        router.push('/dashboard');
    } catch (error) {

        // Handle login error, if any
        errors.value = 'Login Failed'
        console.error('Login failed:', error.message)
    }
};

</script>

<template>
    <div class="flex justify-center items-center h-screen">
        <div class="bg-gray-100 p-6 rounded-md shadow-md w-full max-w-md">
            <h3 class="text-2xl font-bold mb-4">Log in</h3>
            <form @submit.prevent="handleLogin" class="space-y-4">
                <div class="space-y-1">
                    <label for="email" class="block text-sm mb-1">Email</label>
                    <input type="email" id="email" v-model="form.email" :class="{ 'border-red-500': errors.email }"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <div v-if="errors.email" class="text-red-500 text-sm">{{ errors.email }}</div>
                </div>
                <div class="space-y-1">
                    <label for="password" class="block text-sm mb-1">Password</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" id="password" v-model="form.password"
                            :class="{ 'border-red-500': errors.password }"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <button type="button"
                            class="text-blue-500 text-sm absolute top-1/2 right-2 transform -translate-y-1/2"
                            @click="showPassword = !showPassword">{{ showPassword ? 'Hide ' : 'Show ' }}</button>
                    </div>
                    <div v-if="errors.length" class=" ml-2 mt-2 text-red-500 text-sm">{{ errors }}</div>

                </div>
                <button type="submit"
                    class="w-full px-4 py-2 text-white bg-blue-500 rounded-full hover:bg-blue-600 transition-all mb-2">Login</button>
            </form>
        </div>
    </div>
</template>