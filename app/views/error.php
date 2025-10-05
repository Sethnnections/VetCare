<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8d7da; color: #721c24; margin: 0; padding: 40px; }
        .container { max-width: 600px; margin: auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 32px; }
        h1 { color: #721c24; }
        p { margin-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>An error occurred</h1>
        <p>
            <?php
            if (isset($error_message) && !empty($error_message)) {
                echo htmlspecialchars($error_message);
            } else {
                echo "Sorry, something went wrong. Please try again later.";
            }
            ?>
        </p>
    </div>
</body>
</html>