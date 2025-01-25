<?php
include 'db.php';

$db = new Database();
$conn = $db->connect();
$sql = "SELECT nome, email, Telefone FROM Usuario";
$stmt = $conn->prepare($sql);
$stmt->execute();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CadastrarEspaco.css">
    <title>Cadastrar Usuário</title>
</head>

<body>
    <div class="voltar">
        <a href="home.php"><button class="home">Início</button></a>
    </div>

    <h1 class="titulo">Cadastrar Usuario</h1>
    <form class="CadastrarNovoEspaco" method="POST">
        <label for="nome">Nome</label>
        <input type="text" name="nome" placeholder="Nome">

        <label for="email">E-mail</label>
        <input type="text" name="email" placeholder="E-mail">

        <label for="telefone">Telefone</label>
        <input type="text" name="telefone" placeholder="telefone"></label>

        <button type="submit">Cadastrar</button>

    </form>

    <h1 class="titulo">Usuarios Cadastrados</h1>

    <table border="1" class="tabelaToda">
        <thead>
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Telefone</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM Usuario";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nome']); ?></td>
                    <td><?php echo htmlspecialchars($row["email"]); ?></td>
                    <td><?php echo htmlspecialchars($row["telefone"]); ?></td>
                    <td>
                        <a href="editarUsuario.php?id=<?php echo $row['idUsuario']; ?>">Editar</a>

                        <form method="POST" style="display:inline">
                            <input type="hidden" name="idUsuario" value="<?php echo $row['idUsuario']; ?>">
                            <button type="submit" name="deletar">Deletar</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>

</html>


<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['deletar'])) {
    // var_dump($_POST);
    $nome = trim($_POST["nome"]);
    $email = trim($_POST["email"]);
    $telefone = trim($_POST["telefone"]);

    if (empty($nome) || empty($email) || empty($telefone)) {
        echo "preencha todos os campos!";
    } else {
        try {
            $sqlCode = "INSERT INTO Usuario(nome, email, telefone) VALUES (:nome, :email, :telefone)";
            $stmt = $conn->prepare($sqlCode);
            $stmt->bindParam(":nome", $nome);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":telefone", $telefone);
            $stmt->execute();
            echo "Cadastrado com sucesso!";

            header("Location: " . $_SERVER['PHP_SELF']);
        } catch (PDOException $e) {
            echo "Erro: " . $e->getMessage();
        }
    }
}

if (isset($_POST['deletar'])) {
    $idUsuario = intval($_POST['idUsuario']);
    try {

        $checkSql = "SELECT COUNT(*) AS total FROM reserva WHERE idUsuarioE = :idUsuario";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);
        $checkStmt->execute();
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);


        if ($result['total'] > 0) {
            echo "Não é possível excluir o usuário, ele tem reservas associadas.";
        } else {

            $deleteSql = "DELETE FROM Usuario WHERE idUsuario = :idUsuario";
            $deleteStmt = $conn->prepare($deleteSql);
            $deleteStmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);

            if ($deleteStmt->execute()) {
                echo "Usuário deletado com sucesso!";
            } else {
                echo "Erro ao deletar o usuário.";
            }
        }
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}


?>