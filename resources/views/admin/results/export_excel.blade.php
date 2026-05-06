<table>
    <thead>
        <tr>
            <th colspan="{{ 5 + count($indicators) }}" style="text-align: center; font-weight: bold; font-size: 16pt; color: #db2777;">
                LAPORAN DETAIL HASIL PENILAIAN CORE VALUES
            </th>
        </tr>
        <tr>
            <th colspan="{{ 5 + count($indicators) }}" style="text-align: center; font-weight: bold; font-size: 12pt;">
                Periode: {{ \Carbon\Carbon::parse($date)->isoFormat('D MMMM YYYY') }}
            </th>
        </tr>
        <tr>
            <th colspan="{{ 5 + count($indicators) }}" style="text-align: center; font-weight: bold; font-size: 12pt; border-bottom: 2px solid #000000;">
                Cabang: {{ $branch->name }}
            </th>
        </tr>
        <tr></tr>
        <tr>
            <th style="background-color: #db2777; color: #ffffff; border: 1px solid #000000; font-weight: bold; text-align: center;">Nama Karyawan</th>
            <th style="background-color: #db2777; color: #ffffff; border: 1px solid #000000; font-weight: bold; text-align: center;">Divisi</th>
            <th style="background-color: #db2777; color: #ffffff; border: 1px solid #000000; font-weight: bold; text-align: center;">Nama Penilai</th>
            @foreach($indicators as $indicator)
                <th style="background-color: #db2777; color: #ffffff; border: 1px solid #000000; font-weight: bold; text-align: center;">{{ explode(' (', $indicator->name)[0] }}</th>
            @endforeach
            <th style="background-color: #db2777; color: #ffffff; border: 1px solid #000000; font-weight: bold; text-align: center;">Total</th>
            <th style="background-color: #db2777; color: #ffffff; border: 1px solid #000000; font-weight: bold; text-align: center;">Rata-rata</th>
        </tr>
    </thead>
    <tbody>
        @foreach($results as $index => $item)
            @php $rowCount = count($item->assignments); @endphp
            
            <!-- Baris-baris Penilai -->
            @foreach($item->assignments as $aIndex => $assignment)
            <tr>
                @if($aIndex === 0)
                    <td rowspan="{{ $rowCount + 2 }}" style="border: 1px solid #000000; vertical-align: top; font-weight: bold;">{{ $item->name }}</td>
                    <td rowspan="{{ $rowCount + 2 }}" style="border: 1px solid #000000; vertical-align: top;">{{ $item->division }}</td>
                @endif
                <td style="border: 1px solid #000000; padding: 5px;">{{ $assignment->reviewer->name }}</td>
                @php $rowSum = 0; @endphp
                @foreach($indicators as $indicator)
                    @php 
                        $score = $assignment->assessments->where('indicator_id', $indicator->id)->first()->score ?? 0;
                        $rowSum += $score;
                    @endphp
                    <td style="border: 1px solid #000000; text-align: center;">{{ $score }}</td>
                @endforeach
                <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $rowSum }}</td>
                <td style="border: 1px solid #000000; text-align: center; color: #4b5563;">{{ number_format($rowSum / count($indicators), 2) }}</td>
            </tr>
            @endforeach

            <!-- Baris Ringkasan: Total per Variabel -->
            <tr>
                <td style="border: 1px solid #000000; font-weight: bold; background-color: #f9fafb;">Total per Variabel</td>
                @foreach($indicators as $indicator)
                    <td style="border: 1px solid #000000; text-align: center; font-weight: bold; color: #db2777; background-color: #fff1f2;">{{ $item->indicator_totals[$indicator->id] }}</td>
                @endforeach
                <td colspan="2" style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #db2777; color: #ffffff; vertical-align: middle;" rowspan="2">
                    NILAI AKHIR: {{ $item->total_score }}<br>
                    GRADE: {{ $item->grade }}
                </td>
            </tr>

            <!-- Baris Ringkasan: Rata-rata per Indikator -->
            <tr>
                <td style="border: 1px solid #000000; font-weight: bold; background-color: #f9fafb;">Rata-rata per Indikator</td>
                @foreach($indicators as $indicator)
                    <td style="border: 1px solid #000000; text-align: center; font-weight: bold; color: #db2777; background-color: #fff1f2;">{{ number_format($item->indicator_averages[$indicator->id], 2) }}</td>
                @endforeach
            </tr>

            <!-- Spasi antar karyawan -->
            <tr>
                <td colspan="{{ 5 + count($indicators) }}" style="height: 15px; border: none;"></td>
            </tr>
        @endforeach
    </tbody>
</table>
