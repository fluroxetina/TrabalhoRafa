<?php
include 'db.php';
$db = new Database();
$conn = $db->connect();

if (isset($_GET['id'])) {
    $idEspaco = intval($_GET['id']);
    $sql = "SELECT * FROM Espacos WHERE idEspacos = :idEspaco";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':idEspaco', $idEspaco, PDO::PARAM_INT);
    $stmt->execute();
    $espaco = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$espaco) {
        echo "Espaco não encontrado!";
        exit;
    }
} else {
    echo "ID do espaco não especificado!";
    exit;
}

$sql = "SHOW COLUMNS FROM Espacos LIKE 'tipo'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);


if ($result) {
    $type = $result['Type'];
    preg_match("/^enum\((.*)\)$/", $type, $matches);
    $enumValues = isset($matches[1]) ? explode(",", str_replace("'", "", $matches[1])) : [];
} else {
    $enumValues = [];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $tipo = trim($_POST['tipo']);
    $capacidade = trim($_POST['capacidade']);
    $descricao = trim($_POST['descricao']);

    if (empty($nome) || empty($tipo) || empty($capacidade) || empty($descricao)) {
        echo "Todos os campos são obrigatórios!";
    } else {
        try {
            $updateSql = "UPDATE Espacos SET nome = :nome, tipo = :tipo, capacidade = :capacidade, descricao = :descricao WHERE idEspacos = :idEspaco";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bindParam(':nome', $nome);
            $updateStmt->bindParam(':tipo', $tipo);
            $updateStmt->bindParam(':capacidade', $capacidade);
            $updateStmt->bindParam(':descricao', $descricao);
            $updateStmt->bindParam(':idEspaco', $idEspaco, PDO::PARAM_INT);

            if ($updateStmt->execute()) {
                echo "Espaco atualizado com sucesso!";
            } else {
                echo "Erro ao atualizar o espaco.";
            }
        } catch (PDOException $e) {
            echo "Erro: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CadastrarEspaco.css">
    <title>Editar Espaço</title>
</head>

<body>

    <div class="voltar">
        <a href="home.php"><button class="home">Início</button></a>
    </div>
    
    <h1 class="titulo">Editar Espaço</h1>
    <form class="CadastrarNovoEspaco" method="POST">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" value="<?php echo htmlspecialchars($espaco['nome']); ?>" required>

        <label for="tipo">Tipo:</label>
        <select name="tipo" id="tipo" required>
            <option value="">Selecione um tipo</option>
            <?php foreach ($enumValues as $valor): ?>
                <option value="<?php echo htmlspecialchars($valor); ?>"
                    <?php echo ($valor === $espaco['tipo']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($valor); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="capacidade">Capacidade:</label>
        <input type="text" name="capacidade" value="<?php echo htmlspecialchars($espaco['capacidade']); ?>" required>

        <label for="descricao">Descrição:</label>
        <input type="text" name="descricao" value="<?php echo htmlspecialchars($espaco['descricao']); ?>" required>

        <button type="submit">Salvar Alterações</button>
    </form>
</body>

</html>

</html>