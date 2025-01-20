<?php
    include 'db.php';

    $db = new Database();
    $conn = $db->connect();
    $sql = "SELECT * FROM Espacos";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    // Consulta para obter os valores ENUM
    $sql = "SHOW COLUMNS FROM Espacos LIKE 'tipo'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Extrair os valores ENUM
    if ($result) {
        $type = $result['Type']; // Exemplo: "enum('Sala','Laboratórios','Quadras Esportivas')"
        preg_match("/^enum\((.*)\)$/", $type, $matches);
        $enumValues = isset($matches[1]) ? explode(",", str_replace("'", "", $matches[1])) : [];
    } else {
        $enumValues = [];
    }

?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Espaços</title>
</head>

<body>
    <h1>Cadastrar novos espaços</h1>
    <form method="POST">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" placeholder="Nome">

        <label for="tipo">Tipo:</label>

        <select name="tipo" id="tipo">
            <option value="">Selecione um tipo</option>
            <?php foreach ($enumValues as $value): ?>
                <option value="<?php echo htmlspecialchars($value); ?>">
                    <?php echo htmlspecialchars($value); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="capacidade">Capacidade:</label>
        <input type="number" name="capacidade" id="capacidade" placeholder="Capacidade">

        <label for="descricao">Descrição:</label>
        <input type="text" name="descricao" id="descricao" placeholder="Descrição">

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
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Exibir registros existentes
            $sql = "SELECT * FROM Espacos";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nome']); ?></td>
                    <td><?php echo htmlspecialchars($row['tipo']); ?></td>
                    <td><?php echo htmlspecialchars($row['capacidade']); ?></td>
                    <td><?php echo htmlspecialchars($row['descricao']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>


</html>
<?php


// Lógica para cadastrar espaço
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $tipo = trim($_POST['tipo']);
    $capacidade = trim($_POST['capacidade']);
    $descricao = trim($_POST['descricao']);

    if (empty($nome) || empty($tipo) || empty($capacidade) || empty($descricao)) {
        echo "Preencha todos os campos!";
    } else {
        try {
            $sql = "INSERT INTO Espacos (nome, tipo, capacidade, descricao) VALUES(:nome, :tipo, :capacidade, :descricao)";
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(":nome", $nome);
            $stmt->bindParam(":tipo", $tipo);
            $stmt->bindParam(":capacidade", $capacidade);
            $stmt->bindParam(":descricao", $descricao);

            if ($stmt->execute()) {
                echo "Espaço cadastrado com sucesso!";
                header("Location: CadastrarEspaco.php");
                exit;
            } else {
                echo "Erro ao cadastrar o espaço.";
            }
        } catch (PDOException $e) {
            echo "Erro: " . $e->getMessage();
        }
    }
}
?>