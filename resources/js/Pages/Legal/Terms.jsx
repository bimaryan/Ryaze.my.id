import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../../Layouts/PublicLayout';

export default function Terms({ seoTitle, seoDescription, canonical, ogImage }) {
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
                <h1 className="text-3xl md:text-4xl font-black tracking-tight mb-4">Syarat & Ketentuan</h1>
                <p className="text-sm text-slate-500 dark:text-slate-400 mb-10">Terakhir diperbarui: 4 November 2026</p>

                <div className="space-y-8 text-base leading-relaxed text-slate-900 dark:text-slate-100">
                    <section>
                        <h2 className="text-xl font-bold mb-3">1. Penerimaan Ketentuan</h2>
                        <p>Dengan mendaftar atau menggunakan layanan Ryaze (shared hosting, jasa pengembangan website/aplikasi, dan produk digital), Anda menyetujui syarat dan ketentuan ini.</p>
                    </section>

                    <section>
                        <h2 className="text-xl font-bold mb-3">2. Akun & Keamanan</h2>
                        <ul className="list-disc pl-6 space-y-2">
                            <li>Anda bertanggung jawab menjaga kerahasiaan kata sandi dan aktivitas pada akun Anda.</li>
                            <li>Aktivasi verifikasi dua faktor (2FA) sangat disarankan untuk keamanan akun.</li>
                        </ul>
                    </section>

                    <section>
                        <h2 className="text-xl font-bold mb-3">3. Penggunaan yang Diperbolehkan</h2>
                        <p>Layanan tidak boleh digunakan untuk konten atau aktivitas yang melanggar hukum Republik Indonesia, termasuk namun tidak terbatas pada: distribusi malware, phishing, penipuan, konten pornografi anak, perjudian ilegal, spam massal, dan pelanggaran hak kekayaan intelektual pihak lain. Pelanggaran dapat mengakibatkan penangguhan layanan tanpa pengembalian dana.</p>
                    </section>

                    <section>
                        <h2 className="text-xl font-bold mb-3">4. Pembayaran & Perpanjangan</h2>
                        <ul className="list-disc pl-6 space-y-2">
                            <li>Tagihan hosting diterbitkan sesuai siklus langganan (bulanan/tahunan) yang Anda pilih.</li>
                            <li>Layanan dapat ditangguhkan bila tagihan melewati jatuh tempo sesuai kebijakan pengingat yang berlaku.</li>
                            <li>Harga dapat berubah dengan pemberitahuan sebelumnya; harga aktif saat pemesanan tetap berlaku sampai akhir periode tagihan.</li>
                        </ul>
                    </section>

                    <section>
                        <h2 className="text-xl font-bold mb-3">5. Jasa Pengembangan</h2>
                        <p>Pekerjaan jasa pembuatan website/aplikasi mengikuti lingkup yang disepakati di awal proyek. Revisi di luar lingkup dapat dikenakan biaya tambahan setelah persetujuan kedua pihak.</p>
                    </section>

                    <section>
                        <h2 className="text-xl font-bold mb-3">6. Batasan Tanggung Jawab</h2>
                        <p>Layanan disediakan "sebagaimana adanya". Kami menargetkan ketersediaan 99,9% namun tidak menjamin layanan bebas gangguan sepenuhnya. Tanggung jawab kami atas kerugian langsung dibatasi maksimal sebesar nilai tagihan bulanan berjalan.</p>
                    </section>

                    <section>
                        <h2 className="text-xl font-bold mb-3">7. Pengakhiran Layanan</h2>
                        <p>Anda dapat berhenti berlangganan kapan saja. Kami dapat mengakhiri layanan atas pelanggaran ketentuan ini. Data proyek yang dihapus tidak dapat dipulihkan — lakukan cadangan (backup) secara berkala.</p>
                    </section>

                    <section>
                        <h2 className="text-xl font-bold mb-3">8. Perubahan Ketentuan</h2>
                        <p>Ketentuan ini dapat diperbarui dari waktu ke waktu. Perubahan material akan diumumkan melalui halaman <Link href="/updates" className="text-blue-600 dark:text-blue-400 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4">Info Update</Link>.</p>
                    </section>
                </div>
            </main>
        </PublicLayout>
    );
}
