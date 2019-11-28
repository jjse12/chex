<?php
    header('Content-Type: application/json;charset=utf-8');
    require_once('factura_db_vars.php');
    $fids = $_POST['facturasId'];
    if (isset($fids) && is_array($fids)){
        $conn = new mysqli(FACTURA_DB_HOST, FACTURA_DB_USER, FACTURA_DB_PASS, FACTURA_DB_NAME);
        $ids = implode(',', array_map('intval', $fids));
        $query = "SELECT id, fid, image, image_type FROM factura_image WHERE fid IN ($ids) ORDER BY fid";
        $result = $conn->query($query);
        if (isset($result) && $result !== false) {
            $data = array();
            while($row = mysqli_fetch_assoc($result)){
                $data[$row['fid']][] = [
                    'id'    => $row['id'],
                    'image' => $row['image'],
                    'image_type' => $row['image_type']
                ];
            }

            echo json_encode([
                'success'   => true,
                'message'   => null,
                'data'      =>  $data
            ]);
        }
        else {
            echo json_encode([
                'success' => false,
                'message' => "Error en la consulta a la base de datos:
                              <br><br>{$conn->error}<br><br>
                              <b>Consulta:</b>
                              <br>{$query}"
            ]);
        }
        $conn->close();
    }
    else {
        echo json_encode([
            'success' => false,
            'message' => 'Error en la solicitud enviada.'
        ]);
        return;
    }
