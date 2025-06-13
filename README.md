## Laravel Absensi

### .:: Sebuah kebanggaan keluarga - pria punya selera ::.

- Laravel 12
- Php Min. 8.3
- Php Extension Gd harus aktif
- Mariab DB 11

### Instalasi
- Jalankan
```
    git clone https://github.com/noerfariss/absensi.git
```

- Copy .env.example menjadi .env, dan sesuaikan databasenya
- Jalankan perintah berikut secara berurutan
``` 
    php artisan key:generate
    php artisan storage:link
    php artisan jwt:screet
```

- Lakukan hal yang sama untuk migration database
``` 
    php artisan migrate
    php artisan db:seed
```

- Oiaa karena ada fungsi Geocode Reverse alias mengubah koordinat (latitude & longitude) menjadi alamat, saya di sini menggunakan dari MapBox bukan Google maps.. alasan dipilih murahnya bukan kualitasnya ;(

- Silahkan sesuaikan di bagian .env pada key MAPBOX_TOKEN, isikan dengan token yang sudah didapat dari MapBox

#
#

Untuk dijalankan di hosting, pastikan kamu sudah buat <b>.htaccess</b> di root folder. Dan copy paste script di bawah ini. (Hiraukan jika dijalankan di vps/VM)
``` 
DirectoryIndex index.php
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteRule ^$ public/index.php [L]
    RewriteRule ^((?!public/).*)$ public/$1 [L,NC]
</IfModule>

```

