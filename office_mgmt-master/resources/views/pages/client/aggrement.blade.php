<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Software Development & Maintenance Agreement</title>
  <style>
    body {
      font-family: "Segoe UI", Tahoma, sans-serif;
      line-height: 1.6;
      margin: 40px;
      background: #f9f9f9;
      color: #333;
    }
    .container {
      max-width: 950px;
      background: #fff;
      padding: 40px;
      margin: auto;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    h1, h2 {
      color: #0d47a1;
    }
    h1 {
      text-align: center;
      font-size: 28px;
      margin-bottom: 10px;
    }
    h2 {
      font-size: 20px;
      margin-top: 30px;
      border-bottom: 2px solid #0d47a1;
      padding-bottom: 5px;
    }
    p {
      margin: 10px 0;
      text-align: justify;
    }
    ul {
      margin: 10px 0 10px 25px;
    }
    .signature-section {
      margin-top: 50px;
      display: flex;
      justify-content: space-between;
    }
    .signature-box {
      width: 45%;
      text-align: center;
    }
    .signature-line {
      margin-top: 60px;
      border-top: 1px solid #333;
      padding-top: 5px;
    }
    .date {
      text-align: right;
      font-style: italic;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>SOFTWARE DEVELOPMENT AND MAINTENANCE SERVICE AGREEMENT</h1>
    <p class="date">Date: {{ auth()->user()->created_at->toDateString()}}</p>

    <p>This Software Development and Maintenance Service Agreement (the “Agreement”) is entered into on {{ auth()->user()->created_at->toDateString()}}, by and between:</p>

    <p><strong>{{ env('COMPANY_NAME')}}</strong>, a company incorporated under the laws of Patna Bihar, having its principal place of business at {{ env('COMPANY_ADDRESS')}}, @if(strlen(env('COMPANY_NAME_OLD'))>1) (formerly known as {{ env('COMPANY_NAME_OLD')}}) @endif, defined as “Service Provider”,</p>

    <p>AND</p>

    <p><strong>{{ auth()->user()->kycClient?->business_address ?? auth()->user()->name }}</strong>, located at <strong>{{ auth()->user()->kycClient?->business_address ?? '________'}}</strong>, defined as ("Client").</p>

    <p>Together referred to as the Parties.</p>

    <h2>1. DEFINITIONS</h2>
    <p>1.1 Services shall mean the software development, maintenance, support, consulting, and/or other IT services to be provided by Service Provider to the Client as described in Schedule A.</p>
    <p>1.2 “Software” means any application, system, or platform developed or maintained under this Agreement.</p>
    <p>1.3 NOW, THEREFORE, in consideration of the mutual covenants contained herein, the parties agree as follows:</p>

    <h2>2. SCOPE OF SERVICES</h2>
    <p>1. Service Provider agrees to provide the Client with the Services outlined in Schedule A of this Agreement. Any modifications to the scope of Services must be agreed upon in writing by both Parties.</p>
    <p>2. The Service Provider agrees to develop and maintain the software (the “Software”) according to the specifications set on mutual understanding. The Service Provider shall provide the services in a professional and workmanlike manner.</p>

    <h2>3. TERM AND TERMINATION</h2>
    <p>3.1 This Agreement shall commence on the Effective Date and shall remain in effect for a period of [One Year], unless terminated earlier in accordance with this Agreement.</p>
    <p>3.2 Either Party may terminate this Agreement upon [30] days' prior written notice to the other Party.</p>
    <p>3.3 Upon termination, Service Provider shall deliver to the Client any work in progress or completed deliverables, and the Client shall pay for all Services rendered up to the date of termination.</p>

    <h2>4. FEES AND PAYMENT</h2>
    <ul>
      <li>The Client agrees to pay Service Provider the fees set forth in Schedule B.</li>
      <li>All invoices shall be due and payable within 7 days of receipt by the Client.</li>
      <li>Late payments may incur interest at the rate of 5% per month or the maximum rate permitted by law, whichever is lower.</li>
      <li>Payment shall be made as per the following milestones:
        <ul>
          <li>30% on signing the agreement</li>
          <li>20% upon completion of front-end and back-end modules</li>
          <li>40% after successful integration and testing</li>
          <li>10% on final delivery and deployment</li>
        </ul>
      </li>
    </ul>
    <p>All payments are exclusive of applicable taxes.</p>

    <h2>5. NO REFUND POLICY</h2>
    <p>5.1 The Client acknowledges and agrees that all payments made to Service Provider under this Agreement are non-refundable.</p>
    <p>5.2 This includes, but is not limited to, any advance payments, milestone-based payments, or final project settlement amounts, regardless of:</p>
    <ul>
      <li>Project cancellation by the Client</li>
      <li>Dissatisfaction with the delivered product or services</li>
      <li>Change in business direction or internal decision by the Client</li>
      <li>Any delays caused by the Client's failure to provide timely feedback, approvals, or content</li>
    </ul>
    <p>5.3 Service Provider commits to delivering the project as per the agreed scope and timelines. In the event of any deviation caused by the Service Provider, reasonable efforts will be made to rectify the issue. However, no refund shall be issued.</p>

    <h2>6. INTELLECTUAL PROPERTY</h2>
    <p>All intellectual property developed by Service Provider during the course of providing the Services shall be the property of the Client upon full payment.</p>
    <p>Service Provider may retain the right to use general skills, techniques, and know-how gained during the course of this engagement for other projects and clients.</p>
    <p>Upon final payment, all rights, title, and interest in the Software, including source code if applicable, documentation, and related intellectual property, shall be transferred to the Client.</p>
    <p>Service Provider retains the right to reuse non-client-specific code libraries and frameworks developed independently.</p>

    <h2>7. CONFIDENTIALITY</h2>
    <p>Each Party agrees to maintain the confidentiality of any proprietary or confidential information received during the term of this Agreement. This obligation shall survive for 1 year after the termination of this Agreement.</p>

    <h2>8. DATA SECURITY & COMPLIANCE</h2>
    <p>Service Provider shall develop the software to comply with security standards provided by the client.</p>
    
    <p>The Service Provider shall not store or misuse sensitive data, and any processing shall be done according to best security practices provided by the client.</p>

    <h2>9. WARRANTIES AND DISCLAIMERS</h2>
    <p>Service Provider warrants that it will provide the Services in a professional and workmanlike manner.</p>
    <p>Except as expressly stated in this Agreement, Service Provider disclaims all warranties, express or implied, including but not limited to warranties of merchantability or fitness for a particular purpose.</p>

    <h2>10. LIMITATION OF LIABILITY</h2>
    <p>Service Provider shall not be liable for any indirect, incidental, or consequential damages, including loss of profits or data, arising out of or related to this Agreement, even if advised of the possibility of such damages.</p>

    <h2>11. GOVERNING LAW</h2>
    <p>This Agreement shall be governed by and construed in accordance with the laws of Bihar,India , without regard to its conflict of laws principles.</p>

    <h2>12. GENERAL PROVISIONS</h2>
    <ul>
      <li><strong>Entire Agreement:</strong> This Agreement contains the entire understanding between the Parties and supersedes all prior discussions and agreements.</li>
      <li><strong>Amendment:</strong> This Agreement may only be amended in writing signed by both Parties.</li>
      <li><strong>Severability:</strong> If any provision of this Agreement is found to be invalid, the remaining provisions shall remain in full force.</li>
      <li><strong>Independent Contractors:</strong> The Parties are independent contractors, and nothing in this Agreement shall be construed to create a partnership or joint venture.</li>
    </ul>

    <h2>13. SUPPORT & MAINTENANCE</h2>
    <p>Service Provider shall provide post-deployment support for a period of [3/6/12] months, covering:</p>
    <ul>
      <li>Bug fixes</li>
      <li>Minor updates</li>
      <li>Server monitoring (if included)</li>
    </ul>
    <p>Extended support and service-level agreements (SLAs) shall be negotiated separately.</p>

    <h2>14. LIMITATION OF LIABILITY</h2>
    <p>The Service Provider shall not be liable to the Client for any indirect, special, incidental, or consequential damages, including but not limited to loss of profits, data, business interruption, or other losses arising out of or in connection with this Agreement or the services provided hereunder. The total liability of the Service Provider under this Agreement is zero.</p>

    <h2>15. INDEMNIFICATION</h2>
    <p>Notwithstanding any other provision in this Agreement, {{ env('COMPANY_NAME')}} (the "Service Provider") shall not be obligated to indemnify, defend, or hold harmless the Client or any of its officers, directors, agents, employees, or affiliates from any claims, damages, expenses (including attorneys' fees), or liabilities of any kind, whether direct or indirect, incidental, consequential, or otherwise, arising out of or in connection with this Agreement, the services provided, or the Software developed under this Agreement.</p>
    <p>The Client acknowledges and agrees that the Service Provider shall not be responsible or liable for any losses, damages, costs, or expenses incurred by the Client or any third party related to the Software or services provided hereunder, and the Client hereby waives any and all claims against the Service Provider related to such losses, damages, costs, or expenses.</p>

    <h2>16. DATA AND TRANSACTION</h2>
    <ul>
      <li><strong>Ownership:</strong> The client retains full ownership and control of all data and financial transactions generated through the use of the software.</li>
      <li><strong>Accuracy and Compliance:</strong> The client is responsible for ensuring the accuracy, legality, and compliance of all data and financial transactions generated using the software.</li>
      <li><strong>Compliance with Laws:</strong> The client agrees to comply with all applicable laws, regulations, and industry standards relating to the collection, processing, and use of data and financial transactions generated by the software.</li>
      <li><strong>Confidentiality:</strong> The software provider agrees to treat all data and financial transactions generated by the client using the software as confidential and not disclose them to third parties without prior consent, except as required by law.</li>
    </ul>

    <h2>17. ENTIRE AGREEMENT</h2>
    <p>This Agreement contains the entire agreement between the parties with respect to the subject matter hereof and supersedes all prior agreements, understandings, negotiations, and discussions, whether oral or written.</p>

    <h2>SIGNATURES</h2>
    <div class="signature-section">
      <div class="signature-box">
        <p>For: {{ env('COMPANY_NAME')}}</p>
        <div class="signature-line">Authorized Signatory</div>
      </div>
      <div class="signature-box">
        <p>For: {{ auth()->user()->kycClient?->business_address ?? auth()->user()->name }}</p>
        <div class="signature-line">Authorized Signatory</div>
      </div>
    </div>
  </div>
</body>
</html>
