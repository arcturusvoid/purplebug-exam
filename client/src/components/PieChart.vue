<template>
    <Pie :data="chartData" />
</template>
  
<script>


export default {
    name: 'BarChart',
    components: { Pie },
    data() {
        return {
            chartData: {
                labels: ['January', 'February', 'March'],
                datasets: [
                    {
                        label: 'Data One',
                        backgroundColor: '#f87979',
                        data: [40, 20, 12]
                    }
                ]
            }
        }
    }
}
</script>
  