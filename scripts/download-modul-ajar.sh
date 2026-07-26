#!/bin/bash
# Download 36 file Modul Ajar SMP Kelas 7 Kurikulum Merdeka (asli, dari Google
# Drive publik via Modul Guruku) ke storage/app/public/modul-ajar/.
#
# CARA PAKAI (jalankan di server, BUKAN di sandbox lokal):
#   cd /www/wwwroot/sekolah.co.id/sekolahcoid
#   bash scripts/download-modul-ajar.sh
#
# Setelah selesai, jalankan sekali (kalau belum pernah):
#   php artisan storage:link
#
# Katalog di ModulAjarController otomatis pakai file lokal ini begitu ada,
# tanpa perlu ubah kode apa pun.

set -e
DEST="storage/app/public/modul-ajar"
mkdir -p "$DEST"

# Fungsi download Google Drive yang menangani halaman konfirmasi virus-scan
# untuk file berukuran lebih besar (pola umum yang dipakai banyak orang).
gdrive_download() {
    FILEID="$1"
    FILENAME="$2"
    COOKIE=$(mktemp)

    curl -sc "$COOKIE" "https://drive.google.com/uc?export=download&id=${FILEID}" > /dev/null
    CONFIRM=$(awk '/_warning_/ {print $NF}' "$COOKIE")

    if [ -n "$CONFIRM" ]; then
        curl -Lb "$COOKIE" "https://drive.google.com/uc?export=download&confirm=${CONFIRM}&id=${FILEID}" -o "$DEST/$FILENAME"
    else
        curl -Lb "$COOKIE" "https://drive.google.com/uc?export=download&id=${FILEID}" -o "$DEST/$FILENAME"
    fi
    rm -f "$COOKIE"

    # Validasi dasar: kalau hasil download ternyata halaman HTML (bukan
    # dokumen), kemungkinan link butuh login/berbeda - beri peringatan.
    if file "$DEST/$FILENAME" | grep -qi "html"; then
        echo "  PERINGATAN: $FILENAME kemungkinan gagal (dapat HTML, bukan dokumen). Cek manual."
    else
        echo "  OK: $FILENAME"
    fi
}

echo "Mengunduh modul Matematika..."
gdrive_download "1a3GsyQeqoXCwURbWHhDQk1e10FX09z72" "matematika-bab1-bilangan-bulat.docx"
gdrive_download "1DdjMQDfEh2guJmzOUfRU8QyYgOqhWz8B" "matematika-bab2-aljabar.docx"
gdrive_download "1okLpr-AkKG1o7eNrnyqTl-TYPFS91jKh" "matematika-bab3-persamaan-linear.docx"
gdrive_download "1Z2cpK7q9gqLy8zCqbz9DerrEkXG9z5xI" "matematika-bab4-perbandingan.docx"
gdrive_download "1Rb1mgXSwGVt1YS1ktCa2c1RhrAiFnDJG" "matematika-bab5-bangun-datar.docx"
gdrive_download "1QUhda9VMS5bxiRWXwQvR7mTGoP326cUt" "matematika-bab6-bangun-ruang.docx"
gdrive_download "1o21RRI1tu9EO18UJ7yY7rV56Kl_GdSQr" "matematika-bab7-data.docx"

echo "Mengunduh modul IPA..."
gdrive_download "1mXIFmyMlIfMoRXpQ_dZvs25ER1fjbWH_" "ipa-bab1-hakikat-sains.docx"
gdrive_download "1MdXvU6vKf1r-tKJCvZxdf0EqsYCzBjBr" "ipa-bab2-zat-perubahannya.docx"
gdrive_download "1atgnkFxzJxEunlHuhFk2eIXuHFWSoPCx" "ipa-bab3-suhu-kalor.docx"
gdrive_download "1_PrQN8p2vuDjp_YgwqtTAWi7IZUjEG6i" "ipa-bab4-gerak-gaya.docx"
gdrive_download "18XW3O6QY2-nV4wj6VUzgZWTRjHm2Uq0e" "ipa-bab5-klasifikasi.docx"
gdrive_download "1DE64UYxsoPM-QRy-68jsx556QEjqWtHj" "ipa-bab6-ekologi.docx"
gdrive_download "1kcrF0HNrJ6uM9kL5yxRRqnrA4SWUtJkS" "ipa-bab7-tata-surya.docx"

echo "Mengunduh modul IPS..."
gdrive_download "1RuajunOgmYwOCpO29J3NgzVfDyjDKtHD" "ips-tema1-keluarga.docx"
gdrive_download "1uYlKN2RETcG_uTCsO3QGtSSjXSbzaHym" "ips-tema2-lingkungan.docx"
gdrive_download "1IKpNi641AmbuCunR3GnKd_45ts9xLJ9I" "ips-tema3-ekonomi.docx"
gdrive_download "1myJdS4xbqhc4FM4-Qfq-bVTHKb3IO1yR" "ips-tema4-pemberdayaan.docx"

echo "Mengunduh modul Informatika..."
gdrive_download "1TFUSL2WyIG4SkCcHIFwgyX-6_J7Aj5rx" "informatika-bab1-generik.docx"
gdrive_download "1C_z5MF5Lhm7kowHM3Ew0n9MraCFLr5nS" "informatika-bab2-komputasional.docx"
gdrive_download "1syCE0lZlSl9DElM3Mc99umdT0VBeTkZ-" "informatika-bab3-tik.docx"
gdrive_download "1p-7qXQZpKYmWvptXpM9IYE3E-FKWC1uh" "informatika-bab4-sistem-komputer.docx"
gdrive_download "1puI5LnjxS4diDstpXGNSvR9EvU5R_WK6" "informatika-bab5-jaringan.docx"
gdrive_download "11C1BJ78m-XZrqb0UF_VRMuqSSCi7uwIj" "informatika-bab6-analisis-data.docx"
gdrive_download "16It1zRI5G97BqPfgX8V1M193Yb5ZniXT" "informatika-bab7-algoritma.docx"
gdrive_download "1JG8lVKTYWu0hP5J2wfdYYP267vrmcEwh" "informatika-bab8-dampak-sosial.docx"
gdrive_download "1iMuZbGFFPeC_mcrsZYqWeJ05EmHGD50t" "informatika-bab9-praktik.docx"

echo "Mengunduh modul PJOK..."
gdrive_download "1U2qI6IdU02WvTZprTBVRkv2W67SRfhyI" "pjok-unit1-basket.docx"
gdrive_download "177aPjnQvbBJFHF5U4QqVc3tM78L1VP_O" "pjok-unit2-voli.docx"
gdrive_download "1YUxIrrqwmbROe2sWtvh9QstEOty4flE9" "pjok-unit3-kasti.docx"
gdrive_download "1BkWygwAJYQVJkqLD0Kh2uDXyt70EHkDT" "pjok-unit4-beladiri.docx"
gdrive_download "1yfO5z57mOg_EiUdNAd-ck-_lZVM9l8p9" "pjok-unit5-atletik.docx"
gdrive_download "1sZRZHRXNFZ_oeXqro6r1vQTZLQG9WbYf" "pjok-unit6-senam-lantai.docx"
gdrive_download "1FsN-02-1FNOtH2bA2zZAhCKgNlk3JyNs" "pjok-unit7-senam-irama.docx"
gdrive_download "1Y2KqHidnMsTc1LfSXZ8szzn8j8WHZ1wZ" "pjok-unit8-1-kebugaran.docx"
gdrive_download "18cWfMhxM0uY9EpdaazyfsMEjtQ_ZADiW" "pjok-unit8-2-gizi.docx"

echo ""
echo "Selesai. Total file di $DEST:"
ls "$DEST" | wc -l
echo ""
echo "Kalau ada yang tertulis PERINGATAN di atas, cek manual - kemungkinan"
echo "link Drive-nya butuh permission tambahan atau formatnya bukan docx."
