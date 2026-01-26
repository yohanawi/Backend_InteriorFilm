<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Contacting Us</title>
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
            padding: 30px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
        }

        .content {
            background-color: #f9f9f9;
            padding: 40px 30px;
            border: 1px solid #ddd;
        }

        .greeting {
            font-size: 18px;
            color: #1866ae;
            margin-bottom: 20px;
        }

        .message {
            margin-bottom: 20px;
            line-height: 1.8;
        }

        .info-box {
            background-color: #e8f4fd;
            border-left: 4px solid #1866ae;
            padding: 15px;
            margin: 20px 0;
        }

        .footer {
            background-color: #f1f1f1;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-radius: 0 0 5px 5px;
        }

        .contact-info {
            margin-top: 20px;
        }

        .contact-item {
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo">Interior Film</div>
        <p style="margin: 5px 0;">Premium Interior Solutions</p>
    </div>

    <div class="content">
        <div class="greeting">
            Hello {{ $userName }},
        </div>

        <div class="message">
            <p>Thank you for reaching out to Interior Film! We've received your message and appreciate you taking the
                time to contact us.</p>

            <p>Our support team has been notified and will review your inquiry carefully. We typically respond within
                <strong>24 hours</strong> during business days.</p>
        </div>

        <div class="info-box">
            <strong>What happens next?</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Our team will review your message</li>
                <li>We'll prepare a detailed response</li>
                <li>You'll receive our reply via email</li>
            </ul>
        </div>

        <div class="message">
            <p>In the meantime, feel free to explore our website for more information about our products and services.
            </p>
        </div>

        <div class="contact-info">
            <strong>Need immediate assistance?</strong>
            <div class="contact-item">📞 Phone: +971 52 784 4188</div>
            <div class="contact-item">📧 Email: info@xesstrading.com</div>
            <div class="contact-item">🏢 Address: NO 4, 10, 20A Street, Al Qouz Ind., Dubai</div>
        </div>
    </div>

    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} Interior Film - XESS Trading LLC. All rights reserved.</p>
        <p style="margin-top: 10px;">
            <a href="https://interiorfilm.com" style="color: #1866ae; text-decoration: none;">Visit Our Website</a>
        </p>
    </div>
</body>

</html>
