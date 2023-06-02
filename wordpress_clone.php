<?php


    include('function.php');
    set_time_limit(3000);
    $obj = new copy;
    $time_start = microtime(true); 


    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $sql_file_OR_content = 'backup.sql';
    require_once __DIR__ . '/vendor/autoload.php';

    $client = new Google_Client();
    $client->setApplicationName('phpp');
    $client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
    $client->setAccessType('offline');
    $client->setAuthConfig('D:\flutter\liquid-tractor-387206-76fe56d71678.json');

    $service = new Google_Service_Sheets($client);

    $spreadsheetId = '1EoO4nGs6mfRvfMct2MCmP53LV4CnkiJQoafeNsSDcow';
    // $range = "Sheet3";
    $response = $service->spreadsheets_values->get($spreadsheetId, 'Sheet3');
    $values = $response->getValues();
    
    foreach ($values as $row) 
    {
        $src = $row[0]; //D:/xampp/htdocs/blog.com
    $dst = $row[1]; //D:/xampp/htdocs/blog3.mpcc.in
    $imgsrc = $row[2]; 

    $srrc = basename($src); //blog.com
    $dsst = basename($dst); //blog3.mpcc.in
    $dstimg = basename($imgsrc); //blog3.mpcc.png
    $olddatabase = pathinfo($srrc, PATHINFO_FILENAME); //blog
    $olddatabasee = pathinfo($olddatabase, PATHINFO_FILENAME); //blog
    $newdatabase = pathinfo($dsst,PATHINFO_FILENAME); //blog3.mpcc
    $newdatabasee = pathinfo($newdatabase,PATHINFO_FILENAME); //blog3
    // $dstimg = pathinfo($imgsrc,PATHINFO_FILENAME);
  
    $obj->custom_copy($src, $dst,$olddatabasee,$newdatabasee);
    $obj->Export_Database($host,$user,$pass,$olddatabasee,$srrc,$dsst,$dstimg,  $tables=false, $backup_name=false );
    $obj->IMPORT_TABLES($host, $user, $pass, $newdatabasee, $sql_file_OR_content);
    echo "successfully copy at " . $dst;
    echo "<br>";
    unlink('backup.sql');
    unlink($dst.'/wp-content/uploads/'.$olddatabasee.'.png');
    $imgdes = $dst.'/wp-content/uploads/'.$dstimg;
    copy($imgsrc,$imgdes);

    

}
$time_end = microtime(true);
    $time = ($time_end - $time_start);
    $time = number_format((float)$time, 3, '.', '');
    echo "Process Time: {$time} sec";