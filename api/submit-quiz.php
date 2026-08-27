<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data || !isset($data['nama']) || !isset($data['nilai']) || !isset($data['waktu'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Data tidak lengkap']);
        exit;
    }

    $nama = htmlspecialchars(trim($data['nama']));
    $nilai = intval($data['nilai']);
    $waktu = htmlspecialchars(trim($data['waktu']));
    $tugas = isset($data['tugas']) ? htmlspecialchars(trim($data['tugas'])) : 'Tugas 6';
    $timestamp = isset($data['timestamp']) ? $data['timestamp'] : date('c');

    $logEntry = [
        'nama' => $nama,
        'nilai' => $nilai,
        'waktu' => $waktu,
        'tugas' => $tugas,
        'timestamp' => $timestamp,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];

    $logFile = __DIR__ . '/quiz-results.json';
    $existing = [];

    if (file_exists($logFile)) {
        $content = file_get_contents($logFile);
        $existing = json_decode($content, true) ?: [];
    }

    $existing[] = $logEntry;
    file_put_contents($logFile, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo json_encode([
        'success' => true,
        'message' => 'Hasil quiz berhasil disimpan',
        'data' => $logEntry
    ]);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
