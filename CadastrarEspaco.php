<?php
include 'db.php';

$db = new Database();
$conn = $db->connect();
$sql = "SELECT * FROM Espacos";
$stmt = $conn->prepare($sql);
$stmt->execute();

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



if (isset($_POST['deletar'])) {
    $idEspaco = intval($_POST['idEspaco']);
    try {

        $checkSql = "SELECT COUNT(*) AS total FROM reserva WHERE idEspacoE = :idEspaco";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bindParam(":idEspaco", $idEspaco, PDO::PARAM_INT);
        $checkStmt->execute();
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);

        $deleteSql = "DELETE FROM Espacos WHERE idEspacos = :idEspaco";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bindParam(":idEspaco", $idEspaco, PDO::PARAM_INT);

        if ($deleteStmt->execute()) {
            echo "Espaço deletado com sucesso!";
        } else {
            echo "Erro ao deletar o espaço.";
        }
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}



if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['deletar'])) {
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
            } else {
                echo "Erro ao cadastrar o espaço.";
            }
        } catch (PDOException $e) {
            echo "Erro: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Espaços</title>
    <link rel="stylesheet" href="EspacoStyle.css">
</head>

<body>

    <div class="voltar">
        <a href="home.php"><button class="home">Início</button></a>
    </div>

    <h1 class="titulo">CADASTRAR NOVOS ESPAÇOS</h1>
    <form class="CadastrarNovoEspaco" method="POST">
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
        <input type="text" name="capacidade" id="capacidade" placeholder="Capacidade">

        <label for="descricao">Descrição:</label>
        <input type="text" name="descricao" id="descricao" placeholder="Descrição">

        <button type="submit">Cadastrar</button>
    </form>



    <h1 class="titulo">Espaços Cadastrados</h1>
    <div class="espacoCadastrados">
        <table border="1" class="tabelaToda">
            <thead>
                <tr class="tituloTabala">
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Capacidade</th>
                    <th>Descrição</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php

                $sql = "SELECT * FROM Espacos";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td class="tabela"><?php echo htmlspecialchars($row['nome']); ?></td>
                        <td class="tabela"><?php echo htmlspecialchars($row['tipo']); ?></td>
                        <td class="tabela"><?php echo htmlspecialchars($row['capacidade']); ?></td>
                        <td class="tabela"><?php echo htmlspecialchars($row['descricao']); ?></td>
                        <td>
                            <a href="editarEspaco.php?id=<?php echo $row['idEspacos']; ?>"><button>Editar</button></a>

                            <form method="POST" style="display:inline">
                                <input type="hidden" name="idEspaco" value="<?php echo $row['idEspacos']; ?>">
                                <button type="submit" name="deletar">Deletar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>