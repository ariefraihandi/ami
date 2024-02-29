function reloadPageWithNewDates() {
    // Mengambil nilai dari input tanggal mulai dan tanggal akhir
    var startDate = document.getElementById("startDate").value;
    var endDate = document.getElementById("endDate").value;

    // Membuat URL baru dengan tanggal yang dipilih
    var newURL = "/keuangan/laporan?startDate=" + startDate + "&endDate=" + endDate;

    // Memuat ulang halaman dengan URL baru
    window.location.href = newURL;
  }

  // Menambahkan event listener untuk memanggil fungsi reloadPageWithNewDates() saat nilai input tanggal berubah
  document.getElementById("startDate").addEventListener("change", reloadPageWithNewDates);
  document.getElementById("endDate").addEventListener("change", reloadPageWithNewDates);