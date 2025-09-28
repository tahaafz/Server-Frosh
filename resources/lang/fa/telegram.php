<?php

return [
    'buttons' => [
        'back'      => '⬅️ بازگشت',
        'buy'       => 'خرید VPS',
        'support'   => 'پشتیبانی',
        'manage'    => 'مدیریت سرورها',
        'topup'     => 'افزایش موجودی',
        'approve'   => '✅ تایید',
        'reject'    => '❌ رد',
        'cancel'    => 'لغو',
        'reply'     => '✍️ پاسخ',
    ],
    'wallet' => [
        'enter_amount' => '💰 لطفاً مبلغ شارژ را به تومان وارد کنید (فقط عدد).',
        'send_receipt' => 'برای ادامه، لطفاً <b>عکس رسید</b> را ارسال کنید.',
        'received'     => '✅ رسید شما دریافت شد. پس از بررسی ادمین، نتیجه اطلاع داده می‌شود.',
        'topup_approved' => "✅ شارژ کیف پول تایید شد.\nمبلغ: <b>:amount</b> تومان\nموجودی فعلی: <b>:balance</b> تومان",
        'topup_rejected' => '❌ رسید شما تایید نشد. در صورت سوال، با پشتیبانی در ارتباط باشید.',
        'invalid_amount' => '❗️ مبلغ معتبر نیست. یک عدد (حداقل ۵۰,۰۰۰ تومان) ارسال کنید.',
        'invalid_photo'  => '❗️دریافت عکس نامعتبر بود. دوباره ارسال کنید.',
        'request_not_found' => '❗️درخواست شارژ یافت نشد. دوباره تلاش کنید.',
        'canceled'          => '❎ درخواست شارژ لغو شد.',
    ],
    'errors' => [
        'private_only' => '⛔️ این بات فقط در گفت‌وگوی خصوصی کار می‌کند.',
        'request_expired' => '⏱ این درخواست منقضی شده.',
        'request_expired_short' => 'این درخواست منقضی شده',
    ],
    'channel' => [
        'join'       => 'عضویت در کانال',
        'check'      => '✅ عضو شدم، بررسی کن',
        'prompt'     => "برای ادامه لازم است در کانال ما عضو باشید.\nپس از عضویت، روی «عضو شدم، بررسی کن» بزنید.",
        'not_member' => 'هنوز عضو کانال نیستید.',
    ],
    'admin' => [
        'approved'                  => 'تایید شد',
        'rejected'                  => 'رد شد',
        'reply_prompt'              => '✍️ پاسخ خود را بنویسید (متن یا عکس).',
        'support_reply_prefix'      => '🛠 پاسخ پشتیبانی:',
        'support_reply_photo'       => '🛠 پاسخ پشتیبانی (تصویر)',
        'reply_sent'                => '✅ پاسخ ارسال شد.',
        'reply_photo_sent'          => '✅ پاسخ تصویری ارسال شد.',
        'invalid_photo'             => '❗️ ارسال تصویر نامعتبر بود.',
        'support_from_user_title'   => '📩 پیام پشتیبانی از کاربر',
    ],
    'topup' => [
        'request_title' => '🧾 درخواست شارژ کیف پول',
        'line_amount'   => 'مبلغ: <b>:amount</b> تومان',
        'line_method'   => 'روش: <code>:method</code>',
        'line_id'       => 'ID: <code>:id</code>',
    ],
    'payment' => [
        'card' => [
            'instruction_title' => '💳 اطلاعات واریز کارت به کارت:',
            'to_name'           => 'به نام: <b>:name</b>',
            'card_number'       => 'شماره کارت: <code>:card</code>',
            'amount_line'       => 'مبلغ: <b>:amount</b> تومان',
            'after_payment'     => 'لطفاً پس از واریز، <b>عکس رسید</b> را در همین گفتگو ارسال کنید.',
        ],
    ],
    'buy' => [
        'choose_plan'     => "🔹 لطفاً پلن را انتخاب کنید:\n• 1GB RAM / 1 vCPU / 25GB\n• 2GB RAM / 1 vCPU / 25GB",
        'choose_provider' => '🔰 ارائه‌دهنده را انتخاب کنید:',
        'choose_location' => '📍 لطفاً لوکیشن را انتخاب کنید:',
        'choose_os'       => '🖥 نسخهٔ سیستم‌عامل را انتخاب کنید:',
        'summary_title'   => '🧾 خلاصه سفارش:',
        'confirm_and_send'=> '✅ تایید و ارسال',
        'back'            => '⬅️ برگشت',
        'submitted'       => "✅ درخواست شما ثبت شد.\nپس از ساخت، مشخصات اتصال برایتان ارسال می‌شود.",
    ],
    'spam' => [
        'reason'       => 'اسپم در ربات',
        'user_blocked' => "🚫 شما به دلیل ارسال بیش از حد پیام (اسپم) مسدود شده‌اید.\nاگر فکر می‌کنید اشتباه است، با پشتیبانی تماس بگیرید.",
    ],
    'servers' => [
        'action_failed'    => '❌ اجرای عملیات روی سرور <code>:name</code> ناموفق بود.\nلطفاً کمی بعد دوباره تلاش کنید.',
        'action_requested' => '✅ درخواست «:action» برای سرور <code>:name</code> ارسال شد.\nممکن است چند لحظه زمان ببرد.',
        'action_name'      => [
            'start'  => 'راه‌اندازی',
            'stop'   => 'خاموش',
            'delete' => 'حذف',
        ],
        'create_unavailable' => '❌ در حال حاضر امکان ساخت سرور وجود ندارد.\nلطفاً کمی بعد دوباره تلاش کنید یا با پشتیبانی تماس بگیرید.',
        'ip_pending'         => '— (در حال آماده‌سازی)',
        'created_message'    => "✅ سرور شما ساخته شد\n\n".
                                "ارائه‌دهنده: <b>:provider</b>\n".
                                "نام: <code>:name</code>\n".
                                "پلن: <code>:plan</code>\n".
                                "لوکیشن: <code>:location</code>\n".
                                "IP: :ip\n\n".
                                "ورود:\n• Username: <code>:login_user</code>\n• Password: <code>:login_pass</code>",
        'manage_button'     => '📋 مدیریت سرور',
        'list' => [
            'none'  => 'هنوز سروری ندارید. از «خرید VPS» برای ساخت سرور جدید استفاده کنید.',
            'title' => '📄 لیست سرورهای شما:',
        ],
        'panel' => [
            'not_found'      => 'سرور یافت نشد.',
            'name'           => '🖥 نام: <code>:name</code>',
            'provider'       => 'ارائه‌دهنده: <b>:provider</b>',
            'plan'           => 'پلن: <code>:plan</code>',
            'location'       => 'لوکیشن: <code>:location</code>',
            'status'         => 'وضعیت: <b>:status</b>',
            'ip'             => 'IP: :ip',
            'refresh'        => '🔄 بروزرسانی',
            'delete'         => '🗑 حذف',
            'list'           => '⬅️ لیست',
            'not_enough_info'=> 'اطلاعات کافی برای بروزرسانی وجود ندارد.',
            'not_manageable' => 'سرور قابل مدیریت نیست.',
            'action_queued'  => 'درخواست :action سرور ثبت شد. نتیجه اطلاع‌رسانی می‌شود.',
        ],
    ],
    'blocked' => [
        'by_admin'      => '🚫 شما توسط مدیریت مسدود شده‌اید.',
        'reason_prefix' => 'دلیل: :reason',
    ],
    'welcome' => [
        'intro' => "به ربات خوش آمدید 👋\nلطفاً یکی از گزینه‌ها را انتخاب کنید:",
    ],
    'support' => [
        'prompt'   => "🛠 پشتیبانی — پیام‌تان را بنویسید.\nبرای بازگشت: /back",
        'received' => 'پیام دریافت شد ✅',
    ],
];
