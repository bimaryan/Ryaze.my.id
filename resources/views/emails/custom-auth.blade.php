<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{ $title }}</title>
</head>
<body style="background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; font-size: 16px; line-height: 1.5; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">

    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; width: 100%; background-color: #f1f5f9;">
        <tr>
            <td style="padding: 40px 20px;">

                <!-- Container -->
                <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; max-width: 560px; width: 100%; margin: 0 auto;">

                    <!-- Header -->
                    <tr>
                        <td style="background-color: #4f46e5; border-radius: 12px 12px 0 0; padding: 28px 32px; text-align: center;">
                            <h1 style="color: #ffffff; font-size: 22px; font-weight: 700; margin: 0; letter-spacing: -0.3px;">Ryaze Portal</h1>
                            <p style="color: #c7d2fe; font-size: 13px; margin: 6px 0 0 0; font-weight: 400;">ryaze.my.id</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="background-color: #ffffff; padding: 36px 32px;">

                            <p style="color: #1e293b; font-size: 15px; margin: 0 0 16px 0;">Halo,</p>
                            <p style="color: #475569; font-size: 15px; margin: 0 0 28px 0; line-height: 1.6;">{{ $intro }}</p>

                            <!-- Button -->
                            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; width: 100%; margin-bottom: 28px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $actionUrl }}" target="_blank"
                                           style="background-color: #4f46e5; border-radius: 8px; color: #ffffff; display: inline-block; font-size: 15px; font-weight: 600; padding: 13px 28px; text-decoration: none; letter-spacing: 0.1px;">
                                            {{ $actionText }}
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Fallback link -->
                            <p style="color: #94a3b8; font-size: 13px; margin: 0 0 8px 0;">Jika tombol di atas tidak berfungsi, salin dan tempel tautan berikut ke browser Anda:</p>
                            <p style="margin: 0 0 24px 0; word-break: break-all;">
                                <a href="{{ $actionUrl }}" style="color: #4f46e5; font-size: 12px; text-decoration: none;">{{ $actionUrl }}</a>
                            </p>

                            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 0 0 24px 0;">

                            <p style="color: #94a3b8; font-size: 13px; margin: 0; line-height: 1.6;">{{ $outro }}</p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; border-radius: 0 0 12px 12px; border-top: 1px solid #e2e8f0; padding: 20px 32px; text-align: center;">
                            <p style="color: #94a3b8; font-size: 12px; margin: 0;">
                                &copy; {{ date('Y') }} Ryaze.my.id &mdash; Platform Developer Indonesia<br>
                                Email ini dikirim secara otomatis, mohon tidak membalas email ini.
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- /Container -->

            </td>
        </tr>
    </table>

</body>
</html>
