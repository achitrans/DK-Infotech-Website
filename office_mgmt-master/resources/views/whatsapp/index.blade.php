@extends('layouts.app')
@section('title', 'Whatsapp Session')
@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"> Whatsapp Session
                    @if(isset($status) && $status === 'Connected')
                    <a href="{{ route('whatsapp.logout') }}" class="btn btn-danger btn-sm float-right">Logout Whatsapp</a>
                    @endif
                </div>
                <div class="card-body">

                    @if(isset($message))
                        <div class="alert alert-info">{{ $message }}</div>
                    @endif
                    @if(isset($error))
                        <div class="alert alert-danger">{{ $error }}</div>
                    @endif

                    @if(isset($data) && is_string($data))
                        <div class="alert alert-success">Whatsapp No: {{ $data }}</div>
                    @endif

                    @if(isset($qr) && $qr)
                        <div class="text-center mb-3">
                            <img src="{{ $qr }}" alt="QR Code" class="img-fluid" style="max-width: 300px;">
                            <p class="mt-2">Scan this QR code with your Whatsapp app to login. <br>
                            <span class="text-danger">  This Qr will expire at {{ $expiry }}</span>
                            </p>
                        </div>
                    @endif
                    @if(isset($sessionData) && $sessionData)
                        <div class="mb-3">
                            <h5>Session Data</h5>
                            <pre class="bg-light p-2 border rounded">{{ json_encode($sessionData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if(isset($status) && $status === 'Connected')
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Send Whatsapp Message </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('whatsapp.send-message') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="phone">Phone Number (use comma for multiple numbers)</label>
                            <input type="text" name="phone" class="form-control" placeholder="Enter indian phone number(s) without country code" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea name="message" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="phone">Image (JPG/PNG, max:1MB)</label>
                            <input type="file" name="image" class="form-control" placeholder="JPG/PNG image, Max 1MB">
                        </div>
                        <div class="form-group mt-2">
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

@section('scripts')

<script>
    function updateWhatsappSession() {
        fetch("{{ route('whatsapp.session-status') }}")
            .then(response => response.json())
            .then(data => {
                // Update message, error, qr, sessionData
                console.log(data);
                if (data.data.message == 'Connected') {
                    window.location.reload();
                }
            });
    }
    @if(isset($status) && $status === 'Connected')
    //
    @else
        setInterval(updateWhatsappSession, 7000);
    @endif

</script>
@endsection
