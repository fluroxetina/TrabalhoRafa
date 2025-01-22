<?php
include 'db.php';
$db = new Database();
$conn = $db->connect();

if (isset($_GET['id'])) {
    $idUsuario = intval($_GET['id']);
    $sql = "SELECT * FROM Usuario WHERE idUsuario = :idUsuario";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo "Usuário não encontrado!";
        exit;
    }
} else {
    echo "ID do usuário não especificado!";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["nome"]);
    $email = trim($_POST["email"]);
    $telefone = trim($_POST["telefone"]);

    if (empty($nome) || empty($email) || empty($telefone)) {
        echo "Todos os campos são obrigatórios!";
    } else {
        try {
            $updateSql = "UPDATE Usuario SET nome = :nome, email = :email, telefone = :telefone WHERE idUsuario = :idUsuario";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bindParam(':nome', $nome);
            $updateStmt->bindParam(':email', $email);
            $updateStmt->bindParam(':telefone', $telefone);
            $updateStmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);

            if ($updateStmt->execute()) {
                echo "Usuário atualizado com sucesso!";
            } else {
                echo "Erro ao atualizar o usuário.";
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
    <title>Editar Usuário</title>
</head>
<body>
    <h1>Editar Usuário</h1>
    <form method="POST">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
        <br>

        <label for="email">E-mail:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
        <br>

        <label for="telefone">Telefone:</label>
        <input type="text" name="telefone" value="<?php echo htmlspecialchars($usuario['telefone']); ?>" required>
        <br>

        <button type="submit">Salvar Alterações</button>
    </form>
</body>
</html>
