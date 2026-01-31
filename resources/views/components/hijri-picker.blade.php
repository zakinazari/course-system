@props(['model'])

<div 
    x-data="{
        date: @entangle($model),
        convertAndSet(val) {
            if(!val) return;
            // تبدیل تاریخ میلادی به هجری (تقویم ام‌القری) برای نمایش یا ذخیره
            let date = new Date(val);
            let hijriDate = new Intl.DateTimeFormat('ar-SA-u-ca-islamic-uma', {
                day: 'numeric', month: 'long', year: 'numeric'
            }).format(date);
            
            console.log('Selected Hijri:', hijriDate);
            this.date = val; // ذخیره تاریخ میلادی استاندارد در دیتابیس
        }
    }"
    class="w-full"
>
    <input 
        type="date" 
        class="form-control"
        x-model="date"
        @change="convertAndSet($event.target.value)"
        style="direction: rtl;"
    >
    <template x-if="date">
        <div class="mt-1 text-sm text-success">
            تاریخ هجری: 
            <span x-text="new Intl.DateTimeFormat('ar-SA-u-ca-islamic-uma', {day: 'numeric', month: 'long', year: 'numeric'}).format(new Date(date))"></span>
        </div>
    </template>
</div>
