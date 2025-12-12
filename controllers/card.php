<?php
session_start();

$diretorio = "../uploads/";
$imagens = glob($diretorio . "*");

// Start output buffering
ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Gallery</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="card-container">
        <h1>Image Gallery</h1>
        <div class="galeria" id="imageGallery">
            <?php foreach ($imagens as $imagem): ?>
                <div class="imagem-card">
                    <img src="<?php echo $imagem; ?>" alt="Image">
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>

<?php
// Capture the output into a variable
$output = ob_get_clean();

// Output the buffered content
echo $output;
?>