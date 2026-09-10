<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Flash;
use App\Core\View;
use App\Models\Message;

final class ContactController
{
    public function show(): string
    {
        return View::render('front/contact', [
            'pageTitle'       => 'İletişim',
            'metaDescription' => 'Bize ulaşabileceğiniz adres, telefon, e-posta ve iletişim formu.',
        ], 'front');
    }

    public function submit(): never
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $subject = trim((string) ($_POST['subject'] ?? ''));
        $body = trim((string) ($_POST['body'] ?? ''));
        $honeypot = trim((string) ($_POST['website'] ?? ''));

        // Gizli alan doluysa bot isteğidir: sessizce başarı gibi davran.
        if ($honeypot !== '') {
            Flash::set('success', 'Mesajınız alındı, teşekkür ederiz.');
            redirect('/iletisim');
        }

        $errors = [];
        if (mb_strlen($name) < 2) {
            $errors[] = 'Lütfen adınızı yazın.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Geçerli bir e-posta adresi girin.';
        }
        if (mb_strlen($body) < 10) {
            $errors[] = 'Mesajınız en az 10 karakter olmalı.';
        }

        if ($errors !== []) {
            keep_old($_POST);
            Flash::set('error', implode(' ', $errors));
            redirect('/iletisim');
        }

        Message::create([
            'name'    => $name,
            'email'   => $email,
            'phone'   => $phone !== '' ? $phone : null,
            'subject' => $subject !== '' ? $subject : null,
            'body'    => $body,
        ]);

        clear_old();
        Flash::set('success', 'Mesajınız alındı. En kısa sürede size dönüş yapacağız.');
        redirect('/iletisim');
    }
}
