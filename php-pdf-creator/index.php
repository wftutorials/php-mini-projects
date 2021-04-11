<?php
require_once __DIR__ . '/vendor/autoload.php';


if(isset($_POST['text'])){

  $content = isset($_POST['text']) ? $_POST['text'] : null;
  $mpdf = new \Mpdf\Mpdf();
  $mpdf->WriteHTML($content);
  $mpdf->Output();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pdf Creator</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.23.0/ui/trumbowyg.min.css">
  <script
    src="https://code.jquery.com/jquery-3.5.1.min.js"
    integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
    crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.23.0/trumbowyg.min.js"></script>
</head>
<body>

<h1>Create A PDF HERE</h1>
<p>Create a PDF</p>
<form method="post">
  <textarea id="content" name="text" cols="50" rows="15"> </textarea><br>
  <button type="submit">Create your pdf file</button>
</form>
<script>
  $('#content').trumbowyg();
</script>
</body>
</html>



