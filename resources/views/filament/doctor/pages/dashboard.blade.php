<x-filament-panels::page>

<style>
    :root {
        --dash-card-bg:       #ffffff;
        --dash-card-border:   rgba(0,0,0,0.08);
        --dash-label-color:   rgba(0,0,0,0.4);
        --dash-section-sep:   rgba(0,0,0,0.06);
        --dash-icon-emerald:  rgba(16,185,129,0.12);
        --dash-icon-blue:     rgba(59,130,246,0.12);
        --dash-icon-amber:    rgba(245,158,11,0.12);
        --dash-act-blue-bg:   rgba(59,130,246,0.08);
        --dash-act-blue-brd:  rgba(59,130,246,0.2);
        --dash-act-blue-txt:  #1d4ed8;
        --dash-act-blue-sub:  rgba(29,78,216,0.6);
        --dash-act-grn-bg:    rgba(16,185,129,0.08);
        --dash-act-grn-brd:   rgba(16,185,129,0.2);
        --dash-act-grn-txt:   #065f46;
        --dash-act-grn-sub:   rgba(6,95,70,0.6);
        --dash-act-teal-bg:   rgba(20,184,166,0.08);
        --dash-act-teal-brd:  rgba(20,184,166,0.2);
        --dash-act-teal-txt:  #134e4a;
        --dash-act-teal-sub:  rgba(19,78,74,0.6);
        --dash-val-emerald:   #059669;
        --dash-val-blue:      #2563eb;
        --dash-val-amber:     #d97706;
    }
    .dark {
        --dash-card-bg:       #1c1c1e;
        --dash-card-border:   rgba(255,255,255,0.08);
        --dash-label-color:   rgba(255,255,255,0.4);
        --dash-section-sep:   rgba(255,255,255,0.06);
        --dash-icon-emerald:  rgba(16,185,129,0.12);
        --dash-icon-blue:     rgba(59,130,246,0.12);
        --dash-icon-amber:    rgba(245,158,11,0.12);
        --dash-act-blue-bg:   rgba(59,130,246,0.08);
        --dash-act-blue-brd:  rgba(59,130,246,0.25);
        --dash-act-blue-txt:  #93c5fd;
        --dash-act-blue-sub:  rgba(147,197,253,0.6);
        --dash-act-grn-bg:    rgba(16,185,129,0.08);
        --dash-act-grn-brd:   rgba(16,185,129,0.25);
        --dash-act-grn-txt:   #6ee7b7;
        --dash-act-grn-sub:   rgba(110,231,183,0.6);
        --dash-act-teal-bg:   rgba(20,184,166,0.08);
        --dash-act-teal-brd:  rgba(20,184,166,0.25);
        --dash-act-teal-txt:  #5eead4;
        --dash-act-teal-sub:  rgba(94,234,212,0.6);
        --dash-val-emerald:   #10b981;
        --dash-val-blue:      #3b82f6;
        --dash-val-amber:     #f59e0b;
    }
</style>

    <div style="display:grid;gap:1.5rem;">

        {{-- Hero --}}
        <div style="grid-column:1/-1;">
            <div style="background:linear-gradient(135deg,#0f2744 0%,#1a4068 50%,#0e6e6e 100%);border-radius:12px;padding:28px;display:flex;align-items:center;justify-content:space-between;position:relative;overflow:hidden;">
                <div style="position:absolute;top:-32px;right:-32px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,0.04);"></div>
                <div style="position:absolute;bottom:-48px;right:64px;width:208px;height:208px;border-radius:50%;background:rgba(255,255,255,0.03);"></div>
                <div style="position:relative;z-index:1;">
                    <h2 style="font-size:1.5rem;font-weight:700;color:#fff;margin-bottom:4px;">{{ $this->getGreeting() }}</h2>
                    <p style="font-size:0.875rem;color:rgba(255,255,255,0.6);">{{ $this->getWelcomeMessage() }}</p>
                </div>
                <div style="position:relative;z-index:1;width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg style="width:24px;height:24px;color:rgba(255,255,255,0.8);" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div style="grid-column:1/-1;display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;">

            {{-- Today's Appointments --}}
            <div style="background:var(--dash-card-bg);border-radius:12px;border:1px solid var(--dash-card-border);padding:24px;position:relative;overflow:hidden;">
                <div style="position:absolute;top:0;left:0;width:3px;height:100%;background:#10b981;"></div>
                <div style="padding-left:12px;">
                    <div style="width:36px;height:36px;border-radius:8px;background:var(--dash-icon-emerald);display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                        <svg style="width:18px;height:18px;color:#10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--dash-label-color);margin-bottom:4px;">{{ __('doctor.todays_appointments') }}</p>
                    <p style="font-size:28px;font-weight:700;color:var(--dash-val-emerald);">0</p>
                </div>
            </div>

            {{-- Total Patients --}}
            <div style="background:var(--dash-card-bg);border-radius:12px;border:1px solid var(--dash-card-border);padding:24px;position:relative;overflow:hidden;">
                <div style="position:absolute;top:0;left:0;width:3px;height:100%;background:#3b82f6;"></div>
                <div style="padding-left:12px;">
                    <div style="width:36px;height:36px;border-radius:8px;background:var(--dash-icon-blue);display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                        <svg style="width:18px;height:18px;color:#3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                    <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--dash-label-color);margin-bottom:4px;">{{ __('doctor.total_patients') }}</p>
                    <p style="font-size:28px;font-weight:700;color:var(--dash-val-blue);">0</p>
                </div>
            </div>

            {{-- Monthly Revenue --}}
            <div style="background:var(--dash-card-bg);border-radius:12px;border:1px solid var(--dash-card-border);padding:24px;position:relative;overflow:hidden;">
                <div style="position:absolute;top:0;left:0;width:3px;height:100%;background:#f59e0b;"></div>
                <div style="padding-left:12px;">
                    <div style="width:36px;height:36px;border-radius:8px;background:var(--dash-icon-amber);display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                        <svg style="width:18px;height:18px;color:#f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--dash-label-color);margin-bottom:4px;">{{ __('doctor.monthly_revenue') }}</p>
                    <p style="font-size:28px;font-weight:700;color:var(--dash-val-amber);">$0</p>
                </div>
            </div>

        </div>

        {{-- Quick Actions --}}
        <div style="grid-column:1/-1;background:var(--dash-card-bg);border-radius:12px;border:1px solid var(--dash-card-border);padding:24px;">
            <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--dash-label-color);margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--dash-section-sep);">
                {{ __('doctor.quick_actions') }}
            </p>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;">

                <a href="#" style="display:flex;align-items:center;gap:12px;padding:16px;border-radius:10px;border:1px solid var(--dash-act-blue-brd);background:var(--dash-act-blue-bg);text-decoration:none;">
                    <div style="width:36px;height:36px;border-radius:8px;background:#2563eb;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg style="width:17px;height:17px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size:13px;font-weight:600;color:var(--dash-act-blue-txt);">{{ __('doctor.create_appointment') }}</p>
                        <p style="font-size:11px;color:var(--dash-act-blue-sub);margin-top:2px;">{{ __('doctor.add_new_appointment_slots') }}</p>
                    </div>
                </a>

                <a href="#" style="display:flex;align-items:center;gap:12px;padding:16px;border-radius:10px;border:1px solid var(--dash-act-grn-brd);background:var(--dash-act-grn-bg);text-decoration:none;">
                    <div style="width:36px;height:36px;border-radius:8px;background:#059669;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg style="width:17px;height:17px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size:13px;font-weight:600;color:var(--dash-act-grn-txt);">{{ __('doctor.view_calendar') }}</p>
                        <p style="font-size:11px;color:var(--dash-act-grn-sub);margin-top:2px;">{{ __('doctor.check_your_schedule') }}</p>
                    </div>
                </a>

                <a href="#" style="display:flex;align-items:center;gap:12px;padding:16px;border-radius:10px;border:1px solid var(--dash-act-teal-brd);background:var(--dash-act-teal-bg);text-decoration:none;">
                    <div style="width:36px;height:36px;border-radius:8px;background:#0d9488;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg style="width:17px;height:17px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size:13px;font-weight:600;color:var(--dash-act-teal-txt);">{{ __('doctor.manage_bookings') }}</p>
                        <p style="font-size:11px;color:var(--dash-act-teal-sub);margin-top:2px;">{{ __('doctor.view_patient_bookings') }}</p>
                    </div>
                </a>

            </div>
        </div>

    </div>
</x-filament-panels::page>