<?php

return [
    'buttons' => [
        'buy'     => 'خرید VPS',
        'support' => 'پشتیبانی',
        'manage'  => 'مدیریت سرورها',
        'topup'   => 'افزایش موجودی',
        'back'    => '⬅️ بازگشت',
        'back_main' => '⬅️ بازگشت به منوی اصلی', // 👈 برای ReplyKeyboard تک‌دکمه‌ای
        'confirm' => 'تایید',
        'cancel'  => 'لغو',
    ],

    'welcome' => 'به ربات خوش آمدید 👋 لطفاً یکی از گزینه‌ها را انتخاب کنید:',

    'providers' => [
        'gcore' => 'Gcore',
    ],

    'plans' => [
        'g2s_shared_1_1_25' => 'Plan 1',
        'g2s_shared_1_2_25' => 'Plan 2',
    ],

    'regions' => [
        'dubai'     => '🇦🇪 Dubai',
        'london'    => '🇬🇧 London',
        'frankfurt' => '🇩🇪 Frankfurt',
    ],

    'os' => [
        'ubuntu22' => 'Ubuntu 22',
        'debian12' => 'Debian 12',
    ],

    'buy' => [
        'choose_provider' => 'لطفاً ارائه‌دهنده را انتخاب کنید:',
        'choose_plan'     => 'لطفاً پلن را انتخاب کنید:',
        'choose_location' => 'لطفاً لوکیشن سرور را انتخاب کنید:',
        'choose_os'       => 'لطفاً نسخه سیستم‌عامل را انتخاب کنید:',
        'confirm'         => "خلاصه سفارش:\nProvider: :provider\nPlan: :plan\nRegion: :region\nOS: :os\n\nتایید می‌کنید؟",
        'submitting'      => 'در حال ثبت و ایجاد سرور… ⏳',
        'submitted'       => '✅ درخواست شما ثبت شد. نتیجه به‌زودی اطلاع‌رسانی می‌شود.',
    ],

    'wallet' => [
        'enter_amount' => '💰 لطفاً مبلغ شارژ به تومان را فقط عدد وارد کنید.',
        'send_receipt' => 'لطفاً رسید پرداخت را ارسال کنید.',
        'received'     => '✅ رسید شما دریافت شد. پس از بررسی نتیجه اطلاع داده می‌شود.',
        'invalid_amount'=> '❗️ مبلغ وارد شده نامعتبر است.',
        'invalid_photo' => '❗️ تصویر نامعتبر بود. لطفاً مجدد ارسال کنید.',
        'request_missing'=> '❗️ درخواست شارژ در حال انتظار یافت نشد.',
    ],

    'support' => [
        'enter' => '🛠 پیام خود را برای پشتیبانی بنویسید.',
        'sent'  => '✅ پیام شما ثبت شد.',
    ],

    'servers' => [
        'list'  => '📦 لیست سرورهای شما:',
        'empty' => 'هنوز سروری ندارید.',
        'item'  => '- #:id : :provider / :region / :plan',
    ],
];
