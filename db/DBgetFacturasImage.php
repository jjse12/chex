<?php
    header('Content-Type: application/json;charset=utf-8');
    require_once("server_db_vars.php");
    $fids = $_POST['facturasId'];
    if (isset($fids) && is_array($fids)){
        $conn = new mysqli(SERVER_DB_HOST, SERVER_DB_USER, SERVER_DB_PASS, SERVER_DB_NAME);
        $conn->set_charset('utf8mb4');
        $ids = implode(',', array_map('intval', $fids));
        $query = "SELECT fid, image, image_type FROM factura_image WHERE fid IN ($ids) ORDER BY fid";
        $result = $conn->query($query);
        if (isset($result) && $result !== false) {
            $data = array();
            while($row = mysqli_fetch_assoc($result)){
                $data[$row['fid']][] = [
                    'image' => $row['image'],
                    'image_type' => $row['image_type']
                ];
            }

            echo json_encode([
                'success' => true,
                'message' => null,
                'data' => empty($data) ? null : $data
            ]);
        }
        else {
            header("HTTP/1.1 500 Internal Server Error");
        }
    }
    else {
        echo json_encode([
            'success' => false,
            'message' => 'Error en la solicitud enviada.'
        ]);
        return;
    }
