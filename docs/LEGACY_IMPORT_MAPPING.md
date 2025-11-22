# Legacy Import Mapping – Sensipay Mini

File ini menjelaskan aturan konversi dari Excel/CSV lama ke struktur invoice & payment baru.

- Positive amount (bukan "Diskon") → harga utama (base price)
- Baris dengan item mengandung "Diskon" dan nilai negatif → diskon (mengurangi harga)
- Baris dengan amount 0 dan kata "Free" → bonus, tidak mempengaruhi angka
- Baris dengan amount negatif lain → pembayaran / angsuran (dibuat sebagai Payment terpisah)

Total invoice:
- base_price = penjumlahan semua amount positif
- discount   = penjumlahan absolut amount negatif yang item-nya mengandung "Diskon"
- total_amount = max(0, base_price - discount)

Pembayaran:
- Setiap baris negatif non-diskon dibuat sebagai Payment dengan amount = ABS(amount)
- paid_amount = jumlah seluruh payment untuk invoice tersebut

Status invoice:
- unpaid  → paid_amount <= 0
- partial → 0 < paid_amount < total_amount
- paid    → paid_amount >= total_amount

Controller `LegacyInstallmentImportController` di patch ini mengimplementasikan aturan di atas
dengan asumsi CSV memiliki header:

```text
parent_name, parent_email, student_name, invoice_code, item, amount
```

Silakan sesuaikan jika nama kolom di CSV berbeda.
