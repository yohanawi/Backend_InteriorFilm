<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Message</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #1866ae;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
        }

        .field {
            margin-bottom: 20px;
        }

        .label {
            font-weight: bold;
            color: #1866ae;
            display: block;
            margin-bottom: 5px;
        }

        .value {
            background-color: white;
            padding: 10px;
            border-left: 3px solid #1866ae;
        }

        .message-box {
            background-color: white;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            min-height: 100px;
        }

        .footer {
            background-color: #f1f1f1;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-radius: 0 0 5px 5px;
        }

        .btn {
            display: inline-block;
            background-color: #1866ae;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>New Contact Message</h1>
    </div>

    <div class="content">
        <p>You have received a new contact form submission from Interior Film website.</p>

        <div class="field">
            <span class="label">Name:</span>
            <div class="value">{{ $contactMessage->name }}</div>
        </div>

        <div class="field">
            <span class="label">Email:</span>
            <div class="value">
                <a href="mailto:{{ $contactMessage->email }}">{{ $contactMessage->email }}</a>
            </div>
        </div>

        <div class="field">
            <span class="label">Phone:</span>
            <div class="value">
                <a href="tel:{{ $contactMessage->phone }}">{{ $contactMessage->phone }}</a>
            </div>
        </div>

        <div class="field">
            <span class="label">Message:</span>
            <div class="message-box">
                {{ $contactMessage->message }}
            </div>
        </div>

        <div class="field">
            <span class="label">IP Address:</span>
            <div class="value">{{ $contactMessage->ip_address }}</div>
        </div>

        <div class="field">
            <span class="label">Submitted:</span>
            <div class="value">{{ $contactMessage->created_at->format('F d, Y - h:i A') }}</div>
        </div>

        <center>
            <a href="{{ url('/contacts/' . $contactMessage->id) }}" class="btn">View in Admin Panel</a>
        </center>
    </div>

    <div class="footer">
        <p>This is an automated notification from Interior Film Contact Form.</p>
        <p>&copy; {{ date('Y') }} Interior Film. All rights reserved.</p>
    </div>
</body>

</html>
