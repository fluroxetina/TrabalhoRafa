<?php 
    include 'db.php';

    $db = new Database();
    $conn = $db->connect();
    $sql = "SELECT * FROM Espacos";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Espaços</title>
</head>
<body>
    <h1>Cadastrar novos espaços</h1>
    <form method="POST">
        <label for="nome">Nome</label>
        <input type="text" name="nome" placeholder="Nome" >

        <label for="tipo">Tipo</label>
        <select name="tipo" required>
            <option value="Sala">Salas de Reunião</option>
            <option value="laboratórios">Laboratórios</option>
            <option value="quadras esportiva">Quadras Esportivas</option>
            
            
        </select>

        <label for="capacidade">Capacidade</label>
        <input type="text" name="capacidade" placeholder="Capacidade" >

        <label for="descricao">Descrição</label>
        <input type="text" name="descricao" placeholder="Descrição" >

        <button type="submit">Cadastrar</button>
    </form>
    <h1>Espaços Cadastrados</h1>

    <table border="1">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Tipo</th>
                <th>Capacidade</th>
                <th>Descrição</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['tipo']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['capacidade']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['descricao']) . "</td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>

</body>
</html>

<?php 
    

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $nome = $_POST['nome'];
        $tipo = $_POST['tipo'];
        $capacidade = $_POST['capacidade'];
        $descricao = $_POST['descricao'];

        try {
            $sql = "INSERT INTO Espacos (nome, tipo, capacidade, descricao) VALUES(:nome, :tipo, :capacidade, :descricao)";
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(":nome", $nome);
            $stmt->bindParam(":tipo", $tipo);
            $stmt->bindParam(":capacidade", $capacidade);
            $stmt->bindParam(":descricao", $descricao);

            if ($stmt->execute()) {
                echo "Espaço cadastrado com sucesso!";
            } else {
                echo "Erro ao cadastrar o espaço.";
            }
        } catch (PDOException $e) {
            echo "Erro: " . $e->getMessage();
        }
    }
?>

