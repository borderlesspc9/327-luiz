<?php
/**
 * Health Check Endpoint
 * Usado pelo Docker para verificar se o serviço está funcionando
 */

header('Content-Type: application/json');

$health = [
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'service' => 'semmultas-backend',
    'version' => '1.0.0'
];

// Verificar conexão com banco
try {
    $db = new PDO('sqlite:' . __DIR__ . '/../database/database.sqlite');
    $health['database'] = 'connected';
} catch (Exception $e) {
    $health['database'] = 'error';
    $health['status'] = 'degraded';
}

// Verificar storage
$storageWritable = is_writable(__DIR__ . '/../storage');
$health['storage'] = $storageWritable ? 'writable' : 'readonly';

if (!$storageWritable) {
    $health['status'] = 'degraded';
}

http_response_code($health['status'] === 'ok' ? 200 : 503);
echo json_encode($health, JSON_PRETTY_PRINT);
