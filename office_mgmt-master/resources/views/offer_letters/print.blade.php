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
            border: 1px solid #eee;
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
            text-decoration: none;
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

        main h1 {
            font-size: 24px;
            color: #43d17a;
            margin-bottom: 30px;
            text-align: center;
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
            margin-top: 40px;
            padding-top: 10px;
            border-top: 1px solid #999;
            width: 200px;
        }

        .signature-row {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            gap: 30px;
        }

        .signature-block {
            flex: 1;
            min-width: 0;
        }

        .signature-block.right {
            text-align: right;
        }

        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #999;
            display: inline-block;
            width: 240px;
            padding-top: 8px;
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
    </style>
</head>

<body>
    @php
        $career = $offerLetter->career;
        $candidateName = $career?->name ?? '-';
        $position = $offerLetter->position;
        $interviewBy = $offerLetter->interview_by_name ?: $offerLetter->interviewer?->name ?? '-';
        $photoUrl = null;
        if ($career && $career->photo) {
            $photoUrl = \Illuminate\Support\Facades\Storage::url($career->photo);
        }
    @endphp

    <div class="controls">
        <button onclick="window.print()">Print</button>
    </div>

    <div class="container" id="letter">
        <header>
            <div class="left-header">
                <h2><a href="{{ env('COMPANY_WEBSITE') }}">{{ env('COMPANY_NAME') }}</a></h2>
                <p>
                    {!! nl2br(config('COMPANY_ADDRESS')) !!} ,
                    Mob:- <a href="tel:+{{ env('COMPANY_PHONE') }}">{{ env('COMPANY_PHONE') }}</a> ,
                    <a href="{{ env('COMPANY_WEBSITE') }}">{{ env('COMPANY_WEBSITE') }}</a> ,
                    <a href="mailto:{{ env('COMPANY_EMAIL') }}">{{ env('COMPANY_EMAIL') }}</a>
                </p>
            </div>
            <div class="right-header">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
            </div>
        </header>

        <main>
            <h1>EMPLOYMENT OFFER LETTER</h1>

            <p>Dear {{ $candidateName }},</p>

            <p>
                We’re delighted to inform you that based on your skills and merit, you have been selected for the
                position of
                <strong>{{ $position }}</strong> in <strong>{{ env('COMPANY_NAME') }}</strong>.
            </p>

            <p>
                This position will be a full-time. You will be required to work 48 hours/week from
                Monday to Saturday. Your CTC is Rs:- {{ number_format((float) $offerLetter->ctc, 2) }} per annum.
                {{ env('COMPANY_NAME') }} also offers every employee multiple benefits packages to choose from,
                details of which have been attached with this letter. You will also be asked to sign an official
                contract and an NDA during your onboarding process
            </p>

            <table class="details-table">
                <tr>
                    <td rowspan="7" class="logo-cell">
                        @if ($photoUrl)
                            <img src="{{ $photoUrl }}" alt="{{ $candidateName }} Photo">
                        @else
                            <div style="font-size: 12px;">No Photo</div>
                        @endif
                    </td>
                    <td><strong>Interview ID</strong></td>
                    <td>{{ $career?->interview_id ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Candidate Name</strong></td>
                    <td>{{ $candidateName }}</td>
                </tr>
                <tr>
                    <td><strong>Date of Interview</strong></td>
                    <td>{{ $career?->interview_date ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Interview By</strong></td>
                    <td>{{ $interviewBy }}</td>
                </tr>
                <tr>
                    <td><strong>Offered Salary (CTC)</strong></td>
                    <td>₹{{ number_format((float) $offerLetter->ctc, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Salary</strong></td>
                    <td>{{ $offerLetter->salary !== null ? '₹' . number_format((float) $offerLetter->salary, 2) : '-' }}
                    </td>
                </tr>
                @if ($offerLetter->stipend !== null)
                    <tr>
                        <td><strong>Stipend</strong></td>
                        <td>{{ $offerLetter->stipend }}</td>
                    </tr>
                @endif

                <tr>
                    <td colspan="2"><strong>Date of Joining</strong></td>
                    <td>{{ $offerLetter->date_of_joining?->format('Y-m-d') ?? '-' }}</td>
                </tr>
            </table>

            <p>
                During your employment, you will be governed by the rules, regulations, and policies of the
                company. You will also be required to maintain strict confidentiality of all company and
                client information, both during and after your employment.
            </p>
            <p>
                Your employment may be terminated by either party by giving 10 days written notice or
                salary in lieu of notice, as per company policy.
            </p>
            <p>
                As a part of the employment process, you will be undergoing a training for a period of 15 days
                from the date of your joining. Post that you will have to clear the certification. In case you are
                not able to pass/clear the desired certification, your employment with the Company shall cease
                to continue with effect from that date.
            </p>

            <p>
                As a part of {{ env('COMPANY_NAME') }} e-joining process, you will be required to share scanned
                copies of a list of documents on the Company portal. The link for uploading the documents
                shall be sent on your e-mail id / contact number registered with us.
            </p>

            <p>
                You are expected to complete e-documentation formalities before you join on
                {{ $offerLetter->date_of_joining?->format('Y-m-d') ?? '-' }}.
                Please carry all your original documents (identity proof documents, educational and
                experience documents) at the time of joining for verification.
            </p>

            <p>
                Please note that the date of joining mentioned above may vary due to exigencies beyond our
                control, including but not limited to, successful upload and verification of your documents
                and delay in completing the hiring of requisite batch strength.
            </p>

            <p>
                As part of our standard policy, we conduct a background verification for all our employees.
                In case of any falsification at documents or misrepresentation during the interview or data
                submission, we reserve the right to withhold the salary and terminate the employment without
                any notice.
            </p>
            <p>
                You are requested to kindly go through the offer letter carefully and join us within 3 working
                days of the joining date. On failure to do so, this letter will be automatically considered as null
                and void.
            </p>

            <p>
                We congratulate you and wish you a long and successful career with the Company. We assure
                you of our support for your professional development and growth.
            </p>

            <div class="signature-row">
                <div class="signature-block left">
                    <p>Yours truly,</p>
                    <img src="{{ asset('images/auth_sign.png') }}" alt="Authorised Sign">
                    <p><strong class="ceo">{{ env('COMPANY_AUTHORISED_PERSON') }}</strong><br>CEO</p>
                </div>

                <div class="signature-block " style="padding-left: 90px">
                    <p> Agreed and Accepted </p>
                    <div class="signature-line"></div>
                    <p style="margin-top: 1px; ">{{ $candidateName }}
                        <br> Date: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                    </p>
                </div>
            </div>


        </main>
    </div>
</body>

</html>
