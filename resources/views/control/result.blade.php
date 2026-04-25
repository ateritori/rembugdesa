<div class="control-result-section">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Hasil Kontrol</h3>

        <div class="space-y-4">
            @if (isset($results) && count($results) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-gray-700">Nama</th>
                                <th class="px-4 py-2 text-left text-gray-700">Nilai</th>
                                <th class="px-4 py-2 text-left text-gray-700">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($results as $result)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2 text-gray-800">{{ $result->name ?? '-' }}</td>
                                    <td class="px-4 py-2 text-gray-600">{{ $result->value ?? '-' }}</td>
                                    <td class="px-4 py-2">
                                        <span
                                            class="px-2 py-1 rounded text-xs font-semibold
                                            @if ($result->status === 'valid') bg-green-100 text-green-800
                                            @elseif($result->status === 'warning')
                                                bg-yellow-100 text-yellow-800
                                            @else
                                                bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($result->status ?? 'unknown') }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <p>Tidak ada data kontrol untuk ditampilkan.</p>
                </div>
            @endif
        </div>
    </div>
</div>
