<x-mail::message>
# 🤖 گزارش مشکل حل‌نشده دستیار هوشمند ادورا (AI Escalation)

کاربری در هنگام استفاده از چت‌بات هوشمند وب‌سایت با مشکلی مواجه شده که نیاز به بررسی و پیگیری توسط تیم مدیریت و پشتیبانی دارد.

<x-mail::panel>
**زمان ثبت:** {{ $timestamp }}

**نام کاربر:** {{ $userName ?: 'کاربر مهمان (Guest)' }}

**ایمیل کاربر:** {{ $userEmail ?: ($userContact ?: 'ارائه نشده') }}

**اطلاعات تماس اضافی:** {{ $userContact ?: 'ندارد' }}
</x-mail::panel>

### ❓ متن سوال یا پیام کاربر:
> {{ $userQuestion }}

### ⚠️ خلاصه مشکل و علت ارجاع به ادمین:
> {{ $issueSummary }}

<x-mail::button :url="url('/admin-panel')" color="primary">
ورود به پنل مدیریت ادورا
</x-mail::button>

با احترام،  
سیستم دستیار هوشمند {{ config('app.name', 'Edvora Tech') }}
</x-mail::message>
