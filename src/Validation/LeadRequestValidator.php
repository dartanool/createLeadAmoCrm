<?php

namespace App\Validation;

final class LeadRequestValidator
{
    public function validate(array $input): array
    {
        $errors = [];

        $name = trim((string) ($input['name'] ?? ''));
        $email = trim((string) ($input['email'] ?? ''));
        $phone = trim((string) ($input['phone'] ?? ''));
        $priceRaw = trim((string) ($input['price'] ?? ''));
        $timeOnSiteRaw = trim((string) ($input['time_on_site'] ?? '0'));

        if ($name === '') {
            $errors['name'][] = 'Имя обязательно';
        } elseif (mb_strlen($name) < 2) {
            $errors['name'][] = 'Имя должно быть не менее 2 символов';
        } elseif (mb_strlen($name) > 100) {
            $errors['name'][] = 'Имя должно быть не более 100 символов';
        } elseif (!preg_match('/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u', $name)) {
            $errors['name'][] = 'Имя содержит недопустимые символы';
        }

        if ($email === '') {
            $errors['email'][] = 'Email обязателен';
        } elseif (mb_strlen($email) > 255) {
            $errors['email'][] = 'Email слишком длинный';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'Некорректный email';
        }

        $cleanPhone = preg_replace('/[^\d+]/', '', $phone);

        if ($cleanPhone === '') {
            $errors['phone'][] = 'Телефон обязателен';
        } elseif (!preg_match('/^\+?\d{10,15}$/', $cleanPhone)) {
            $errors['phone'][] = 'Некорректный формат телефона';
        }

        if ($priceRaw === '') {
            $errors['price'][] = 'Цена обязательна';
        } elseif (!is_numeric($priceRaw)) {
            $errors['price'][] = 'Цена должна быть числом';
        } else {
            $price = (float) $priceRaw;

            if ($price <= 0) {
                $errors['price'][] = 'Цена должна быть больше 0';
            } elseif ($price > 1_000_000_000) {
                $errors['price'][] = 'Цена слишком большая';
            }
        }

        if (!ctype_digit($timeOnSiteRaw)) {
            $errors['time_on_site'][] = 'Время на сайте должно быть целым числом';
        }

        if ($errors !== []) {
            return [
                'data' => null,
                'errors' => $errors,
            ];
        }

        return [
            'data' => [
                'name' => $name,
                'email' => $email,
                'phone' => $cleanPhone,
                'price' => (float) $priceRaw,
                'time_on_site' => (int) $timeOnSiteRaw,
            ],
            'errors' => [],
        ];
    }
}