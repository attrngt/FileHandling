<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keyword = $_POST['keyword'] ?? '';
    $operation = strtolower($_POST['operation'] ?? 'find');
    $output_type = strtoupper($_POST['output_type'] ?? 'N');
    $foundkeyword = false;

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        die("Gagal mengunggah file.");
    }

    $tmpFile = $_FILES['file']['tmp_name'];
    $originalName = $_FILES['file']['name'];
    $lines = file($tmpFile);
    $result_lines = [];

    echo "<h3>Hasil Pencarian:</h3><pre>";

 foreach ($lines as $line) {
    if (stripos($line, $keyword) !== false) {
        $foundkeyword = true;
        // memilih redact maka keyword akan disensor dengan ***
        if ($operation === 'redact') {
            $line = str_ireplace($keyword, "<span style='background-color: yellow;'>$keyword</span>", $line);
            $edited_line = str_ireplace($keyword, '***', $line);
            $result_lines[] = $edited_line;
        } else {
            // jika hanya find, tidak ada perubahan
            $result_lines[] = $line;
        }
        // tampilkan
        echo $line;
    } else {
        // jika tidak ada keyword dan operasi redact
        $result_lines[] = $line;
    }
}
    if (!$foundkeyword) {
    echo "Keyword '<strong>", $keyword, "</strong>' not found!</p>";
    }


    echo "</pre>";

    // mengubah nama file hasil
    $pathinfo = pathinfo($originalName);
    $outputName = ($output_type === 'O')
        ? $originalName
        : $pathinfo['filename'] . "-new." . ($pathinfo['extension'] ?? 'txt');

    $outputPath = __DIR__ . '/' . $outputName; // menyimpan file new di folder yang sama
    if ($operation === 'redact') {
        file_put_contents($outputPath, implode('', $result_lines));
        echo "<p>Hasil disimpan di: <a href='$outputName'>$outputName</a></p>";
    }
} else {
    echo "Form belum dikirim.";
}
?>
