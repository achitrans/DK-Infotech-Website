<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root{
            --success:#22c55e;
            --danger:#ef4444;
        }

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background:linear-gradient(
                135deg,
                #0f172a,
                #1e293b,
                #312e81,
                #1e1b4b
            );
            background-size:400% 400%;
            animation:bgMove 12s ease infinite;
            overflow:hidden;
            padding:20px;
        }

        @keyframes bgMove{
            0%{background-position:0% 50%}
            50%{background-position:100% 50%}
            100%{background-position:0% 50%}
        }

        /* Floating Orbs */
        .orb{
            position:absolute;
            border-radius:50%;
            filter:blur(50px);
            opacity:.35;
        }

        .orb1{
            width:250px;
            height:250px;
            background:#22c55e;
            top:10%;
            left:10%;
            animation:float 8s infinite ease-in-out;
        }

        .orb2{
            width:300px;
            height:300px;
            background:#6366f1;
            bottom:10%;
            right:10%;
            animation:float 10s infinite ease-in-out;
        }

        @keyframes float{
            0%,100%{transform:translateY(0px)}
            50%{transform:translateY(-30px)}
        }

        .payment-card{
            width:100%;
            max-width:580px;
            background:rgba(255,255,255,.08);
            backdrop-filter:blur(18px);
            -webkit-backdrop-filter:blur(18px);
            border:1px solid rgba(255,255,255,.15);
            border-radius:30px;
            color:white;
            overflow:hidden;
            box-shadow:
                0 20px 50px rgba(0,0,0,.35),
                inset 0 1px 0 rgba(255,255,255,.2);
            animation:cardIn .8s ease;
        }

        @keyframes cardIn{
            from{
                opacity:0;
                transform:translateY(40px) scale(.95);
            }
            to{
                opacity:1;
                transform:translateY(0) scale(1);
            }
        }

        .card-header-custom{
            text-align:center;
            padding:40px 30px 20px;
        }

        .icon-wrapper{
            width:120px;
            height:120px;
            margin:auto;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            position:relative;
        }

        .success .icon-wrapper{
            background:rgba(34,197,94,.15);
            border:2px solid rgba(34,197,94,.5);
        }

        .failed .icon-wrapper{
            background:rgba(239,68,68,.15);
            border:2px solid rgba(239,68,68,.5);
        }

        .icon-wrapper::before{
            content:"";
            position:absolute;
            inset:-12px;
            border-radius:50%;
            animation:pulse 2s infinite;
        }

        .success .icon-wrapper::before{
            border:2px solid rgba(34,197,94,.4);
        }

        .failed .icon-wrapper::before{
            border:2px solid rgba(239,68,68,.4);
        }

        @keyframes pulse{
            0%{
                transform:scale(1);
                opacity:1;
            }
            100%{
                transform:scale(1.3);
                opacity:0;
            }
        }

        .status-icon{
            font-size:60px;
        }

        .success .status-icon{
            color:var(--success);
        }

        .failed .status-icon{
            color:var(--danger);
        }

        .status-title{
            font-size:2rem;
            font-weight:700;
            margin-top:25px;
        }

        .success .status-title{
            color:var(--success);
        }

        .failed .status-title{
            color:var(--danger);
        }

        .status-subtitle{
            color:#cbd5e1;
        }

        .info-box{
            margin:25px;
            padding:20px;
            border-radius:18px;
            background:rgba(255,255,255,.05);
            border:1px solid rgba(255,255,255,.08);
        }

        .info-row{
            display:flex;
            justify-content:space-between;
            margin-bottom:14px;
        }

        .info-row:last-child{
            margin-bottom:0;
        }

        .info-label{
            color:#94a3b8;
        }

        .info-value{
            font-weight:600;
        }

        .btn-premium{
            border:none;
            border-radius:14px;
            padding:14px;
            font-weight:600;
            transition:.3s;
        }

        .btn-premium:hover{
            transform:translateY(-3px);
        }

        .btn-success-premium{
            background:linear-gradient(135deg,#22c55e,#16a34a);
            color:white;
        }

        .btn-danger-premium{
            background:linear-gradient(135deg,#ef4444,#dc2626);
            color:white;
        }

        .btn-outline-light{
            border-radius:14px;
        }

        .footer-note{
            color:#94a3b8;
            font-size:13px;
            text-align:center;
            margin-top:20px;
        }

        .badge-custom{
            padding:8px 14px;
            border-radius:50px;
            font-size:12px;
        }
    </style>
</head>
<body>

<div class="orb orb1"></div>
<div class="orb orb2"></div>

<div class="payment-card @if($status=='success') success @else failed @endif">

    <div class="card-header-custom">

        <div class="icon-wrapper">
            <i class="fa-solid @if($status=='success') fa-circle-check @else fa-circle-xmark @endif status-icon"></i>
        </div>

        <h2 class="status-title">
            Payment @if($status=='success') Success @else Failed @endif
        </h2>

        <p class="status-subtitle">
            {{ $message }}
        </p>

    </div>

    <div class="info-box">

        <div class="info-row">
            <span class="info-label">Transaction ID</span>
            <span class="info-value">{{ $data['txnid'] ?? 'na' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Purpose</span>
            <span class="info-value">{{ $data['productinfo'] ?? 'na' }}</span>
        </div>

        <div class="info-row">
            <span class="info-label">Amount</span>
            <span class="info-value">₹{{ $data['amount'] ?? 'na' }}</span>
        </div>

        <div class="info-row">
            <span class="info-label">Date</span>
            <span class="info-value">{{ date('d M Y') }}</span>
        </div>

        <div class="info-row">
            <span class="info-label">Status</span>
            <span class="badge @if($status=='success') bg-success @else bg-danger @endif badge-custom">
                {{ $status }}
            </span>
        </div>

    </div>

    <div class="px-4 pb-4 d-grid gap-3">
        <button class="btn btn-outline-light">
            Back To Home
        </button>
    </div>

    <div class="footer-note pb-4">
        Secured by SSL Encryption
    </div>

</div>


</body>
</html>
