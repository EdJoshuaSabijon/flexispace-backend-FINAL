# Email Verification Setup Guide

## Current Status
✅ Email verification logic is implemented
✅ Verification emails will be sent with a clickable button
✅ Frontend handles verification status from email links

## Configure Email Settings

You need to configure your Laravel backend to send emails. Here are your options:

### Option 1: Gmail SMTP (Recommended for Development)

1. Enable 2-Factor Authentication on your Gmail account
2. Generate an App Password:
   - Go to Google Account > Security
   - Enable 2-Step Verification
   - Go to App passwords
   - Create a new app password (select "Mail" and "Other")
   - Copy the generated password

3. Update your `.env` file in the backend:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_gmail@gmail.com
MAIL_PASSWORD=your_app_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_gmail@gmail.com
MAIL_FROM_NAME="FlexiSpace"
```

### Option 2: Mailtrap (Recommended for Testing)

1. Sign up at https://mailtrap.io
2. Create a new inbox
3. Copy the SMTP credentials

4. Update your `.env` file:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@flexispace.com
MAIL_FROM_NAME="FlexiSpace"
```

### Option 3: Production Email Services

For production, consider using:
- SendGrid
- Amazon SES
- Mailgun
- Postmark

## How Email Verification Works

1. **Registration**: When a user registers, the system sends a verification email
2. **Email Content**: The email contains a "Verify Email Address" button
3. **Verification**: Clicking the button redirects to the backend verification route
4. **Confirmation**: Backend marks email as verified and redirects to login page
5. **Login**: User can now log in with verified email

## Testing the Verification Flow

1. Configure your mail settings in `.env`
2. Clear Laravel config cache:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

3. Register a new user account
4. Check your email (or Mailtrap inbox)
5. Click the "Verify Email Address" button
6. You should be redirected to the login page with a success message

## Troubleshooting

### Emails not sending?
- Check Laravel logs: `storage/logs/laravel.log`
- Verify mail configuration in `.env`
- Run `php artisan config:clear` after changing `.env`

### Verification link not working?
- Ensure `APP_URL` in `.env` points to your backend URL
- Check that the verification route in `routes/web.php` is accessible
- Verify the signed URL is being generated correctly

### For development with log driver:
If you want to test without sending real emails, set:
```env
MAIL_MAILER=log
```
Emails will be logged to `storage/logs/laravel.log` instead of being sent.
