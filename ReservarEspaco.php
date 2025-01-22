<?php
include 'db.php';

$db = new Database();
$conn = $db->connect();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Espaco</title>
</head>
<body>
<h1>Reservar Espaco</h1>
<table border="1">
        <thead>
                <tr>
                <th>Nome</th>
                <th>Tipo</th>
                <th>Capacidade</th>
                <th>Descrição</th> 
                <th>Reservar</th>                   
            </tr>
        </thead>
        <tbody>
            <?php
                
            $sql = "SELECT * FROM Espacos";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['nome']); ?></td>
                <td><?php echo htmlspecialchars($row['tipo']); ?></td>
                <td><?php echo htmlspecialchars($row['capacidade']); ?></td>
                <td><?php echo htmlspecialchars($row['descricao']); ?>
                <td>
                    <form method="POST" action="ReservarEspaco.php">
                        <input type="hidden" name="idEspaco" value="<?php echo htmlspecialchars($row['idEspacos']); ?>">

                        <button type="submit">Reservar</button>
                    </form>
                </td>

            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <script>
        function minhaFuncao() {
            alert("Local reservado");
        }
    </script>
</body>
</html>