# OXXO Laundry

### .:: Sebuah kebanggaan keluarga - pria punya selera ::.

- Laravel 12
- Php Min. 8.3
- Php Extension Gd harus aktif
- Mariab DB 11

### Instalasi
- Jalankan
```
    git clone https://github.com/noerfariss/oxxo.git
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

