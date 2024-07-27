<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://samogoza.com/img/samogoza.png" type="image/x-icon">
    <link rel="shortcut icon" href="https://samogoza.com/img/samogoza.png" type="image/x-icon">
    <title>Samogoza Loan Service</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            text-align: center;
            padding: 20px;
            background-color: #002E5B;
            color: white;
        }

        .email-content {
            padding: 20px;
            line-height: 1.6;
            color: #333333;
        }

        .email-footer {
            text-align: center;
            padding: 20px;
            background-color: #f4f4f4;
            color: #777777;
        }

        .email-footer a {
            color: #0073e6;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <img src="https://samogoza.com/img/samogoza.png" alt="Logo" style="max-width: 150px;">
        </div>
        <div class="email-content">
            {TEMPLATE}
        </div>
        <div class="email-footer">
            <p>No. 67. Rivers State University shopping complex,<br> Nkpolu Oroworukwo Mile 3. Port Harcourt.</p>
            <p>0805 105 2273, 0901 3822 9859</p>
            <p>&copy; <?= date("Y") ?> Samogoza Loan Service. All rights reserved.</p>
        </div>
    </div>
</body>

</html>