<?php

header('Content-Type: application/json;charset=utf-8');

if( empty($_POST['factura_id'])          ||
    empty($_FILES['imgs'])
) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en la solicitud enviada.'
    ]);
    exit;
}

$conn = new mysqli("198.71.225.53", "chispuditoex", "Chispudito2015", "usercreator");

if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => "Ocurrió un error al intentar conectarse con la base de datos."
    ]);
    $conn->close();
    exit;
}

$facturaId = $_POST['factura_id'];
$imgs = $_FILES['imgs'];
$insertions = 0;
$imagesCount =  count($imgs['name']);
for ($i = 0; $i < $imagesCount; $i++){
    $name = $imgs['name'][$i];
    $size = $imgs['size'][$i];
    $type = $imgs['type'][$i];

    if ($type !== 'image/png' && $type !== 'image/jpg' && $type !== 'image/jpeg') {
        $conn->rollback();
        $conn->autocommit(true);
        $conn->close();
        echo json_encode([
            'success' => false,
            'message' => 'El formato del archivo adjunto no es válido. Por favor ingrese archivos en formato png o jpg'
        ]);
        exit;
    }

    $tmp_name = $imgs['tmp_name'][$i];
    $file = file_get_contents($tmp_name);
    $fileData = base64_encode($file);

    $query = "INSERT INTO factura_image (fid, image, image_type) values({$facturaId}, '{$fileData}', '{$type}');";
    if ($conn->query($query) === TRUE) {
        $insertions++;
    }
}

if ($insertions === $imagesCount) {
    $commited = $conn->autocommit(true);
    if ($commited) {
        echo json_encode([
            'success' => true,
            'message' => null,
        ]);
        $conn->close();
        exit;
    }
}
else {
    $message = $size === 1 ? 'la captura de pantalla' : 'una o varias capturas de pantalla';
    echo json_encode([
        'success' => false,
        'message' => "Ocurrió un error al intentar guardar {$message}. Por favor intente nuevamente."
    ]);
}

$conn->close();
exit;
