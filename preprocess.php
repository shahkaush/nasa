<?php
// Load JSON
$json = file_get_contents("pdf_index.json");
$data = json_decode($json, true);

if ($data === null) {
    die("Error: Invalid JSON\n");
}

// Convert PHP array to PHP code
$php = "<?php\nreturn " . var_export($data, true) . ";\n";

// Save to keywords.php
file_put_contents("keywords.php", $php);

echo "✅ Converted keywords.json → keywords.php\n";
?>