<?php http_response_code(404); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="text-center">
        <h1 class="text-6xl font-bold text-gray-800">404</h1>
        <p class="text-2xl font-light text-gray-600">Sorry, the page you are looking for could not be found.</p>
        <a href="/login" class="mt-6 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Go to Homepage
        </a>
    </div>
</body>
</html>
