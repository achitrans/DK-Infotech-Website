<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Employment Offer Letter</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.5;
            padding: 40px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            border: 1px solid #eee;
            padding: 40px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #3c5a99;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }

        .left-header h2 {
            color: #00b0f0;
            font-size: 2rem;
            margin: 0;
        }

        .left-header h2 a {
            color: #00b0f0;
        }

        .left-header p {
            margin: 2px 0;
            font-size: 15px;
            font-weight: 500;
            color: #000;
        }

        .right-header img {
            width: 220px;
            height: auto;
        }

        h1 {
            color: #00b0f0;
            text-align: center;
            font-size: 24px;
            text-decoration: none;
            margin-bottom: 30px;
        }

        /* Buttons */
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

        .container {
            width: 210mm;
            min-height: 297mm;
            padding: 40px;
            margin: auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        main h1 {
            font-size: 24px;
            color: #43d17a;
            margin-bottom: 30px;
        }

        main p {
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .ceo {
            color: #a042c3;
        }

        .signature {
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #999;
            width: 200px;
        }

        /* Print Styles */
        @media print {
            body {
                background: none;
                padding: 0;
            }

            .controls {
                display: none;
            }

            .container {
                box-shadow: none;
                border-radius: 0;
            }
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .details-table td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 15px;
        }

        .logo-cell {
            width: 150px;
            text-align: center;
            vertical-align: middle;
        }

        .logo-cell img {
            max-width: 120px;
            height: auto;
        }
    </style>
</head>

<body>
    <!-- Buttons -->
    <div class="controls">
        <button onclick="printPage()">🖨️ Print</button>
    </div>

    <div class="container" id="letter">
        <header>
            <div class="left-header">
                <h2><a href="{{ env('COMPANY_NAME') }}">{{ env('COMPANY_NAME') }}</a></h2>
                <p>{!! nl2br(env('COMPANY_ADDRESS')) !!} ,
                    Mob:- <a href="tel:+{{ env('COMPANY_PHONE') }}">{{ env('COMPANY_PHONE') }}</a> ,
                    <a href="{{ env('COMPANY_WEBSITE') }}">{{ env('COMPANY_WEBSITE') }}</a> ,
                    <a href="mailto:{{ env('COMPANY_EMAIL') }}">{{ env('COMPANY_EMAIL') }}</a>
                </p>
            </div>
            <div class="right-header">
                <img src="{{ asset('logo.png') }}" alt="Logo">
            </div>
        </header>
        <main>
            <h1>INTERNSHIP CONFIRMATION LETTER</h1>

            <p>
                <strong>To,</strong><br>
                The Training & Placement Officer<br>
                {{ $confirmLetter->college ?? 'college name' }}
            </p>

            <p>
                <strong>Subject: Internship Confirmation for {{ $confirmLetter->position ?? 'position' }}</strong>
            </p>

            <p>Dear Sir/Madam,</p>

            <p>
                This is to inform you that your existing student
                <strong>{{ $confirmLetter->name ?? '--' }}</strong>,
                D/O <strong>{{ $confirmLetter->father_name ?? '--' }}</strong>,
                Roll No. <strong>{{ $confirmLetter->roll_no ?? '--' }}</strong>,
                currently pursuing
                <strong>{{ $confirmLetter->course ?? '--' }}</strong>,
                has taken Enrolled in
                our organization For An Internship Program. The internship will scheduled to
                commence from
                <strong>{{ $confirmLetter->date_of_joining ?? '--' }}</strong>.
            </p>

            <p>
                Our industrial training programs are designed for students who are looking to master
                their skills. {{ env('COMPANY_NAME') }} training gives students hands-on experience.
                {{ env('COMPANY_NAME') }} project-based training/internship program and guidance is
                the preferred choice of Engineering/Marketing Students (IT, BCA, BBA, MCA, MBA,
                Science Students & Professionals) as it gives students hands-on experience. Our projectbased
                training/internship programs are exhaustive and cover the latest and upcoming
                technologies.

            </p>

            <p>
                We offer training in a variety of domains, including Information Security, Application
                Programming (such as Android, Java, PHP, Node.js, React.js, Next.js, MERN Stack),
                Sales, Digital Marketing, Finance, Networking, Embedded Systems, Robotics, and
                related fields.
            </p>

            <p>
                Our mission is to deliver up-to-date, industry-relevant education that enhances individual
                capabilities and contributes to improved organizational productivity through a knowledge-driven
                approach.
            </p>

            <p>
                Kindly extend your support and cooperation.
            </p>

            <p>
                Thank You
            </p>

            <div style="display:flex;justify-content:space-between;margin-top:60px;">
                <div>
                    <p>Yours sincerely,</p>

                    <p>
                        <strong class="ceo">
                            {{ env('COMPANY_AUTHORISED_PERSON') }}
                        </strong><br>
                        Director
                    </p>

                    <img src="{{ asset('images/auth_sign.png') }}" alt="Authorised Sign">

                    <div class="signature">
                        Signature
                    </div>
                </div>

                <div>
                    <p>Agreed & Accepted</p>

                    <br><br>

                    <strong>Student Signature</strong>

                    <br>

                    ______________________
                </div>
            </div>
        </main>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function printPage() {
            window.print();
        }
    </script>
</body>

</html>
