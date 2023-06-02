<?php
    require_once __DIR__ . '/vendor/autoload.php';

    $client = new Google_Client();
    $client->setApplicationName('phpp');
    $client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
    $client->setAccessType('offline');
    $client->setAuthConfig('D:\flutter\liquid-tractor-387206-76fe56d71678.json');

    $service = new Google_Service_Sheets($client);

    $spreadsheetId = '1EoO4nGs6mfRvfMct2MCmP53LV4CnkiJQoafeNsSDcow';

    $range = 'Sheet2!A1';
    $token = $service->spreadsheets_values->get($spreadsheetId, $range)->getValues()[0][0];

    $range = 'Sheet2!A2';
    $site_url = $service->spreadsheets_values->get($spreadsheetId,$range)->getValues()[0][0];

    $media_url = $site_url."wp-json/wp/v2/media";
    $post_url = $site_url."wp-json/wp/v2/posts";

    $response = $service->spreadsheets_values->get($spreadsheetId, 'Sheet1');
    $values = $response->getValues();
    
    foreach ($values as $row) 
    {
        $title = $row[0];
        $content = $row[1];
        $image_url = $row[2];

       

        $images = file_get_contents($image_url);
        file_put_contents("image.jpg", $images);

        $feature = "image.jpg";

        $media_id = get_media($media_url,$feature,$token);
        $result = create_post($title,$content,$media_id,$post_url,$token);

        if ($result === false) {
            echo "Error creating post: " . print_r(error_get_last(), true);     
        } else {
            $post_id = json_decode($result, true)['id'];
            echo "Post created with ID $post_id \n";
        }
        
        
        unlink($feature);
    }
    
    function get_media($media_url,$feature,$token)
    {
        $image_url = $feature;
        $image_filename = basename($image_url);

        // Get the image data using file_get_contents()
        $image_data = file_get_contents($image_url);
        $headers = array(
            'Content-Disposition: attachment; filename="' . $image_filename . '"',
            'Content-Type: image/jpeg', // Change this based on your image format
            'Authorization: Bearer '.$token,
            // 'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36'
            
        );
        $options = array(
            'httpversion' => '1.1',
            'header' => implode("\r\n", $headers),
            'method' => 'POST',
            'content' => $image_data,
        );

        $context = stream_context_create(array('http' => $options));
        // $get_header = get_headers($media_url,1,$context);
        // print_r($get_header);
        $response = file_get_contents($media_url, false, $context);

        $image_data = json_decode($response, true);

        // Get the ID of the uploaded image
        $image_id = $image_data['id'];

        return $image_id;
    }

    function create_post($title,$content,$image_id,$post_url,$token)
    {
        $post_data = array(
            'title' => $title,
            'content' => $content,
            'status' => 'publish',
            'featured_media' => $image_id ,// Set the image ID as featured image ID
        );
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$token 
        );
        $options = array(
            'header' => implode("\r\n", $headers),
            'method' => 'POST',
            'content' => json_encode($post_data),
        );

        // Make the REST API request to create the post and handle the response 
        $context = stream_context_create(array('http' => $options));
        $result = file_get_contents($post_url, false, $context);
        
        return $result;
    }
   
    
    
    
    
    
    
    


?>