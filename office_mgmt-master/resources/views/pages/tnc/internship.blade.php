@extends('layouts.app')
@section('title', 'Internship Rules & Terms & Conditions')
@section('content')
    <style>
        h2 {
            margin-top: 28px;
            font-size: 20px;
        }

        p {
            margin: 8px 0;
        }

        ul,
        ol {
            padding-left: 30px;
        }

        .note {
            font-size: 14px;
            color: #555;
        }

        .card {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 16px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }

        .muted {
            color: #555;
        }
    </style>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                Internship Rules & Terms & Conditions
            </div>
            <div class="card-body">

                <h2>1. Internship Timings</h2>
                <ul>
                    <li>Standard office hours: <strong>09:30 AM &ndash; 06:30 PM (Monday &ndash; Saturday)</strong>, unless
                        otherwise assigned.</li>
                    <li>Lunch break: <strong>1:30 PM &ndash; 2:00 PM</strong>.</li>
                    <li>Interns must be punctual and adhere to the schedule provided.</li>
                </ul>

                <h2>2. Attendance &amp; Leave Policy</h2>
                <ul>
                    <li>Attendance is mandatory; interns must mark attendance daily.</li>
                    <li>Prior approval is required for any leave:
                        <ul>
                            <li><strong>1-day leave</strong> → Inform at least 24 hrs before.</li>
                            <li><strong>More than 2 days leave</strong> → Inform at least 3 days in advance.</li>
                        </ul>
                    </li>
                    <li>Uninformed absence may lead to termination of internship.</li>
                </ul>

                <h2>3. Work Ethics &amp; Behaviour</h2>
                <ul>
                    <li>Interns must maintain professional behaviour with colleagues, clients, and management.</li>
                    <li>Misconduct, harassment, or violation of workplace ethics will lead to termination.</li>
                    <li>Interns must respect confidentiality of company data, projects, and client information.</li>
                </ul>

                <h2>4. Use of Company Property</h2>
                <ul>
                    <li>Interns may be provided with laptops, systems, or tools for learning and project work.</li>
                    <li>Misuse or damage (intentional/negligent) will lead to recovery charges and disciplinary action.</li>
                    <li>Company resources (emails, internet, software) should be used only for official purposes.</li>
                </ul>

                <h2>5. Dress Code &amp; Conduct</h2>
                <ul>
                    <li>Interns should maintain decent, neat, and professional attire.</li>
                    <li>Workstations should be kept clean and tidy.</li>
                </ul>

                <h2>6. Performance &amp; Responsibility</h2>
                <ul>
                    <li>Interns are expected to sincerely complete tasks assigned by mentors/supervisors.</li>
                    <li>Performance will be regularly reviewed.</li>
                    <li>Failure to show learning interest or complete work responsibly may affect the continuation or
                        certificate of internship.</li>
                </ul>

                <h2>7. Stipend &amp; Benefits</h2>
                <ul>
                    <li>If applicable, stipend details will be shared individually.</li>
                    <li>Any leave or absence without approval may impact stipend/benefits.</li>
                    <li>No overtime or additional allowances will be provided unless approved.</li>
                </ul>

                <h2>8. IT &amp; Data Security</h2>
                <ul>
                    <li>Unauthorized use of software, tools, or company data is strictly prohibited.</li>
                    <li>Interns must not copy, share, or distribute any company data without written approval.</li>
                    <li><strong>Exit Security:</strong> Interns must surrender all data, credentials, and property before
                        leaving. Unauthorized usage after exit may result in legal action.</li>
                </ul>

                <h2>9. Internship Completion &amp; Exit</h2>
                <ul>
                    <li>Interns must serve the complete internship duration as agreed.</li>
                    <li>Early exit requires written approval and may affect issuance of internship certificate.</li>
                    <li>On completion, all assets, access rights, and data must be returned.</li>
                </ul>

                <h2>10. General Terms</h2>
                <ul>
                    <li>The company reserves the right to amend internship policies anytime.</li>
                    <li>Interns are expected to follow all rules and professional standards.</li>
                    <li>Any violation may lead to suspension, termination, or withholding of certificate/stipend.</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
