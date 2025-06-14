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

    // 投手能力チャート (変化球)
    const pitchingAbilityChartCanvas = document.getElementById('pitchingAbilityChart');
    if (pitchingAbilityChartCanvas) {
        const pitchingAbilitiesData = JSON.parse(pitchingAbilityChartCanvas.dataset.pitchingAbilities);
        
        const maxPitchLevel = Math.max(...pitchingAbilitiesData.data, 7);
        const suggestedMaxPitchingAbility = Math.ceil(maxPitchLevel / 1) * 1;
        const finalMaxPitchingAbility = Math.max(suggestedMaxPitchingAbility, 8);

        new Chart(pitchingAbilityChartCanvas, {
            type: 'radar',
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

    // 投手基本能力チャート (スタミナ, コントロール) を棒グラフに変更
    const pitchingFundamentalAbilityChartCanvas = document.getElementById('pitchingFundamentalAbilityChart');
    if (pitchingFundamentalAbilityChartCanvas) {
        const pitchingFundamentalAbilitiesData = JSON.parse(pitchingFundamentalAbilityChartCanvas.dataset.pitchingFundamentalAbilities);

        // スタミナとコントロールの最大値は100
        const maxFundamentalValue = Math.max(...pitchingFundamentalAbilitiesData.data);
        const suggestedMaxFundamental = Math.ceil(maxFundamentalValue / 20) * 20;
        const finalMaxFundamental = Math.max(suggestedMaxFundamental, 100); // 最低でも100は確保

        new Chart(pitchingFundamentalAbilityChartCanvas, {
            type: 'bar',
            data: {
                labels: pitchingFundamentalAbilitiesData.labels,
                datasets: [{
                    label: '基本能力値',
                    data: pitchingFundamentalAbilitiesData.data,
                    backgroundColor: 'rgba(255, 99, 132, 0.8)', // 赤系
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // 棒を横向きにする
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
                            display: false // 横棒グラフなのでY軸のグリッド線は不要
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
                    },
                    tooltip: { // ツールチップの表示を調整
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.x !== null) {
                                    label += context.parsed.x;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    // ★球速比較チャート (新しい棒グラフ) の追加★
    const pitchingVelocityComparisonChartCanvas = document.getElementById('pitchingVelocityComparisonChart');
    if (pitchingVelocityComparisonChartCanvas) {
        const pitchingVelocityComparisonData = JSON.parse(pitchingVelocityComparisonChartCanvas.dataset.pitchingVelocityComparison);

        const maxVelocity = Math.max(...pitchingVelocityComparisonData.data);
        const suggestedMaxVelocity = Math.ceil(maxVelocity / 20) * 20; // 20刻みで切り上げ
        const finalMaxVelocity = Math.max(suggestedMaxVelocity, 160); // 最低でも160は確保

        new Chart(pitchingVelocityComparisonChartCanvas, {
            type: 'bar',
            data: {
                labels: pitchingVelocityComparisonData.labels,
                datasets: [{
                    label: '球速 (km/h)',
                    data: pitchingVelocityComparisonData.data,
                    backgroundColor: [ // 各棒の色を調整
                        'rgba(0, 123, 255, 0.8)', // あなたの球速 (青系)
                        'rgba(255, 193, 7, 0.8)', // チーム内投手平均 (黄系)
                        'rgba(23, 162, 184, 0.8)' // 投手全体平均 (水色系)
                    ],
                    borderColor: [
                        'rgba(0, 123, 255, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(23, 162, 184, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // 棒を横向きにする
                scales: {
                    x: {
                        // ★ここを変更★
                        beginAtZero: false, // 0から開始しない
                        min: 100,           // 最小値を100に設定
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
                        text: '球速 (チーム・全体との比較)',
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


    // 投手総合ランクチャート (棒グラフ) - ロジックは変更なし
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