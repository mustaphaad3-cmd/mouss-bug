<?php
// admin.php - Page pour voir les données collectées

session_start();

// Simple authentification
$password = 'admin123'; // À changer !

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === $password) {
        $_SESSION['logged_in'] = true;
    }
}

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Admin Login</title>
        <style>
            body { font-family: Arial; background: #f0f0f0; display: flex; justify-content: center; align-items: center; height: 100vh; }
            .login-form { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
            input { padding: 10px; margin: 10px 0; width: 200px; }
            button { padding: 10px 20px; background: #667eea; color: white; border: none; cursor: pointer; }
        </style>
    </head>
    <body>
        <div class="login-form">
            <h2>Administration</h2>
            <form method="POST">
                <input type="password" name="password" placeholder="Mot de passe" required>
                <button type="submit">Se connecter</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Afficher les données
$data_file = 'collected_data.json';
$data = [];
if (file_exists($data_file)) {
    $data = json_decode(file_get_contents($data_file), true) ?? [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Données collectées</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #f0f0f0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #333; margin-bottom: 20px; }
        .stats { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .stat-box { text-align: center; }
        .stat-number { font-size: 36px; font-weight: bold; color: #667eea; }
        .stat-label { color: #666; }
        .data-table { background: white; border-radius: 10px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #667eea; color: white; padding: 15px; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f5f5f5; }
        .export-btn { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 20px; }
        .logout { float: right; background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Données collectées 
            <a href="?logout=1" class="logout">Déconnexion</a>
        </h1>
        
        <?php
        if (isset($_GET['logout'])) {
            session_destroy();
            header('Location: admin.php');
            exit;
        }
        ?>

        <div class="stats">
            <div class="stat-box">
                <div class="stat-number"><?php echo count($data); ?></div>
                <div class="stat-label">Total entrées</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">
                    <?php 
                    $whatsapp = array_filter($data, function($d) { return !empty($d['whatsapp']); });
                    echo count($whatsapp);
                    ?>
                </div>
                <div class="stat-label">WhatsApp</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">
                    <?php 
                    $snap = array_filter($data, function($d) { return !empty($d['snapchat']); });
                    echo count($snap);
                    ?>
                </div>
                <div class="stat-label">Snapchat</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">
                    <?php 
                    $today = array_filter($data, function($d) { 
                        return substr($d['received_at'] ?? '', 0, 10) == date('Y-m-d');
                    });
                    echo count($today);
                    ?>
                </div>
                <div class="stat-label">Aujourd'hui</div>
            </div>
        </div>

        <button class="export-btn" onclick="exportCSV()">📥 Exporter en CSV</button>

        <div class="data-table">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>WhatsApp</th>
                        <th>Snapchat</th>
                        <th>Ville</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach(array_reverse($data) as $row): ?>
                    <tr>
                        <td><?php echo substr($row['received_at'] ?? '', 5, 11); ?></td>
                        <td><?php echo htmlspecialchars($row['nom'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['telephone'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['whatsapp'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['snapchat'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['ville'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['ip'] ?? ''); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function exportCSV() {
        let csv = "Date,Nom,Email,Téléphone,WhatsApp,Snapchat,Instagram,Facebook,Ville,IP\n";
        
        <?php foreach($data as $row): ?>
        csv += "<?php echo $row['received_at'] ?? ''; ?>,<?php echo $row['nom'] ?? ''; ?>,<?php echo $row['email'] ?? ''; ?>,<?php echo $row['telephone'] ?? ''; ?>,<?php echo $row['whatsapp'] ?? ''; ?>,<?php echo $row['snapchat'] ?? ''; ?>,<?php echo $row['instagram'] ?? ''; ?>,<?php echo $row['facebook'] ?? ''; ?>,<?php echo $row['ville'] ?? ''; ?>,<?php echo $row['ip'] ?? ''; ?>\n";
        <?php endforeach; ?>
        
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'donnees_collectees.csv';
        a.click();
    }
    </script>
</body>
</html>