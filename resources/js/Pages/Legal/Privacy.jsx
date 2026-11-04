import { Head } from '@inertiajs/react';
import PublicLayout from '../../Layouts/PublicLayout';

export default function Privacy({ seoTitle, seoDescription, canonical, ogImage }) {
    return (
        <PublicLayout bodyClass="bg-white font-sans antialiased text-slate-900 dark:bg-slate-950 dark:text-slate-100">
            <Head title={seoTitle}>
                <meta name="description" content={seoDescription} />
                <link rel="canonical" href={canonical} />
                <meta property="og:type" content="website" />
                <meta property="og:site_name" content="Ryaze" />
                <meta property="og:locale" content="id_ID" />
                <meta property="og:url" content={canonical} />
                <meta property="og:title" content={seoTitle} />
                <meta property="og:description" content={seoDescription} />
                <meta property="og:image" content={ogImage} />
                <meta name="twitter:card" content="summary_large_image" />
                <meta name="twitter:url" content={canonical} />
                <meta name="twitter:title" content={seoTitle} />
                <meta name="twitter:description" content={seoDescription} />
                <meta name="twitter:image" content={ogImage} />
            </Head>

            <main className="w-full max-w-3xl mx-auto px-6 pt-28 pb-20 flex-1">
                <h1 className="text-3xl md:text-4xl font-black tracking-tight mb-4">Kebijakan Privasi</h1>
                <p className="text-sm text-slate-500 dark:text-slate-400 mb-10">Terakhir diperbarui: 4 November 2026</p>

                <div className="space-y-8 text-base leading-relaxed text-slate-900 dark:text-slate-100">
                    <section>
                        <h2 className="text-xl font-bold mb-3">1. Data yang Kami Kumpulkan</h2>
                        <ul className="list-disc pl-6 space-y-2">
                            <li><strong>Data akun:</strong> nama, alamat email, dan informasi profil yang Anda isi saat mendaftar.</li>
                            <li><strong>Data transaksi:</strong> riwayat pesanan, tagihan, dan status pembayaran (kami tidak menyimpan nomor penuh kartu atau kredensial bank Anda).</li>
                            <li><strong>Data teknis:</strong> alamat IP, jenis peramban, dan log aktivitas server untuk keamanan dan pemecahan masalah.</li>
                            <li><strong>Data preferensi:</strong> pilihan tema (terang/gelap) disimpan di perangkat Anda melalui localStorage, bukan di server kami.</li>
                        </ul>
                    </section>

                    <section>
                        <h2 className="text-xl font-bold mb-3">2. Penggunaan Data</h2>
                        <p>Data digunakan untuk: menyediakan dan memelihara layanan, memproses transaksi, mengirim pemberitahuan layanan (mis. info pemeliharaan server), mencegah penyalahgunaan, serta meningkatkan kualitas layanan.</p>
                    </section>

                    <section>
                        <h2 className="text-xl font-bold mb-3">3. Analitik & Cookie</h2>
                        <p>Situs ini dapat menggunakan Google Analytics untuk statistik pengunjungan secara agregat dan anonim. Anda dapat menonaktifkannya melalui pengaturan peramban atau add-on pengoptimalan iklan.</p>
                    </section>

                    <section>
                        <h2 className="text-xl font-bold mb-3">4. Berbagi Data</h2>
                        <p>Kami tidak menjual data pribadi Anda. Data hanya dapat dibagikan sebatas yang diperlukan untuk menjalankan layanan (mis. registrar domain untuk pendaftaran domain) atau bila diwajibkan oleh hukum yang berlaku.</p>
                    </section>

                    <section>
                        <h2 className="text-xl font-bold mb-3">5. Keamanan Data</h2>
                        <p>Kami menerapkan enkripsi TLS/HTTPS, pembatasan akses berbasis peran, dan pencatatan aktivitas admin. Tidak ada metode transmisi melalui internet yang 100% aman, namun kami berupaya melindungi data dengan cara yang wajar secara komersial.</p>
                    </section>

                    <section>
                        <h2 className="text-xl font-bold mb-3">6. Retensi & Penghapusan</h2>
                        <p>Data akun disimpan selama akun aktif. Anda dapat meminta penghapusan akun dan data pribadi dengan menghubungi kami; sebagian data wajib disimpan untuk kepatuhan pembukuan sesuai ketentuan hukum.</p>
                    </section>

                    <section>
                        <h2 className="text-xl font-bold mb-3">7. Kontak</h2>
                        <p>Pertanyaan tentang kebijakan ini dapat dikirim melalui halaman <a href="/consultation" className="text-blue-600 dark:text-blue-400 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4">Konsultasi</a> atau saluran dukungan resmi Ryaze.</p>
                    </section>
                </div>
            </main>
        </PublicLayout>
    );
}
