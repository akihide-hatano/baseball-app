document.addEventListener('DOMContentLoaded', function () {
    // 既存の打撃能力チャート (レーダーチャート)
    const battingAbilityChartCanvas = document.getElementById('battingAbilityChart');
    if (battingAbilityChartCanvas) {
        const battingAbilitiesData = JSON.parse(battingAbilityChartCanvas.dataset.battingAbilities);
        new Chart(battingAbilityChartCanvas, {
            type: 'radar',
            data: {
                labels: battingAbilitiesData.labels,
                datasets: [{
                    label: '能力値',
                    data: battingAbilitiesData.data,
                    backgroundColor: 'rgba(75, 192, 192, 0.4)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                    pointBorderColor: 'rgba(75, 192, 192, 1)',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(75, 192, 192, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: {
                            display: false
                        },
                        suggestedMin: 0,
                        suggestedMax: 100, // 能力値の最大値を適切に設定
                        ticks: {
                            beginAtZero: true,
                            stepSize: 20,
                            color: '#4a5568'
                        },
                        grid: {
                            color: 'rgba(150, 150, 150, 0.7)',
                            lineWidth: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: '打撃能力',
                        font: {
                            size: 18
                        }
                    }
                }
            }
        });
    }

    // 新しい総合ランクチャート (棒グラフ)
    const overallRankChartCanvas = document.getElementById('overallRankChart');
    if (overallRankChartCanvas) {
        const overallRankData = JSON.parse(overallRankChartCanvas.dataset.overallRank);
        new Chart(overallRankChartCanvas, {
            type: 'bar',
            data: {
                labels: overallRankData.labels,
                datasets: [{
                    label: '総合ランク',
                    data: overallRankData.data,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 159, 64, 0.8)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 20
                        },
                        grid: {
                            color: 'rgba(150, 150, 150, 0.7)',
                            lineWidth: 1
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(150, 150, 150, 0.7)',
                            lineWidth: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: '総合能力ランク',
                        font: {
                            size: 18
                        }
                    }
                }
            }
        });
    }

    // 既存の投球能力チャート (レーダーチャート)
    const pitchingAbilityChartCanvas = document.getElementById('pitchingAbilityChart');
    if (pitchingAbilityChartCanvas) {
        const pitchingAbilitiesData = JSON.parse(pitchingAbilityChartCanvas.dataset.pitchingAbilities);
        new Chart(pitchingAbilityChartCanvas, {
            type: 'radar',
            data: {
                labels: pitchingAbilitiesData.labels, // そのままラベルを使用
                datasets: [{
                    label: '変化球レベル',
                    data: pitchingAbilitiesData.data, // ★ここを修正：.data を1つ削除★
                    backgroundColor: 'rgba(153, 102, 255, 0.4)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(153, 102, 255, 1)',
                    pointBorderColor: 'rgba(153, 102, 255, 1)',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(153, 102, 255, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: {
                            display: false
                        },
                        suggestedMin: 0,
                        suggestedMax: 7, // 変化球レベルの最大値を適切に設定
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1,
                            color: '#4a5568'
                        },
                        grid: {
                            color: 'rgba(150, 150, 150, 0.7)',
                            lineWidth: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: '選手変化球能力',
                        font: {
                            size: 18
                        }
                    }
                }
            }
        });
    }
});