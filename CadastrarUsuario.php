<?php 
    include 'db.php';

    $db = new Database();
    $conn = $db->connect();
    $sql = "SELECT nome, email, Telefone FROM Usuario"; 
    $stmt = $conn->prepare($sql);
    $stmt->execute();
  
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Cadastrar Usuario</h1>
    <form action="" method="POST">
        <label for="nome">Nome</label>
        <input type="text" name="nome" placeholder="Nome">

        <label for="email">E-mail</label>
        <input type="text" name="email" placeholder="E-mail">

        <label for="Telefone">Telefone</label>
        <input type="text" name="Telefone" placeholder="Telefone"></label>

        <button type="submit">Cadastrar</button>
       
    </form>

    <h1>Usuarios Cadastrados</h1>

    <table border="1">
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
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Telefone']) . "</td>";
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
</body>
</html>


<?php
    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $nome = $_POST["nome"];
        $email = $_POST["email"];
        $Telefone = $_POST["Telefone"];

        try
        {
            $sqlCode = "INSERT INTO Usuario(nome, email, telefone) VALUES (:nome, :email, :Telefone)";
            $stmt = $conn->prepare($sqlCode);
            $stmt->bindParam(":nome", $nome);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":Telefone", $Telefone);
            $stmt->execute();
            echo "Cadastrado com sucesso!";
        }
        catch(PDOException $e)
        {
            echo "Erro: " . $e->getMessage();   
        }
        
    }

?>