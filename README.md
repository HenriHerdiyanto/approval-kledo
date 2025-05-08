<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Langkah menjalankan project ( HENRI HERDIYANTO )
1. simpan repository ini di local anda
2. jalankan phpmyadmin
3. php artisan serve
4. php artisan l5-swagger:generate
5. buka url baru <a href="http://localhost:8000/api/documentation" target="_blank">http://localhost:8000/api/documentation</a>
6. untuk menguji coba phpunit jalankan php artisan test --filter=ExpenseApprovalFlowTest

## Langkah menjalankan project manual melalui swagger
1. pada api status tambahkan 2 data yaitu "menunggu persetujuan" & "disetujui"
2. pada api approvers tambahkan 3 data yaitu "Ana", "Ani", dan ,"Ina"
3. ketika menambahkan data approvers maka akan otomatis menambahkan data juga pada tabel approval_stages
4. tambahkan data amount pada api expenses maka akan otomatis menambah data juga pada tabel approvals
5. logika inti dari pembuatan api ini berada pada url approvals kolom approver_id default nya adalah null atau inisialisasi awal dimana belum ada yang meng-approve
6. approver_id awalnya hanya bisa di update menjadi 1 setelah itu menjadi 2 setelah itu menjadi 3. tidak bisa langsung 2 atau 3 karna sesuai peraturan harus berjenjang
7. jika melangkah maka akan muncul meesage
   'message' => 'Proses approval harus mengikuti urutan yang benar.',
   'error' => 'Approver ID tidak sesuai dengan urutan yang benar.'
8. jika sesuai aturan dan approver_id bernilai 3 maka kolom status_id akan otomatis berubah menjadi 2 alias "disetujui"
9. kolom status_id pada tabel expenses juga akan terupdate menjadi 2 alias "disetujui"
10. jika approver_id masih 1 atau 2 maka akan muncul message "menunggu persetujuan"

