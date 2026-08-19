<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ID Card - @auth{{ auth()->user()->name }}@endauth
    </title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            display: flex;
            gap: 40px;
            padding: 50px;
        }

        .id-card {
            width: 320px;
            height: 500px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            background: linear-gradient(to bottom right, #f0faff, #dff6ff);
            border: 2px solid #00aaff;
        }

        .front,
        .back {
            padding: 20px;
            box-sizing: border-box;
        }

        .logo {
            text-align: center;
        }

        .logo img {
            height: 70px;
        }

        .photo {
            text-align: center;
            margin: 15px 0 5px;
        }

        .photo img {
            width: 120px;
            height: 120px;
            border-radius: 9px;
            border: 3px solid #1E90FF;
        }

        .name {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            color: #e63946;
        }

        .position {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }

        .info {
            font-size: 14px;
            margin: 6px 0;
            padding-left: 10px;
            display: flex;
            font-weight: bold;
        }

        .label {
            font-weight: bold;
            width: 100px;
            display: inline-block;
        }

        .value {
            flex: 1;
        }

        .signature {
            margin-top: 9px;
            text-align: right;
            font-style: italic;
            color: #555;
        }

        .back .title {
            margin-top: 20px;
            color: #e63946;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }

        .address,
        .contact {
            font-size: 14px;
            text-align: center;
            line-height: 1.5;
            font-weight: bold;
        }

        .contact {
            margin-top: 20px;
            font-size: 20px;
            font-weight: bold;
        }

        .qrcode {
            text-align: center;
        }

        .qrcode img {
            height: 150px;
        }

        /* Button styling */
        .buttons {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 20px;
        }

        .buttons button {
            background: #1E90FF;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
            transition: 0.3s;
        }

        .buttons button:hover {
            background: #005f99;
        }

        @media print {
            .buttons {
                display: none;
            }
        }
    </style>
</head>

<body id="id-cards">

    <!-- Front Side -->
    <div class="id-card front">
        <div class="logo">
            <img src="{{ asset('logo.png') }}" alt="{{ env('COMPANY_NAME') }} Logo">
        </div>
        <div class="photo">
            <img src="{{ \Illuminate\Support\Facades\Storage::url(auth()->user()->kyc->photograph_path) }}"
                alt="{{ auth()->user()->name }} Photo">
        </div>
        <div class="name">{{ auth()->user()->name }}</div>
        <div class="position">Department: {{ auth()->user()->department }} </div>
        <div class="info"><span class="label">E. ID:</span><span
                class="value">{{ auth()->user()->employee_id }}</span></div>
        <div class="info"><span class="label">Blood:</span><span
                class="value">{{ auth()->user()->kyc->blood_group }}</span></div>
        <div class="info"><span class="label">Phone:</span><span class="value">{{ env('COMPANY_PHONE2') }}</span>
        </div>
        <br>
        <img src="{{ asset('auth_sign.png') }}" alt="Authorised Sign" style="max-height: 60px; float: right">
        <div class="signature" style="margin-top: 70px">Authorised Sign. &nbsp; &nbsp;</div>
    </div>

    <!-- Back Side -->
    <div class="id-card back">
        <div class="logo">
            <img src="{{ asset('logo.png') }}" alt="{{ env('COMPANY_NAME') }} Logo">
        </div>
        <div class="title">{{ env('COMPANY_NAME') }}</div>
        <div class="address">
            {!! env('COMPANY_ADDRESS') !!}
        </div>
        <div class="contact">Mob: +91 {{ env('COMPANY_PHONE') }}</div>
        <br>
        <div class="qrcode">
            <img src="{{ asset('qrcode.jpg') }}" alt="QR Code">
        </div>
    </div>

    <!-- Buttons -->
    <div class="buttons">
        <button onclick="printPage()">🖨️ Print</button>
        {{--    <button onclick="downloadPDF()">⬇️ Download</button> --}}
    </div>

    <!-- JS for PDF & Print -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
    <script>
        function printPage() {
            window.print();
        }

        {{-- function downloadPDF() { --}}
        {{--  const element = document.getElementById('id-cards'); --}}
        {{--  const opt = { --}}
        {{--    margin: 0, --}}
        {{--    filename: 'ID_Card_{{ auth()->user()->name }}.pdf', --}}
        {{--    image: { type: 'jpeg', quality: 0.98 }, --}}
        {{--    html2canvas: { scale: 2 }, --}}
        {{--    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' } --}}
        {{--  }; --}}
        {{--  html2pdf().set(opt).from(element).save(); --}}
        {{-- } --}}
    </script>

</body>

</html>
