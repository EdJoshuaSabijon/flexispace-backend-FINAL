<?php
try { 
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => true,
    ];
    $pdo = new PDO('mysql:host=tramway.proxy.rlwy.net;port=39154;dbname=railway', 'root', 'AniutCDIfiDqkngWCSLFYoNDYRcVHxiT', $options); 
    echo 'Success'; 
} catch (PDOException $e) { 
    echo $e->getMessage(); 
}
