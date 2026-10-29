<?php
// Intentionally vulnerable test app — unrestricted file upload (PHP2Shell pattern).
// For authorized Snyk API & Web (DAST) scan testing only.
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>php2shell-test-app</title>
</head>
<body>
  <h1>php2shell-test-app</h1>
  <p>Vulnerable test target for Snyk API &amp; Web DAST scanning. See README.md.</p>

  <h2>Upload a file</h2>
  <form action="upload.php" method="post" enctype="multipart/form-data">
    <input type="file" name="file">
    <button type="submit">Upload</button>
  </form>

  <h2>Uploaded files</h2>
  <ul>
    <?php
    $dir = __DIR__ . '/uploads';
    if (is_dir($dir)) {
        foreach (scandir($dir) as $f) {
            if ($f === '.' || $f === '..' || $f === '.gitkeep') continue;
            echo '<li><a href="uploads/' . htmlspecialchars($f) . '">' . htmlspecialchars($f) . '</a></li>';
        }
    }
    ?>
  </ul>
</body>
</html>
