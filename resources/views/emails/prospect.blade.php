<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Arial', sans-serif; color: #333333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e5e7eb; border-radius: 8px; }
        .header { background-color: #1e3a8a; padding: 15px; text-align: center; color: #ffffff; border-top-left-radius: 8px; border-top-right-radius: 8px; }
        .content { padding: 20px; }
        .footer { font-size: 12px; color: #6b7280; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Disty Academy</h2>
        </div>
        <div class="content">
            <p>Yth. Bapak/Ibu <strong>{{ $nama }}</strong>,</p>
            <p><em>{{ $institusi }}</em></p>
            <br>
            <div>
                {!! nl2br(e($body)) !!}
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Disty Academy. All rights reserved.
        </div>
    </div>
</body>
</html>