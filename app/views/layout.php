<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRM</title>
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>public/dist/style.css">
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="bg-gray-100">
    <div class="flex">
        <?php if (is_logged_in()): // Only include sidebar if user is logged in ?>
            <?php if (is_admin()): ?>
                <?php include ROOT_PATH . 'app/views/partials/sidebar.php'; ?>
            <?php else: ?>
                <?php include ROOT_PATH . 'app/views/partials/user_sidebar.php'; ?>
            <?php endif; ?>
        <?php endif; ?>

        <div class="flex-1">
            <?php include ROOT_PATH . 'app/views/partials/header.php'; ?>

            <main class="p-4">
                <?php echo $content; ?>
            </main>
        </div>
    </div>

    <?php include ROOT_PATH . 'app/views/partials/footer.php'; ?>
</body>
</html>