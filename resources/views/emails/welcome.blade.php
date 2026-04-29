<!DOCTYPE html>
<html>
<head>
    <title>Welcome to FlexiSpace</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
            <h1 style="color: white; margin: 0;">Welcome to FlexiSpace!</h1>
        </div>
        <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;">
            <p>Hello {{ $user->first_name }},</p>
            <p>Thank you for joining FlexiSpace! We're excited to have you as part of our community.</p>
            <p>At FlexiSpace, we offer the best modular furniture solutions to help you create the perfect space. Browse our collection of sofas, desks, storage solutions, and more!</p>
            <p>If you have any questions, feel free to reach out to our support team.</p>
            <p style="margin-top: 30px;">Best regards,<br>The FlexiSpace Team</p>
        </div>
    </div>
</body>
</html>
