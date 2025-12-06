@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-start">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-chart-area text-purple-600 mr-3"></i>Budget Forecast Report
                </h1>
                <p class="text-gray-600">Financial projections and forecasts for strategic planning</p>
            </div>
            <a href="{{ route('analytics.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>

        <!-- Forecast Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-indigo-500">
                <h3 class="text-gray-500 text-sm font-semibold uppercase mb-2">Current Month Forecast</h3>
                <p class="text-3xl font-bold text-gray-900" id="currentMonthForecast">$0</p>
                <p class="text-indigo-600 text-sm mt-2"><i class="fas fa-calendar-alt mr-1"></i>Projected Spending</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                <h3 class="text-gray-500 text-sm font-semibold uppercase mb-2">Q2 Total Forecast</h3>
                <p class="text-3xl font-bold text-gray-900" id="q2Forecast">$0</p>
                <p class="text-purple-600 text-sm mt-2"><i class="fas fa-chart-line mr-1"></i>3-Month Projection</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-pink-500">
                <h3 class="text-gray-500 text-sm font-semibold uppercase mb-2">Forecast Confidence</h3>
                <p class="text-3xl font-bold text-gray-900">95%</p>
                <p class="text-pink-600 text-sm mt-2"><i class="fas fa-check-circle mr-1"></i>Based on Historical Data</p>
            </div>
        </div>

        <!-- Main Forecast Chart -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                <i class="fas fa-chart-line text-indigo-600 mr-2"></i>Historical Data & 3-Month Forecast
            </h2>
            <div style="position: relative; height: 400px;">
                <canvas id="mainForecastChart"></canvas>
            </div>
        </div>

        <!-- Forecast by Scenario -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Conservative Scenario -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">
                    <i class="fas fa-shield-alt text-green-600 mr-2"></i>Conservative Scenario
                </h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-gray-600 text-sm">Assumption: 10% lower spending</p>
                        <p class="text-2xl font-bold text-green-600">-10%</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">3-Month Projected Total</p>
                        <p class="text-2xl font-bold text-gray-900" id="conservativeTotal">$0</p>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded p-3">
                        <p class="text-green-800 text-sm">
                            <i class="fas fa-check-circle mr-1"></i>
                            Best case scenario - budget comfortable, good reserves
                        </p>
                    </div>
                </div>
            </div>

            <!-- Aggressive Scenario -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>Aggressive Scenario
                </h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-gray-600 text-sm">Assumption: 10% higher spending</p>
                        <p class="text-2xl font-bold text-red-600">+10%</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">3-Month Projected Total</p>
                        <p class="text-2xl font-bold text-gray-900" id="aggressiveTotal">$0</p>
                    </div>
                    <div class="bg-red-50 border border-red-200 rounded p-3">
                        <p class="text-red-800 text-sm">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            Worst case scenario - may require budget review
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Forecast by Department -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                <i class="fas fa-sitemap text-blue-600 mr-2"></i>Department-wise Forecast
            </h2>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase">Historical Avg</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase">Conservative</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase">Base Case</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase">Aggressive</th>
                        </tr>
                    </thead>
                    <tbody id="deptForecastBody" class="divide-y divide-gray-200">
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recommendations -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-blue-50 border-l-4 border-blue-600 rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-blue-900 mb-3">
                    <i class="fas fa-lightbulb text-blue-600 mr-2"></i>Key Recommendations
                </h3>
                <ul class="space-y-2 text-blue-800 text-sm">
                    <li><i class="fas fa-check-circle mr-2 text-blue-600"></i>Monitor spending patterns monthly</li>
                    <li><i class="fas fa-check-circle mr-2 text-blue-600"></i>Review high-spending departments quarterly</li>
                    <li><i class="fas fa-check-circle mr-2 text-blue-600"></i>Adjust allocations based on actual trends</li>
                    <li><i class="fas fa-check-circle mr-2 text-blue-600"></i>Prepare contingency plans for aggressive scenario</li>
                </ul>
            </div>

            <div class="bg-orange-50 border-l-4 border-orange-600 rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-orange-900 mb-3">
                    <i class="fas fa-exclamation text-orange-600 mr-2"></i>Risk Factors
                </h3>
                <ul class="space-y-2 text-orange-800 text-sm">
                    <li><i class="fas fa-alert-circle mr-2 text-orange-600"></i>Forecast accuracy ±15% due to seasonal variations</li>
                    <li><i class="fas fa-alert-circle mr-2 text-orange-600"></i>Unexpected expenditures may impact accuracy</li>
                    <li><i class="fas fa-alert-circle mr-2 text-orange-600"></i>Delayed approvals could skew timing</li>
                    <li><i class="fas fa-alert-circle mr-2 text-orange-600"></i>Department budget changes not yet reflected</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    let charts = {};

    function formatCurrency(value) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value);
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadMainForecastChart();
        loadDeptForecast();
    });

    function loadMainForecastChart() {
        axios.get('/api/analytics/forecast')
            .then(response => {
                const data = response.data;

                if (charts.mainForecast) charts.mainForecast.destroy();

                const ctx = document.getElementById('mainForecastChart').getContext('2d');
                charts.mainForecast = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Historical Data ($)',
                                data: data.historical,
                                borderColor: '#06b6d4',
                                backgroundColor: 'rgba(6, 182, 212, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 3,
                                pointRadius: 5,
                                pointBackgroundColor: '#06b6d4',
                            },
                            {
                                label: 'Forecast ($)',
                                data: data.forecast,
                                borderColor: '#8b5cf6',
                                borderDash: [5, 5],
                                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 3,
                                pointRadius: 5,
                                pointBackgroundColor: '#8b5cf6',
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    font: { size: 13, weight: 'bold' }
                                }
                            },
                            annotation: {}
                        },
                        scales: {
                            y: {
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });

                // Calculate totals
                const forecastValues = data.forecast.filter(v => v !== null);
                if (forecastValues.length > 0) {
                    const total = forecastValues.reduce((a, b) => a + b, 0);
                    document.getElementById('currentMonthForecast').textContent = formatCurrency(forecastValues[0]);
                    document.getElementById('q2Forecast').textContent = formatCurrency(total);
                    document.getElementById('conservativeTotal').textContent = formatCurrency(total * 0.9);
                    document.getElementById('aggressiveTotal').textContent = formatCurrency(total * 1.1);
                }
            })
            .catch(error => console.error('Error loading main forecast chart:', error));
    }

    function loadDeptForecast() {
        axios.get('/api/analytics/budget-by-department')
            .then(response => {
                const depts = response.data.data;
                const tableBody = document.getElementById('deptForecastBody');
                tableBody.innerHTML = '';

                if (depts.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No data available</td></tr>';
                } else {
                    depts.forEach(dept => {
                        const avg = dept.budget / 12; // Monthly average
                        const conservative = avg * 0.9;
                        const baseCase = avg;
                        const aggressive = avg * 1.1;

                        const row = `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">${dept.name}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${formatCurrency(avg)}</td>
                                <td class="px-6 py-4 text-sm text-green-600 font-semibold">${formatCurrency(conservative)}</td>
                                <td class="px-6 py-4 text-sm text-blue-600 font-semibold">${formatCurrency(baseCase)}</td>
                                <td class="px-6 py-4 text-sm text-red-600 font-semibold">${formatCurrency(aggressive)}</td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });
                }
            })
            .catch(error => console.error('Error loading department forecast:', error));
    }
</script>
@endsection
