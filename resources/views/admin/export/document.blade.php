<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:w="urn:schemas-microsoft-com:office:word"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    <title>Data Siswa Magang</title>
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>100</w:Zoom>
            <w:DoNotOptimizeForBrowser/>
        </w:WordDocument>
    </xml>
    <![endif]-->
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 12pt; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; vertical-align: top; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .student-block { margin-bottom: 30px; page-break-inside: avoid; }
    </style>
</head>
<body>
    <h2>Laporan Dokumentasi Keseluruhan Siswa Magang</h2>
    
    @foreach($students as $index => $student)
    <div class="student-block">
        <h3>{{ $index + 1 }}. {{ $student->nama_lengkap }} ({{ $student->institusi ?? '-' }})</h3>
        <table>
            <tr>
                <th width="30%">Informasi Pribadi</th>
                <td width="70%">
                    <b>NISN:</b> {{ $student->nisn ?? '-' }}<br>
                    <b>No HP:</b> {{ $student->no_hp ?? '-' }}<br>
                    <b>Jurusan:</b> {{ $student->jurusan?->nama_jurusan ?? $student->jurusan ?? '-' }}<br>
                    <b>Kelas:</b> {{ $student->kelas?->tingkat ?? '-' }} {{ $student->kelas?->nama_kelas ?? $student->kelas ?? '-' }}
                </td>
            </tr>
            <tr>
                <th>Penempatan Magang</th>
                <td>
                    <b>Mitra Magang:</b> {{ $student->mitra_magang ?? '-' }}<br>
                    <b>Alamat:</b> {{ $student->alamat_magang ?? '-' }}<br>
                    <b>Waktu Magang:</b> {{ $student->tanggal_mulai ? $student->tanggal_mulai->format('d/m/Y') : '-' }} s/d {{ $student->tanggal_selesai ? $student->tanggal_selesai->format('d/m/Y') : '-' }}<br>
                </td>
            </tr>
            <tr>
                <th>Pembimbing</th>
                <td>
                    <b>Guru Pembimbing:</b> {{ $student->guruPembimbing?->nama_guru ?? '-' }}<br>
                    <b>Pembimbing Lapangan:</b> {{ $student->pembimbing_lapangan ?? '-' }}
                </td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ ucfirst($student->status) }}</td>
            </tr>
        </table>
    </div>
    @endforeach

</body>
</html>
