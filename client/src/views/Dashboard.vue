<template>
  <SideBar>
    <div class="flex justify-between">
      <h3 class="text-xl">My Expenses</h3>
      <h4 class="text-xl">Dashboard</h4>
    </div>

    <div class="flex flex-col">
      <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
          <div class="overflow-hidden grid grid-cols-2">
            <div class="">
              <div class="grid grid-cols-2 text-center">
                <div>
                  <h3 class="text-xl text-right">Expense Categories</h3>
                </div>

                <div>
                  <h3 class="text-xl text-right">Total</h3>
                </div>
              </div>

              <div class="grid grid-cols-2 text-center" v-for="summary in expensesSummary" :key="summary.id"
                v-show="summary?.expenses_sum > 0">
                <div>
                  <h3 class="text-lg text-right text-gray-500">{{ summary?.name }}</h3>
                </div>

                <div>
                  <h3 class="text-lg text-right text-gray-500">${{ summary?.expenses_sum }}</h3>
                </div>
              </div>

            </div>

            <div class="chart-div max-w-2xl">
              <Pie v-if="isLoaded" :data="data" :options="options" />
            </div>

          </div>
        </div>
      </div>
    </div>

  </SideBar>
</template>

<script setup>
import { Chart as ChartJS, ArcElement, Tooltip, Colors } from 'chart.js'
import { Pie } from 'vue-chartjs'

import SideBar from '../components/SideBar.vue'
import axios from 'axios'
import { ref, onMounted } from 'vue'


ChartJS.register(ArcElement, Tooltip, Colors)
const expensesSummary = ref(null)
const isLoaded = ref(false)

onMounted(async () => {
  const response = await axios.get('/api/dashboard')
  expensesSummary.value = response.data

  data.labels = response.data.map(expense => expense.name)
  data.datasets[0].data = response.data.map(expense => expense.expenses_sum)
  isLoaded.value = true
})

let data = {
  labels: [],
  datasets: [
    {
      data: []
    }
  ]
}

let options = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    Color: {
      mode: 'data'
    }
  }
}

</script>

<style></style>