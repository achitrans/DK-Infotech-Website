@extends('layouts.app')
@section('title', 'Office Rules & Terms & Conditions')
@section('content')
<style>
    h2 { margin-top: 28px; font-size: 20px; }
    p { margin: 8px 0; }
    ul, ol { padding-left: 30px; }
    .note { font-size: 14px; color: #555; }
    
    .card { background: #fff; border: 1px solid #eee; border-radius: 8px; padding: 16px; box-shadow: 0 1px 2px rgba(0,0,0,0.03); }
    .muted { color: #555; }
</style>
<div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                Office Rules &amp; Terms &amp; Conditions
            </div>
            <div class="card-body">

                <h2>1. Office Timings</h2>
                <ul>
                    <li>Standard office hours: <strong>09:30 AM &ndash; 06:30 PM</strong> (Monday &ndash; Saturday).</li>
                    <li>Lunch break: <strong>1:30 PM &ndash; 2:00 PM</strong>.</li>
                    <li>Employees must adhere to the attendance.</li>
                    <li>Employees must wear office ID card.</li>
                </ul>

                <h2>2. Attendance &amp; Leave Policy</h2>
                <ul>
                    <li>All employees must mark attendance daily.</li>
                    <li>Late arrivals beyond 15 minutes more than 3 times a month will be considered half-day leave.</li>
                    <li>For leave:
                        <ul>
                            <li><strong>1&ndash;2 days</strong> leave &rarr; Must be informed at least 24 hrs before.</li>
                            <li><strong>More than 2 days</strong> leave &rarr; Must be informed at least 5 days in advance.
                            </li>
                        </ul>
                    </li>
                    <li>Uninformed absence will result in a deduction of <strong>3 days&rsquo; salary</strong> per absent
                        day.</li>
                </ul>

                <h2>3. Work Ethics &amp; Behaviour</h2>
                <ul>
                    <li>Employees must maintain a professional attitude with colleagues, clients, and management.</li>
                    <li>Any kind of misbehaviour, harassment, discrimination, or misconduct will lead to strict disciplinary
                        action including termination.</li>
                    <li>Confidentiality of company data, projects, and client information must be maintained at all times.
                    </li>
                </ul>

                <h2>4. Office Property &amp; Assets</h2>
                <ul>
                    <li>Employees are responsible for company assets provided (laptops, desktops, software licenses, etc.).
                    </li>
                    <li>Damaging or misusing office property intentionally will result in recovery charges and disciplinary
                        action.</li>
                    <li>Company emails, software, and internet must be used strictly for work purposes.</li>
                </ul>

                <h2>5. Dress Code &amp; Cleanliness</h2>
                <ul>
                    <li>Employees should maintain decent and professional attire.</li>
                    <li>Eating at workstations is discouraged to maintain cleanliness.</li>
                    <li>Each employee is responsible for keeping their workspace tidy.</li>
                </ul>

                <h2>6. Performance &amp; Responsibility</h2>
                <ul>
                    <li>Employees must complete assigned work within deadlines.</li>
                    <li>Regular performance reviews will be conducted.</li>
                    <li>Failure to meet performance standards without valid reasons may affect continuation of employment.
                    </li>
                </ul>

                <h2>7. Salary &amp; Benefits</h2>
                <ul>
                    <li>Salary will be credited on or before 12th of every month.</li>
                    <li>Any unauthorized absence will result in salary deductions as per company policy.</li>
                    <li>Overtime will be compensated only if pre-approved by management.</li>
                </ul>

                <h2>8. IT &amp; Data Security</h2>
                <ul>
                    <li>Usage of unauthorized software or applications is strictly prohibited.</li>
                    <li>Employees must not copy, share, or transfer company data, credentials without management approval.
                    </li>
                    <li>Violation of data security will lead to termination and legal action.</li>
                    <li><strong>Exit Security:</strong> Employees are required to return all company data, credentials, and
                        access rights before leaving the organization. Any unauthorized use or sharing of such information
                        after departure will result in legal action, and the individual will be held liable for any losses
                        incurred by the company.</li>
                </ul>

                <h2>9. Notice Period &amp; Exit Policy</h2>
                <ul>
                    <li>Minimum 30 days&rsquo; written notice is required for resignation.</li>
                    <li>Failure to serve notice period may result in salary deduction or withholding of relieving letter.
                    </li>
                    <li>All company property, data, credentials, and access rights must be returned before the final
                        settlement.</li>
                </ul>

                <h2>10. General Terms &amp; Conditions</h2>
                <ul>
                    <li>Management reserves the right to modify office rules and policies at any time.</li>
                    <li>Employees are expected to comply with all updated policies.</li>
                    <li>Any violation of these rules may result in penalties, salary deductions, suspension, or termination,
                        legal action depending on severity.</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
