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
        'approve' => '✅ تایید',
        'reject'  => '❌ رد',
    ],

    'welcome' => 'به ربات خوش آمدید 👋',
    'choose' => ' لطفاً یکی از گزینه‌ها را انتخاب کنید:',
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
        'invalid_photo' => '❗️ تنها ارسال تصویر مجاز است. لطفاً مجدد ارسال کنید.',
        'request_missing'=> '❗️ درخواست شارژ در حال انتظار یافت نشد.',
        'topup_approved' => "✅ پرداخت شما به مبلغ :amount تومان تایید شد. موجودی فعلی شما: :balance تومان.",
        'topup_rejected' => '❌ پرداخت شما تایید نشد. لطفاً با پشتیبانی تماس بگیرید.',
        'manual_topup_instructions' => "برای افزایش موجودی به صورت دستی، مبلغ :amount تومان را به شماره‌ی حساب زیر واریز کنید👇🏻\n\n====================\n🔰 :card_number - :card_holder\n====================\n\n🌅 عکس رسید خود را در این مرحله ارسال نمایید.\n\n⚠️ حداقل واریز مبلغ 50 هزار تومان است\n⚠️ مسئولیت واریز اشتباهی با شماست.",
        'manual_topup_expired' => '⏰ پرداخت شما منقضی شد. لطفاً مجدداً برای افزایش موجودی اقدام کنید.',
    ],

    'topup' => [
        'request_title' => 'درخواست جدید افزایش موجودی ثبت شد',
        'line_amount'   => 'مبلغ: :amount تومان',
        'line_amount_html' => 'مبلغ: <code>:amount</code> تومان',
        'line_id'       => 'شناسه درخواست: :id',
        'line_id_html'  => 'شناسه درخواست: <code>:id</code>',
        'line_method'   => 'روش پرداخت: :method',
        'line_method_html' => 'روش پرداخت: <code>:method</code>',
        'line_user'     => 'کاربر: :user',
        'line_user_html'=> 'کاربر: <code>:user</code>',
    ],

    'support' => [
        'enter' => '🛠 پیام خود را برای پشتیبانی بنویسید.',
        'sent'  => '✅ پیام شما ثبت شد.',
    ],

    'admin' => [
        'reply_prompt'          => '✍️ لطفاً پاسخ خود به کاربر را ارسال کنید (۱۰ دقیقه فرصت دارید).',
        'support_reply_prefix'  => 'پاسخ پشتیبانی:',
        'reply_sent'            => '✅ پاسخ شما برای کاربر ارسال شد.',
        'reply_photo_sent'      => '✅ تصویر برای کاربر ارسال شد.',
        'approved'              => '✅ درخواست با موفقیت تایید شد.',
        'rejected'              => '❌ درخواست رد شد.',
        'support_reply_photo'   => '📷 پاسخ پشتیبانی (تصویر)',
        'invalid_photo'         => '❗️ ارسال تصویر نامعتبر بود، لطفاً دوباره امتحان کنید.',
        'support_from_user_title' => 'پیام جدید کاربر برای پشتیبانی',
        'support_from_user_meta'  => 'تیکت: :ticket • کاربر: :user • آی‌دی تلگرام: :telegram',
        'management_intro'      => "شناسه کاربر یا @یوزرنیم او را ارسال کنید تا اطلاعات نمایش داده شود. برای بازگشت، دکمه «لغو» را بفرستید.",
        'user_not_found'        => '❗️ کاربری با این مشخصات یافت نشد.',
        'user_status' => [
            'blocked' => 'مسدود',
            'active'  => 'فعال',
        ],
        'user_details' => "شناسه داخلی: <code>:id</code>\nنام: :name\nنام کاربری: :username\nTelegram ID: <code>:telegram_user_id</code>\nChat ID: <code>:telegram_chat_id</code>\nوضعیت: :status\nتاریخ ساخت حساب: :created_at\nآخرین پیام: :last_message_at\nموجودی: :balance تومان",
    ],

    'servers' => [
        'list'  => '📦 لیست سرورهای شما:',
        'empty' => 'هنوز سروری ندارید.',
        'item'  => '- #:id : :provider / :region / :plan',
    ],
];
