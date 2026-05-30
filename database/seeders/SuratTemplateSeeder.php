<?php

namespace Database\Seeders;

use App\Models\SuratTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuratTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SuratTemplate::create([
            'nama_template' => 'Template Standar Surat Izin Magang',
            'isi_template' => 'Dengan hormat,

Dalam rangka pelaksanaan kurikulum SMK Negeri 1 Perhentian Raja yang mensyaratkan siswa untuk melaksanakan Praktik Kerja Lapangan (PKL) / Magang Industri guna meningkatkan keterampilan dan kompetensi keahlian, maka dengan ini kami memohon kesediaan Bapak/Ibu untuk dapat menerima siswa kami melaksanakan PKL di perusahaan/instansi yang Bapak/Ibu pimpin.

Adapun data siswa tersebut adalah sebagai berikut:

Nama Lengkap : [NAMA_SISWA]
NIS : [NIS]
Kelas : [KELAS]
Kompetensi Keahlian : [JURUSAN]
No. HP Siswa : [NO_HP]

Mitra/Perusahaan Tujuan : [MITRA_TUJUAN]
Alamat : [ALAMAT_MITRA]

Pelaksanaan Praktik Kerja Lapangan (PKL) ini direncanakan selama kurang lebih 3-6 bulan sesuai dengan kalender akademik sekolah.

Demikian permohonan ini kami sampaikan. Atas perhatian dan kerja sama yang baik dari Bapak/Ibu, kami ucapkan terima kasih.',
            'deskripsi' => 'Template standar untuk surat izin magang dengan format umum',
        ]);

        SuratTemplate::create([
            'nama_template' => 'Template Surat Izin Magang Dengan Detail Penuh',
            'isi_template' => 'Dengan hormat,

Dalam rangka pelaksanaan Program Pendidikan dan Pelatihan Berbasis Kompetensi di SMK Negeri 1 Perhentian Raja, siswa kami harus melaksanakan Praktik Kerja Lapangan (PKL) / Magang Industri. Hal ini dimaksudkan untuk meningkatkan keterampilan dan kompetensi profesional sesuai dengan bidang keahliannya.

Oleh karena itu, dengan ini kami mohon secara hormat kepada Bapak/Ibu Pimpinan [MITRA_TUJUAN] untuk berkenan menerima siswa kami melaksanakan PKL di lembaga/perusahaan yang Bapak/Ibu pimpin.

Berikut adalah data siswa yang akan melaksanakan PKL:

DATA SISWA
───────────────────────────────────
Nama Lengkap : [NAMA_SISWA]
Nomor Induk Siswa (NIS) : [NIS]
NISN : [NIS]
Kelas : [KELAS]
Kompetensi Keahlian : [JURUSAN]
No. HP : [NO_HP]

DATA PERUSAHAAN / MITRA
───────────────────────────────────
Nama Perusahaan : [MITRA_TUJUAN]
Atas Nama : [PIMPINAN_MITRA]
Alamat : [ALAMAT_MITRA]

Ketentuan Pelaksanaan:
1. Siswa akan melaksanakan PKL selama 3-6 bulan sesuai dengan kalender akademik sekolah
2. Siswa diharapkan dapat menerapkan ilmu yang telah didapat di sekolah
3. Laporan dan evaluasi akan dikirimkan kepada pihak sekolah setelah program selesai

Demikian surat permohonan ini kami sampaikan. Atas kesediaan dan kerja sama yang baik dari Bapak/Ibu, kami ucapkan terima kasih yang sebesar-besarnya.

Hormat kami,
SMK Negeri 1 Perhentian Raja',
            'deskripsi' => 'Template surat izin magang dengan format detail dan informasi lengkap',
        ]);

        SuratTemplate::create([
            'nama_template' => 'Template Surat Izin Magang Formal',
            'isi_template' => 'Dengan Hormat,

Sebagai bagian dari kurikulum pendidikan di SMK Negeri 1 Perhentian Raja, siswa kami diwajibkan mengikuti Praktik Kerja Lapangan (PKL) untuk mengembangkan keterampilan dan kesiapan memasuki dunia kerja.

Berkaitan dengan hal tersebut, kami bermohon dengan hormat agar Bapak/Ibu Pimpinan [MITRA_TUJUAN] berkenan menerima siswa kami untuk melaksanakan PKL di institusi yang Bapak/Ibu kelola.

Identitas Siswa:
- Nama : [NAMA_SISWA]
- NIS : [NIS]
- Kelas : [KELAS]
- Program Keahlian : [JURUSAN]
- Telepon : [NO_HP]

Institusi Tujuan:
- Nama Institusi : [MITRA_TUJUAN]
- Pimpinan : [PIMPINAN_MITRA]
- Lokasi : [ALAMAT_MITRA]

Durasi pelaksanaan PKL diperkirakan 3-6 bulan sesuai dengan program dan kebutuhan perusahaan.

Kami berharap dapat menjalin kerja sama yang baik dalam menunjang pendidikan berkualitas bagi generasi muda. Atas perhatian Bapak/Ibu, kami ucapkan terima kasih.

Salam Hormat,
SMK Negeri 1 Perhentian Raja',
            'deskripsi' => 'Template surat izin magang dengan bahasa formal resmi',
        ]);
    }
}
