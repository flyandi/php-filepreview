<?php

include("../vendor/autoload.php");

// create file preview
$filePreview = new FilePreview();


// render generic
$image = $filePreview->renderGeneric(320, 240, "XLS", "Microsoft Excel", FilePreview::COLOR_BROWN, FilePreview::COLOR_WHITE);
$filePreview->output($image, "output.xls.png");

// render generic
$image = $filePreview->renderGeneric(640, 300, "XLS", "Microsoft Excel", FilePreview::COLOR_BLUE, FilePreview::COLOR_WHITE);
$filePreview->output($image, "output.xls-long.png");

// render image
$image = $filePreview->renderPreview("./preview.jpg", 320, 240);
$filePreview->output($image, "output.preview.png");

// render image with caption
$image = $filePreview->renderPreviewWithCaption("./preview.jpg", 320, 240, "JPG", "Mountains", FilePreview::COLOR_BLACK, FilePreview::COLOR_WHITE);
$filePreview->output($image, "output.preview-caption.png");

// render file (pdf)
$image = $filePreview->renderPreview("./test.pdf", 320, 240);
$filePreview->output($image, "output.pdf.png");

// render file (docx)
$image = $filePreview->renderPreview("./test.docx", 320, 240);
$filePreview->output($image, "output.docx.png");

// render file (doc)
$image = $filePreview->renderPreview("./test.doc", 320, 240);
$filePreview->output($image, "output.doc.png");

// render file (text)
$image = $filePreview->renderPreview("./test.txt", 320, 240);
$filePreview->output($image, "output.txt.png");

// render file (csv)
$image = $filePreview->renderPreview("./test.csv", 320, 240);
$filePreview->output($image, "output.csv.png");



