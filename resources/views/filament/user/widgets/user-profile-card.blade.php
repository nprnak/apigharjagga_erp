@php
    $user = auth()->user();
    $initials = strtoupper(substr($user->name ?? 'U', 0, 2));
    $accountNo = str_pad($user->id ?? 1, 5, '0', STR_PAD_LEFT);
@endphp

<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm text-center" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.5rem; text-align: center;">
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
        <!-- Large Avatar with Badge (Strict Circular Container) -->
        <div style="position: relative; width: 68px; height: 68px; display: inline-block; margin: 0 auto;">
            <div style="width: 68px; height: 68px; min-width: 68px; min-height: 68px; max-width: 68px; max-height: 68px; border-radius: 50%; background-color: #2563eb; color: #ffffff; font-size: 1.35rem; font-weight: 700; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(37,99,235,0.2);">
                {{ $initials }}
            </div>
            <span style="position: absolute; bottom: 0; right: 0; width: 20px; height: 20px; border-radius: 50%; background-color: #3b82f6; color: #ffffff; font-size: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                1
            </span>
        </div>

        <!-- User Info -->
        <h3 class="mt-4 text-base font-bold text-slate-900" style="margin-top: 1rem; font-size: 1.05rem; font-weight: 700; color: #0f172a;">
            {{ $user->name ?? 'User' }}
        </h3>
        <p class="mt-1 text-xs text-slate-500 font-normal" style="margin-top: 0.25rem; font-size: 0.75rem; color: #64748b;">
            {{ $user->email ?? 'user@example.com' }} &bull; Account #{{ $accountNo }}
        </p>
    </div>
</div>
