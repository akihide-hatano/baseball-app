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

    // 既存の総合ランクチャート (棒グラフ)
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
                        text: '野手総合能力ランク',
                        font: {
                            size: 18
                        }
                    }
                }
            }
        });
    }

    // 投手能力チャート (変化球) - レーダーチャート
    const pitchingAbilityChartCanvas = document.getElementById('pitchingAbilityChart');
    if (pitchingAbilityChartCanvas) {
        const pitchingAbilitiesData = JSON.parse(pitchingAbilityChartCanvas.dataset.pitchingAbilities);
        
        const maxPitchLevel = Math.max(...pitchingAbilitiesData.data, 7);
        const suggestedMaxPitchingAbility = Math.ceil(maxPitchLevel / 1) * 1;
        const finalMaxPitchingAbility = Math.max(suggestedMaxPitchingAbility, 8);

        new Chart(pitchingAbilityChartCanvas, {
            type: 'radar', // ★レーダーチャートのまま★
            data: {
                labels: pitchingAbilitiesData.labels,
                datasets: [{
                    label: '変化球レベル',
                    data: pitchingAbilitiesData.data,
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
                        suggestedMax: finalMaxPitchingAbility,
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
                        text: '投手変化球能力',
                        font: {
                            size: 18
                        }
                    }
                }
            }
        });
    }

    // 投手基本能力チャート (スタミナ, コントロール) - ★棒グラフに変更済み★
    const pitchingFundamentalAbilityChartCanvas = document.getElementById('pitchingFundamentalAbilityChart');
    if (pitchingFundamentalAbilityChartCanvas) {
        const pitchingFundamentalAbilitiesData = JSON.parse(pitchingFundamentalAbilityChartCanvas.dataset.pitchingFundamentalAbilities);

        const maxFundamentalValue = Math.max(...pitchingFundamentalAbilitiesData.data);
        const suggestedMaxFundamental = Math.ceil(maxFundamentalValue / 20) * 20;
        const finalMaxFundamental = Math.max(suggestedMaxFundamental, 100);

        new Chart(pitchingFundamentalAbilityChartCanvas, {
            type: 'bar', // ← ここが 'bar' になっています！
            data: {
                labels: pitchingFundamentalAbilitiesData.labels,
                datasets: [{
                    label: '基本能力値',
                    data: pitchingFundamentalAbilitiesData.data,
                    backgroundColor: 'rgba(255, 99, 132, 0.8)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // 横棒グラフ
                scales: {
                    x: {
                        beginAtZero: true,
                        max: finalMaxFundamental,
                        ticks: {
                            stepSize: 20
                        },
                        grid: {
                            color: 'rgba(150, 150, 150, 0.7)',
                            lineWidth: 1
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: '投手基本能力 (スタミナ, コントロール)',
                        font: {
                            size: 18
                        }
                    }
                }
            }
        });
    }

    // 新しい球速チャート (棒グラフ)
    const pitchingVelocityChartCanvas = document.getElementById('pitchingVelocityChart');
    if (pitchingVelocityChartCanvas) {
        const pitchingVelocityData = JSON.parse(pitchingVelocityChartCanvas.dataset.pitchingVelocity);

        const maxVelocity = Math.max(...pitchingVelocityData.data);
        const suggestedMaxVelocity = Math.ceil(maxVelocity / 20) * 20;
        const finalMaxVelocity = Math.max(suggestedMaxVelocity, 160);

        new Chart(pitchingVelocityChartCanvas, {
            type: 'bar',
            data: {
                labels: pitchingVelocityData.labels,
                datasets: [{
                    label: '球速 (km/h)',
                    data: pitchingVelocityData.data,
                    backgroundColor: 'rgba(0, 123, 255, 0.8)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        max: finalMaxVelocity,
                        ticks: {
                            stepSize: 20
                        },
                        title: {
                            display: true,
                            text: 'km/h',
                            color: '#4a5568'
                        },
                        grid: {
                            color: 'rgba(150, 150, 150, 0.7)',
                            lineWidth: 1
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: '球速',
                        font: {
                            size: 18
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.x !== null) {
                                    label += context.parsed.x + ' km/h';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    // 投手総合ランクチャート (棒グラフ)
    const pitchingOverallRankChartCanvas = document.getElementById('pitchingOverallRankChart');
    if (pitchingOverallRankChartCanvas) {
        const pitchingOverallRankData = JSON.parse(pitchingOverallRankChartCanvas.dataset.pitchingOverallRank);
        new Chart(pitchingOverallRankChartCanvas, {
            type: 'bar',
            data: {
                labels: pitchingOverallRankData.labels,
                datasets: [{
                    label: '投手総合ランク',
                    data: pitchingOverallRankData.data,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)'
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
                        text: '投手総合能力ランク',
                        font: {
                            size: 18
                        }
                    }
                }
            }
        });
    }
});