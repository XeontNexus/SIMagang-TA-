<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Siswa Magang</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Data Dokumentasi Keseluruhan Siswa Magang</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>NISN</th>
                <th>Institusi</th>
                <th>Jurusan</th>
                <th>Kelas</th>
                <th>Mitra Magang</th>
                <th>Alamat Magang</th>
                <th>Tgl Mulai</th>
                <th>Tgl Selesai</th>
                <th>No HP</th>
                <th>Guru Pembimbing</th>
                <th>Pembimbing Lapangan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student->nama_lengkap }}</td>
                    <td style="mso-number-format:'\@'">{{ $student->nisn ?? '-' }}</td>
                    <td>{{ $student->institusi ?? '-' }}</td>
                    <td>{{ $student->jurusan?->nama_jurusan ?? $student->jurusan ?? '-' }}</td>
                    <td>{{ $student->kelas?->tingkat ?? '-' }} {{ $student->kelas?->nama_kelas ?? $student->kelas ?? '-' }}</td>
                    <td>{{ $student->mitra_magang ?? '-' }}</td>
                    <td>{{ $student->alamat_magang ?? '-' }}</td>
                    <td>{{ $student->tanggal_mulai ? $student->tanggal_mulai->format('d/m/Y') : '-' }}</td>
                    <td>{{ $student->tanggal_selesai ? $student->tanggal_selesai->format('d/m/Y') : '-' }}</td>
                    <td style="mso-number-format:'\@'">{{ $student->no_hp ?? '-' }}</td>
                    <td>{{ $student->guruPembimbing?->nama_guru ?? '-' }}</td>
                    <td>{{ $student->pembimbing_lapangan ?? '-' }}</td>
                    <td>{{ ucfirst($student->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
