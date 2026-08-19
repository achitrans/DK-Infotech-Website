<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Leave Status Update</title>
    <style>
        :root {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #f5f6fb;
            color: #0f172a;
        }

        body {
            margin: 0;
            display: flex;
            min-height: 100vh;
            justify-content: center;
            align-items: center;
            background: radial-gradient(circle at top, rgba(80, 61, 233, 0.12), transparent 35%),
                #f5f6fb;
        }

        .panel {
            background: #ffffff;
            width: min(420px, calc(100% - 32px));
            border-radius: 24px;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.15);
            padding: 2.5rem;
            text-align: left;
        }

        .eyebrow {
            text-transform: uppercase;
            letter-spacing: 0.3em;
            font-size: 0.7rem;
            color: #6366f1;
            margin: 0 0 0.75rem;
        }

        h1 {
            margin: 0;
            font-size: 1.75rem;
            line-height: 1.2;
        }

        .meta {
            margin: 1.25rem 0;
            display: grid;
            gap: 0.5rem;
            font-size: 0.95rem;
            color: #475569;
        }

        .meta span {
            font-weight: 600;
            color: #0f172a;
        }

        button {
            width: 100%;
            border: none;
            padding: 0.85rem;
            border-radius: 999px;
            font-size: 0.95rem;
            font-weight: 600;
            background: #4f46e5;
            color: white;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        button:active {
            transform: translateY(1px);
        }

        button:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .message {
            padding: 1rem 1.25rem;
            border-radius: 12px;
            background: #e0f2fe;
            color: #0f172a;
            margin-bottom: 1rem;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .message.neutral {
            background: #f1f5f9;
            border-color: rgba(148, 163, 184, 0.5);
        }

        .hint {
            font-size: 0.8rem;
            color: #94a3b8;
            margin-top: 1rem;
        }

        .link-cta {
            color: #4338ca;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="panel">
        <h1>Update leave status</h1>
        @php
            if ($leave->from_date == $leave->to_date){
                $dateString = $leave->from_date->format('d M Y');
            }else{
                $dateString = $leave->from_date->format('d M Y')." &ndash; ".$leave->to_date->format('d M Y');
            }
        @endphp
        <div class="meta">
            <div><span>Employee:</span> {{ optional($leave->user)->name ?? 'N/A' }}</div>
            <div><span>Date(s):</span> {{ $dateString }}</div>
            <div><span>Reason:</span> {{ $leave->reason ?? '-' }}</div>
            <div><span>Type:</span> {{ ucfirst($leave->leave_type) ?? '-' }}</div>
            <div><span>Status:</span> {{ ucfirst($leave->status) ?? '-' }}</div>
        </div>

        @isset($statusMessage)
            <div class="message {{ empty($statusUpdated) ? 'neutral' : '' }}">
                {{ $statusMessage }}
            </div>
            <p class="hint">Close this window when you are done.</p>
        @else
            <form method="POST" action="{{ route('leave.updateStatusPublicUrl.process', $string) }}">
                @csrf
                <label for="status" class="hint">Choose status to apply</label>
                <select name="status" id="status" required style="width:100%; padding:0.85rem; border-radius:12px; border:1px solid #cbd5f5; margin-bottom:1rem; font-size:0.95rem;">
                    <option value="" disabled {{ old('status', $selectedStatus ?? '') === '' ? 'selected' : '' }}>Select action</option>
                    @foreach ($statusOptions as $option)
                        <option value="{{ $option }}" {{ old('status', $selectedStatus ?? '') === $option ? 'selected' : '' }}>{{ ucfirst($option) }}</option>
                    @endforeach
                </select>
                <button type="submit">Confirm</button>
            </form>
            <p class="hint">The status will change after you tap the confirm button.</p>
        @endisset
    </div>
</body>

</html>
