<?PHP
    function sendMessage() {
        $content      = array(
                              "en" => 'Welcom push notifications from server'
                              );
        
        $fields = array(
                        'app_id' => "ea4bf5e2-273e-4a40-b08b-334a69defe91",
                        'filters' => array(
                                           array("field" => "tag", "key" => "patient_id", "relation" => "=", "value" => "673097")
                                           ),
                        'data' => array("foo" => "bar"),
                        'contents' => $content
                        );
        
        $fields = json_encode($fields);
        print("\nJSON sent:\n");
        print($fields);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                                   'Content-Type: application/json; charset=utf-8',
                                                   'Authorization: Basic ZjEwOTc5MTItODcwYS00NjkzLWFiNTEtZWI2ZjE3ODE1ZmIx'
                                                   ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }
    
    $response = sendMessage();
    $return["allresponses"] = $response;
    $return = json_encode($return);
    
    $data = json_decode($response, true);
    print_r($data);
    $id = $data['id'];
    print_r($id);
    
    print("\n\nJSON received:\n");
    print($return);
    print("\n");
    ?>
