<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internship Certificate</title>

    <!-- html2canvas -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <!-- jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #eaeaea;
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 20px;

        }

        .controls {
            text-align: right;
            margin-bottom: 20px;
        }

        .controls button {
            padding: 10px 15px;
            font-size: 14px;
            margin-left: 10px;
            background-color: #4caf50;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .controls button:hover {
            background-color: #45a049;
        }

        /* A4 Landscape Size */
        #certificate {
            width: 1123px;
            /* height: 794px; */
            background: white;
            border: 15px solid #102c91;
            position: relative;
            padding: 40px;
        }

        /* Inner Border */
        #certificate::before {
            content: "";
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            bottom: 15px;
            border: 3px solid #102c91;
        }

        .content {
            position: relative;
            z-index: 2;
            height: 100%;
        }

        /* Logo */
        .logo {
            text-align: center;
            color: #102c91;
            font-size: 42px;
            font-weight: bold;

        }

        .company {
            text-align: center;
            font-size: 22px;
            color: #333;
            margin-top: 5px;
        }

        /* Title */
        .title {
            text-align: center;
            font-size: 42px;
            color: #102c91;
            font-weight: bold;
            letter-spacing: 4px;
            margin-top: 25px;
        }

        /* Subtitle */
        .subtitle {
            text-align: center;
            font-size: 30px;
            margin-top: 20px;
            color: #111;
        }

        /* Name */
        .name {
            text-align: center;
            font-size: 32px;
            color: #102c91;
            font-weight: bold;
            margin-top: 25px;
            border-top: 3px solid #102c91;
            border-bottom: 3px solid #102c91;
            display: inline-block;
            padding: 15px 50px;
            width: 100%;
        }

        /* Description */
        .text {
            text-align: center;
            font-size: 22px;
            line-height: 1.8;
            margin-top: 30px;
            color: #222;
        }

        .highlight {
            color: #102c91;
            font-weight: bold;
        }

        /* Skills */
        .skills {
            text-align: center;
            margin-top: 5px;
            font-size: 26px;
            color: #111;
        }

        .skill-name {
            margin-top: 10px;
            font-size: 30px;
            color: #102c91;
            font-weight: bold;
        }

        /* Footer */
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
        }

        .signature,
        .date {
            text-align: center;
            color: #111;
            font-size: 22px;
        }

        .line {
            width: 260px;
            border-top: 3px solid #102c91;
            margin-bottom: 10px;
        }

        /* Download Button */
        .btn {
            margin-top: 25px;
            padding: 15px 40px;
            background: #102c91;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 22px;
            cursor: pointer;
        }

        .btn:hover {
            background: #1f4de0;
        }
    </style>
</head>

<body>

    <div id="certificate">

        <div class="content">

            <div class="logo">
                <img src="{{ asset('logo-black.webp') }}" width="190px" alt="Dk infotech Solutions" loading="lazy">
            </div>

            <div style="font-size: 20px;">
                <b>ID:</b> {{ $experienceLetter->user->employee_id ?? '0000' }}


                <p style="float:right; ">
                    <b>Date:</b> {{ $experienceLetter->issue_date ?? '' }}


                </p>
            </div>

            <div class="title">
                CERTIFICATE OF INTERNSHIP
            </div>

            <div class="subtitle">
                This is to certify that
            </div>

            <div style="text-align:center;">
                <div class="name">
                    {{ $experienceLetter->user->name ?? '' }}
                </div>
            </div>

            <div class="text">
                has successfully completed
                <span class="highlight">{{ $experienceLetter->duration ?? '' }}</span>
                on-site internship program in
                <span class="highlight">
                    {{ $experienceLetter->position ?? '' }}
                </span>
                with wonderful remarks at
                <span class="highlight">DK Infotech Solutions</span>
                from
                <span class="highlight">{{ $experienceLetter->start_date ?? '' }}</span>
                to
                <span class="highlight">{{ $experienceLetter->end_date ?? '' }}</span>.
            </div>

            <div class="skills">
                Skills Learned
                <div class="skill-name">
                    {{ $experienceLetter->skill ?? '' }}
                </div>
            </div>

            <div class="footer">
                <div class="signature">
                    <div class="line"></div>
                    Auth Signature
                </div>
                <div>
                    <img src="{{ asset('images/mca.png') }}" width="100" alt="PCI Image" loading="lazy">
                    <img src="{{ asset('images/ISO.png') }}" width="100" alt="iso image" loading="lazy">
                    <img src="{{ asset('images/PCI.png') }}" width="130" alt="PCI Image" loading="lazy">
                </div>
                <div class="date">
                    <div class="line"></div>
                    Trainer Signature
                </div>
            </div>

            {!! QrCode::size(40)->generate(route('barcode.show', $experienceLetter->user->employee_id)) !!}

        </div>
    </div>

    <!-- Download Button -->
    <button class="btn" onclick="downloadPDF()">
        Download PDF
    </button>

    <script>
        async function downloadPDF() {

            const certificate =
                document.getElementById("certificate");

            const canvas =
                await html2canvas(certificate, {
                    scale: 2
                });

            const imgData =
                canvas.toDataURL("image/png");

            const {
                jsPDF
            } = window.jspdf;

            // A4 Landscape
            const pdf =
                new jsPDF('landscape', 'mm', 'a4');

            const pdfWidth =
                pdf.internal.pageSize.getWidth();

            const pdfHeight =
                pdf.internal.pageSize.getHeight();

            pdf.addImage(
                imgData,
                'PNG',
                0,
                0,
                pdfWidth,
                pdfHeight
            );

            pdf.save("Internship_Certificate.pdf");
        }
    </script>

</body>

</html>
