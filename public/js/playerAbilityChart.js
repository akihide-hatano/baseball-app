// public/js/playerChart.js

document.addEventListener('DOMContentLoaded', function() {

    // --- 打撃能力チャートの描画 ---
    const battingChartCanvas = document.getElementById('battingAbilityChart');

    if (battingChartCanvas && battingChartCanvas.dataset.battingAbilities) {
        try {
            const playerBattingAbilitiesData = JSON.parse(battingChartCanvas.dataset.battingAbilities);

            if (playerBattingAbilitiesData && playerBattingAbilitiesData.labels && playerBattingAbilitiesData.data) {
                const ctx = battingChartCanvas.getContext('2d');
                new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: playerBattingAbilitiesData.labels,
                        datasets: [{
                            label: '能力値',
                            data: playerBattingAbilitiesData.data,
                            backgroundColor: 'rgba(75, 192, 192, 0.4)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: 'rgba(75, 192, 192, 1)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: '選手打撃能力',
                                font: {
                                    size: 20
                                }
                            },
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            r: {
                                angleLines: { color: '#eee' },
                                grid: { color: '#ccc' },
                                pointLabels: { font: { size: 14 }, color: '#333' },
                                suggestedMin: 0,
                                suggestedMax: 100, // 打撃能力の想定最大値
                                ticks: { stepSize: 20, display: false },
                                backgroundColor: 'white'
                            }
                        }
                    }
                });
            } else {
                console.warn('グラフ描画に必要な打撃データが不足しています。', playerBattingAbilitiesData);
            }
        } catch (e) {
            console.error('打撃能力データのパースに失敗しました:', e);
        }
    } else {
        console.warn('battingAbilityChart canvas または data-batting-abilities 属性が見つかりません。');
    }

    // --- 投球能力（変化球）チャートの描画 ---
    const pitchingChartCanvas = document.getElementById('pitchingAbilityChart');

    if (pitchingChartCanvas && pitchingChartCanvas.dataset.pitchingAbilities) {
        try {
            const playerPitchingAbilitiesData = JSON.parse(pitchingChartCanvas.dataset.pitchingAbilities);

            if (playerPitchingAbilitiesData && playerPitchingAbilitiesData.labels && playerPitchingAbilitiesData.data) {
                const ctx = pitchingChartCanvas.getContext('2d');
                new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: playerPitchingAbilitiesData.labels,
                        datasets: [{
                            label: '変化球レベル',
                            data: playerPitchingAbilitiesData.data,
                            backgroundColor: 'rgba(255, 99, 132, 0.4)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: 'rgba(255, 99, 132, 1)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: '選手変化球能力',
                                font: {
                                    size: 20
                                }
                            },
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            r: {
                                angleLines: { color: '#eee' },
                                grid: { color: '#ccc' },
                                pointLabels: { font: { size: 14 }, color: '#333' },
                                suggestedMin: 0,
                                suggestedMax: 7, // 変化球レベルの最大値に合わせて調整（例: 7段階）
                                ticks: { stepSize: 1, display: false },
                                backgroundColor: 'white'
                            }
                        }
                    }
                });
            } else {
                console.warn('グラフ描画に必要な変化球データが不足しています。', playerPitchingAbilitiesData);
            }
        } catch (e) {
            console.error('変化球データのパースに失敗しました:', e);
        }
    } else {
        console.warn('pitchingAbilityChart canvas または data-pitching-abilities 属性が見つかりません。');
    }
});