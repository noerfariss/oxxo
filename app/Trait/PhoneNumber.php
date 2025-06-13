<?php

namespace App\Trait;

trait PhoneNumber
{
    protected function getToken()
    {
        $min = 100000;
        $max = 999999;

        return mt_rand($min, $max);
    }

    protected function formatWhatsAppNumber($phoneNumber)
    {
        // Menghapus semua karakter kecuali angka
        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);

        // Menghapus awalan "0" dari nomor telepon jika ada
        if (substr($phoneNumber, 0, 1) == '0') {
            $phoneNumber = substr($phoneNumber, 1);
        }

        // Menambahkan awalan internasional WhatsApp
        $whatsappNumber = '62' . $phoneNumber;

        return $whatsappNumber;
    }
}
