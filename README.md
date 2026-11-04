# 🌐 Ryaze.my.id

![Web Status](https://img.shields.io/website?url=https%3A%2F%2Fryaze.my.id&label=Ryaze.my.id)
![GitHub last commit](https://img.shields.io/github/last-commit/bimaryan/Ryaze.my.id)
![GitHub repo size](https://img.shields.io/github/repo-size/bimaryan/Ryaze.my.id)
![License](https://img.shields.io/badge/License-MIT-blue.svg)

Selamat datang di repositori **Ryaze.my.id**! Ini adalah *source code* untuk website personal / portofolio saya yang dapat diakses langsung melalui [https://ryaze.my.id](https://ryaze.my.id).

## 🚀 Tentang Proyek
Website ini dibangun untuk menampilkan profil, portofolio proyek, pengalaman, dan keahlian saya. Proyek ini didesain agar responsif, cepat, dan memiliki antarmuka yang ramah pengguna (User Friendly).

## ✨ Fitur Utama
*   **Desain Responsif:** Tampilan yang menyesuaikan dengan sempurna di berbagai perangkat (Mobile, Tablet, Desktop).
*   **Performa Cepat:** Dioptimalkan untuk kecepatan pemuatan halaman.
*   **Mode Gelap/Terang (Dark/Light Mode):** Pengalaman visual yang bisa disesuaikan (Jika ada).
*   **Showcase Portofolio:** Menampilkan proyek-proyek terbaik yang pernah saya kerjakan.
*   **Formulir Kontak:** Memudahkan pengunjung untuk terhubung dengan saya.

## 🛠️ Teknologi yang Digunakan
*(Silakan sesuaikan teknologi di bawah ini dengan stack asli yang Anda gunakan)*
*   **Frontend:** HTML5, CSS3, JavaScript / [React.js](https://reactjs.org/) / [Vue.js](https://vuejs.org/)
*   **Styling:** [Tailwind CSS](https://tailwindcss.com/) / Bootstrap
*   **Hosting/Deployment:** Vercel / Netlify / GitHub Pages

## 💻 Cara Menjalankan Secara Lokal (Local Development)

Jika Anda ingin mencoba menjalankan proyek ini di mesin lokal Anda, ikuti langkah-langkah berikut:

1. **Clone Repositori**
   ```bash
   git clone https://github.com/bimaryan/Ryaze.my.id.git
   ```

2. **Pindah ke Direktori Proyek**
   ```bash
   cd Ryaze.my.id
   ```

3. **Install Dependencies** (Jika menggunakan Node.js/NPM)
   ```bash
   npm install
   ```

4. **Jalankan Server Lokal**
   ```bash
   npm run dev
   # atau buka file index.html di browser (jika menggunakan HTML/CSS biasa)
   ```

## SEO dan pemeriksaan setelah deploy

- Metadata halaman Inertia dibaca dari `page.props` di `resources/views/inertia.blade.php`, sehingga judul, deskripsi, canonical, Open Graph, dan robots tersedia di respons HTML awal. Setelah React aktif, `Head` mengambil alih metadata halaman; tag robots menggunakan `head-key="robots"` agar tidak terduplikasi.
- `/sitemap.xml` hanya memuat rute publik serta artikel dan info update yang sudah tayang. Draft, konten terjadwal, dan kategori tanpa artikel terbit tidak disertakan. `lastmod` hanya diberikan ketika tanggal perubahan konten diketahui, bukan waktu sitemap diakses.
- Halaman autentikasi, area dengan middleware `auth`, transaksi, dan unduhan terkait diberi header `X-Robots-Tag: noindex, nofollow`. Ini bukan pengganti autentikasi atau otorisasi.
- Pastikan `APP_URL=https://ryaze.my.id` di produksi dan gunakan redirect permanen di web server dari HTTP/domain alternatif ke domain utama. Sesuaikan URL sitemap di `public/robots.txt` bila domain berubah.
- Setelah deploy, kirim `https://ryaze.my.id/sitemap.xml` melalui Google Search Console. Periksa beranda, artikel, kategori, dan halaman kedua blog menggunakan URL Inspection, serta data artikel melalui Rich Results Test. Structured data tidak menjamin rich result atau peringkat tertentu.
- Konten utama React masih dirender di browser; perbaikan metadata ini **bukan SSR penuh**. Periksa HTML hasil render Google sebelum mempertimbangkan SSR Inertia untuk konten dan tautan yang lebih cepat ditemukan.

Jalankan tes regresi SEO dan build frontend sebelum deploy:

```bash
php artisan test --filter=SeoRenderTest
npm run build
```

Acuan: [Panduan Memulai SEO Google Search](https://developers.google.com/search/docs/fundamentals/seo-starter-guide?hl=id).

## 📬 Kontak

Jika Anda memiliki pertanyaan, saran, atau peluang kolaborasi, jangan ragu untuk menghubungi saya melalui:

*   **Email:** bimaryan046@gmail.com
*   **Instagram:** https://instagram.com/bima_ryan23

---
Dibuat dengan ❤️ oleh [Bima Ryan](https://github.com/bimaryan)
