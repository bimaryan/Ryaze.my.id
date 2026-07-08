<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{ $title }}</title>
</head>
<body style="background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; font-size: 16px; line-height: 1.5; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
    <table border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background-color: #f8fafc;">
        <tr>
            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
            <td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 500px; padding: 20px; width: 500px; margin: 0 auto;">
                <div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 500px; padding: 20px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                    
                    <!-- Header -->
                    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background-color: #4f46e5; border-radius: 16px 16px 0 0;">
                        <tr>
                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; padding: 30px 20px; text-align: center;">
                                <h1 style="color: #ffffff; font-size: 24px; font-weight: bold; margin: 0; text-transform: uppercase;">Ryaze Portal</h1>
                                <p style="color: #c7d2fe; font-size: 14px; margin: 5px 0 0 0;">{{ $title }}</p>
                            </td>
                        </tr>
                    </table>

                    <!-- Body -->
                    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; padding: 30px;">
                        <tr>
                            <td style="font-family: sans-serif; font-size: 15px; vertical-align: top; color: #334155;">
                                <p style="font-family: sans-serif; font-size: 15px; font-weight: normal; margin: 0; margin-bottom: 20px;">Halo,</p>
                                <p style="font-family: sans-serif; font-size: 15px; font-weight: normal; margin: 0; margin-bottom: 25px;">{{ $intro }}</p>
                                
                                <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; box-sizing: border-box;">
                                    <tbody>
                                        <tr>
                                            <td align="center" style="font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 25px;">
                                                <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: auto;">
                                                    <tbody>
                                                        <tr>
                                                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; border-radius: 8px; text-align: center; background-color: #4f46e5;"> <a href="{{ $actionUrl }}" target="_blank" style="border: solid 1px #4f46e5; border-radius: 8px; box-sizing: border-box; cursor: pointer; display: inline-block; font-size: 15px; font-weight: bold; margin: 0; padding: 12px 25px; text-decoration: none; text-transform: capitalize; background-color: #4f46e5; border-color: #4f46e5; color: #ffffff;">{{ $actionText }}</a> </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                                <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 0; color: #64748b;">{{ $outro }}</p>
                            </td>
                        </tr>
                    </table>

                </div>
                
                <!-- Footer -->
                <div class="footer" style="clear: both; margin-top: 20px; text-align: center; width: 100%;">
                    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                        <tr>
                            <td class="content-block" style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; color: #94a3b8; font-size: 12px; text-align: center;">
                                <span class="apple-link" style="color: #94a3b8; font-size: 12px; text-align: center;">&copy; {{ date('Y') }} Ryaze.my.id. Hak Cipta Dilindungi.</span>
                            </td>
                        </tr>
                    </table>
                </div>

            </td>
            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
        </tr>
    </table>
</body>
</html>
