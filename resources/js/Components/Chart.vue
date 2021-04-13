<template>
    <canvas width="600" height="300"></canvas>
</template>

<script>
    import Chart from 'chart.js';

    export default {
        props: {
            labels: {
                type: Array,
                default: () => [],
            },
            data: {
                type: Array,
                default: () => [],
            },
        },

        mounted() {
            new Chart(this.$el.getContext('2d'), {
                data: {
                    labels: this.labels,
                    datasets: this.datasets,
                },
                type: 'line',
                options: this.options,
            });
        },

        computed: {
            options() {
                return {
                    tooltips: {
                        backgroundColor: '#fff',
                        displayColors: false,
                        titleFontColor: '#000',
                        bodyFontColor: '#000',
                    },
                    legend: {
                        display: false,
                    },
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display:false,
                            },
                        }],
                        yAxes: [{
                            ticks: {
                                stepSize: 1,
                            },
                        }],
                    },
                };
            },
            datasets() {
                return [{
                    data: this.data,
                    backgroundColor: 'rgba(19, 130, 249, 0.07)',
                    borderColor: '#1382f9',
                    borderWidth: 3,
                    pointRadius: 0,
                    pointBackgroundColor: '#1382f9',
                    pointBorderColor: '#1382f9',
                    pointHoverRadius: 4,
                    pointHoverBackgroundColor: 'rgba(0, 97, 242, 1)',
                    pointHoverBorderColor: 'rgba(0, 97, 242, 1)',
                    pointBorderWidth: 3,
                    pointHitRadius: 16,
                }];
            },
        },
    }
</script>
