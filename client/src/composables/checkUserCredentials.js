import axios from 'axios'
import { ref } from 'vue'
import router from '../router'

const userCredentials = ref(null)
const checkUserCredentials = async () => {
    try {
        const response = await axios.get('/api/user');
        userCredentials.value = response.data;

        if (!(userCredentials.value.id === 1 || userCredentials.value.id === 'Administrator'))
            router.push('/dashboard')

    } catch (error) {
        console.error('Error fetching expense categories:', error);
    }
}

export default checkUserCredentials