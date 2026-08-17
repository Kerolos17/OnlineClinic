<div class="flex items-center gap-1">
    @foreach (['en' => 'EN', 'ar' => 'ع'] as $code => $label)
        @if (app()->getLocale() === $code)
            <span class="fi-icon-btn fi-color-gray fi-size-md cursor-default rounded-lg bg-gray-100 px-2.5 py-1.5 text-sm font-semibold text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                {{ $label }}
            </span>
        @else
            <a
                href="{{ route('lang.switch', $code) }}"
                class="fi-icon-btn fi-color-gray fi-size-md rounded-lg px-2.5 py-1.5 text-sm font-medium text-gray-700 transition hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800"
                title="{{ $code === 'en' ? __('doctor.english') : __('doctor.arabic') }}"
            >
                {{ $label }}
            </a>
        @endif
    @endforeach
</div>
