<?php
$host = 'localhost';
$db   = 'bloodbridge_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     if ($e->getCode() == 1049 || str_contains($e->getMessage(), 'Unknown database')) {
         echo "<div style='font-family: sans-serif; max-width: 600px; margin: 50px auto; padding: 30px; border: 1px solid #e74c3c; border-radius: 8px; background-color: #fdf2f2;'>";
         echo "<h2 style='color: #c0392b; margin-top: 0;'><i class='fa-solid fa-triangle-exclamation'></i> Database Connection Error</h2>";
         echo "<p>The database <strong>bloodbridge_db</strong> was not found on your MySQL server.</p>";
         echo "<h3>Setup Instructions:</h3>";
         echo "<ol style='line-height: 1.6;'>";
         echo "<li>Go to <strong>phpMyAdmin</strong>: <a href='http://localhost/phpmyadmin/' target='_blank'>http://localhost/phpmyadmin/</a></li>";
         echo "<li>Create a new database named: <strong>bloodbridge_db</strong></li>";
         echo "<li>Select the new database, go to the <strong>Import</strong> tab, and import the SQL file: <br><code>c:\\xampp\\htdocs\\Blood Bridge\\sql\\bloodbridge_db.sql</code></li>";
         echo "</ol>";
         echo "<p>Once imported, refresh this page to start using the system!</p>";
         echo "</div>";
         exit;
     }
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
