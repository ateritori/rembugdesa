{{-- Tab: Hasil Akhir --}}
<div class="space-y-8 animate-in fade-in duration-700">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-100 pb-6">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Hasil Akhir Keputusan</h2>
            <p class="text-slate-500 mt-1">Analisis konvergensi antara preferensi personal Anda dan konsensus kelompok.
            </p>
        </div>
        @if ($decisionSession->status === 'closed')
            <div class="flex items-center gap-2 px-4 py-2 bg-emerald-50 border border-emerald-100 rounded-xl">
                <span class="relative flex h-3 w-3">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <span class="text-sm font-bold text-emerald-700 uppercase tracking-wide">Keputusan Final</span>
            </div>
        @endif
    </div>

    @if ($decisionSession->status !== 'closed')
        {{-- State: Belum Ditutup --}}
        <div
            class="flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-16 text-center">
            <div class="rounded-2xl bg-white shadow-sm p-4 text-amber-500 mb-4">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m0 0v2m0-2h2m-2 0H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800">Menunggu Finalisasi Administrator</h3>
            <p class="text-slate-500 max-w-sm mx-auto mt-2 text-sm leading-relaxed">Hasil perhitungan agregat Borda dan
                visualisasi kontribusi akan otomatis muncul setelah sesi ini ditutup.</p>
        </div>
    @else
        {{-- VISUALISASI GRID --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            {{-- CARD 1: RADAR CHART --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest font-mono">Peta Preferensi
                    </h3>
                    <svg class="w-4 h-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM5.884 6.944a1 1 0 10-1.414-1.414l.707-.707a1 1 0 101.414 1.414l-.707.707zm8.232-1.414a1 1 0 00-1.414 1.414l.707.707a1 1 0 001.414-1.414l-.707-.707zM8.867 14.035a1 1 0 01.595-.939l3-1.5a1 1 0 011.214 1.428l-3.5 3.5a1 1 0 01-1.414 0l-1.5-1.5a1 1 0 011.414-1.414l.791.791z">
                        </path>
                    </svg>
                </div>
                <div class="relative h-[280px]">
                    <canvas id="contributionRadarChart"></canvas>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-50 flex flex-wrap gap-4 justify-center">
                    <div class="flex items-center gap-2 text-[10px] font-bold text-slate-500 uppercase">
                        <span class="w-2.5 h-2.5 rounded-sm bg-indigo-500"></span> Anda (SMART)
                    </div>
                    <div class="flex items-center gap-2 text-[10px] font-bold text-slate-500 uppercase">
                        <span class="w-2.5 h-2.5 rounded-sm bg-slate-300"></span> Kelompok (Borda)
                    </div>
                </div>
            </div>

            {{-- CARD 2: LOLLIPOP CHART (SELISIH) --}}
            <div
                class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest font-mono">Divergensi
                        Peringkat</h3>
                    <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-1 rounded font-bold uppercase">Delta
                        Rank</span>
                </div>
                <p class="text-[11px] text-slate-400 mb-6 italic">Visualisasi jarak antara peringkat pilihan Anda dengan
                    peringkat akhir konsensus.</p>
                <div class="relative h-[250px]">
                    <canvas id="rankDifferenceChart"></canvas>
                </div>
            </div>

            {{-- CARD 3: TABEL KONTRIBUSI (FULL WIDTH) --}}
            <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-slate-50/50 px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-slate-700 text-sm uppercase tracking-wider">Audit Rincian Nilai</h3>
                    <div class="text-[10px] font-medium text-slate-400 uppercase tracking-tighter italic font-mono">
                        Individual SMART Score vs Group Borda</div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-slate-400 border-b bg-slate-50/30">
                                <th class="px-6 py-4 text-left font-bold text-xs uppercase">Alternatif Program</th>
                                <th class="px-6 py-4 text-right font-bold text-xs uppercase">Skor SMART</th>
                                <th class="px-6 py-4 text-center font-bold text-xs uppercase text-indigo-500">Rank Anda
                                </th>
                                <th class="px-6 py-4 text-right font-bold text-xs uppercase">Skor Borda</th>
                                <th class="px-6 py-4 text-center font-bold text-xs uppercase">Rank Final</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse ($resultContribution as $row)
                                <tr class="hover:bg-indigo-50/20 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div
                                            class="text-slate-800 font-bold group-hover:text-indigo-600 transition-colors uppercase tracking-tight">
                                            {{ $row->alternative->name ?? '-' }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-mono tracking-tighter">REF-ID:
                                            A{{ $row->alternative->id ?? '0' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono text-slate-500">
                                        {{ number_format($row->smart_score, 4) }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="inline-block px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 text-[11px] font-bold">#{{ $row->smart_rank }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono text-slate-600">
                                        {{ number_format($row->borda_score, 0) }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-center bg-slate-50/50 group-hover:bg-indigo-50/50 transition-colors">
                                        <div class="flex justify-center">
                                            <span
                                                class="flex items-center justify-center h-8 w-8 rounded-lg {{ $row->final_rank == 1 ? 'bg-indigo-600 text-white shadow-md' : 'bg-white border border-slate-200 text-slate-600' }} font-black text-xs transition-transform group-hover:scale-110">
                                                {{ $row->final_rank }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">Data belum
                                        tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Insight Box --}}
        <div
            class="bg-gradient-to-br from-slate-800 to-indigo-950 rounded-3xl p-8 text-white shadow-xl shadow-indigo-100 relative overflow-hidden mt-8">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl"></div>
            <div class="relative z-10 flex flex-col md:flex-row gap-6 items-start">
                <div
                    class="bg-indigo-500/20 border border-indigo-400/30 rounded-2xl p-4 shrink-0 shadow-inner text-indigo-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-indigo-200 mb-2 leading-none">Cara Memahami Data</h4>
                    <p class="text-sm text-indigo-100/80 leading-relaxed font-light">
                        Peringkat akhir dihitung menggunakan algoritma <span class="text-white font-semibold">Borda
                            Count</span> yang mengakomodasi suara seluruh Decision Maker. Radar chart menunjukkan
                        korelasi profil keputusan Anda terhadap kelompok, sementara grafik lollipop menunjukkan tingkat
                        keselarasan peringkat secara spesifik.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- SCRIPTS --}}
@if ($decisionSession->status === 'closed' && $resultContribution->isNotEmpty())
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Konfigurasi Font Global
            const chartFont = {
                family: "'Inter', sans-serif",
                size: 11
            };
            const labels = {!! json_encode($resultContribution->map(fn($r, $i) => 'A' . ($r->alternative_id ?? $i + 1))) !!};

            // --- RADAR CHART ---
            const radarCtx = document.getElementById('contributionRadarChart');
            const individualData = {!! json_encode($resultContribution->pluck('smart_score')) !!};
            const groupData = {!! json_encode($resultContribution->pluck('borda_score')) !!};
            const maxBorda = Math.max(...groupData) || 1;
            const normalizedGroupData = groupData.map(val => val / maxBorda);

            new Chart(radarCtx, {
                type: 'radar',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Anda (SMART)',
                            data: individualData,
                            backgroundColor: 'rgba(79, 70, 229, 0.15)',
                            borderColor: 'rgb(79, 70, 229)',
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: 'rgb(79, 70, 229)'
                        },
                        {
                            label: 'Kelompok (Borda)',
                            data: normalizedGroupData,
                            backgroundColor: 'rgba(148, 163, 184, 0.1)',
                            borderColor: 'rgb(148, 163, 184)',
                            borderDash: [5, 5],
                            borderWidth: 1.5,
                            pointRadius: 0
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            ticks: {
                                display: false
                            },
                            grid: {
                                color: '#f1f5f9'
                            },
                            angleLines: {
                                color: '#f1f5f9'
                            },
                            pointLabels: {
                                font: {
                                    ...chartFont,
                                    weight: 'bold'
                                },
                                color: '#64748b'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // --- LOLLIPOP CHART (SELISIH) ---
            const smartRanks = {!! json_encode($resultContribution->pluck('smart_rank')) !!};
            const finalRanks = {!! json_encode($resultContribution->pluck('final_rank')) !!};
            const rankDiff = smartRanks.map((val, i) => Math.abs(val - (finalRanks[i] || 0)));

            const lollipopCtx = document.getElementById('rankDifferenceChart');
            new Chart(lollipopCtx, {
                data: {
                    labels: labels,
                    datasets: [{
                            type: 'scatter',
                            label: 'Selisih Peringkat',
                            data: rankDiff.map((v, i) => ({
                                x: i,
                                y: v
                            })),
                            pointBackgroundColor: rankDiff.map(v => v === 0 ? '#10b981' : '#4f46e5'),
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 7,
                            pointHoverRadius: 9,
                            order: 1
                        },
                        {
                            type: 'bar',
                            data: rankDiff,
                            backgroundColor: '#e2e8f0',
                            barPercentage: 0.05,
                            categoryPercentage: 1.0,
                            order: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 10,
                            displayColors: false,
                            callbacks: {
                                label: (ctx) => ` Selisih: ${ctx.parsed.y} Peringkat`
                            }
                        }
                    },
                    scales: {
                        x: {
                            type: 'category',
                            labels: labels,
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: chartFont,
                                color: '#64748b'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            suggestedMax: Math.max(...rankDiff) + 1,
                            ticks: {
                                stepSize: 1,
                                font: chartFont,
                                color: '#94a3b8'
                            },
                            grid: {
                                color: '#f1f5f9',
                                drawBorder: false
                            },
                            title: {
                                display: true,
                                text: 'Peringkat',
                                font: {
                                    ...chartFont,
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endif
