<?php
$pdo = new PDO(
    'mysql:host=localhost;dbname=movies_imdb;charset=utf8mb4',
    'root',
    'root',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
