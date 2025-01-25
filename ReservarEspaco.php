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
    <title>Reservar Espaço</title>
</head>
<body>
    <h1>Reservar Espaço</h1>
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
                    <td><?php echo htmlspecialchars($row['descricao']); ?></td>
                    <td>
                        <form method="POST" action="ReservarEspaco.php">
                            <input type="hidden" name="idEspaco" value="<?php echo htmlspecialchars($row['idEspacos']); ?>">
                            <label for="nomeUsuario">Nome:</label>
                            <input type="text" name="nomeUsuario" placeholder="Digite o nome" required>
                            <button type="submit">Reservar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <h1>Espaços Reservados</h1>
    <!-- botao de buscar reserva -->
    <form method="GET" >   
        <label for="buscar">Buscar </label>
        <input type="text" name="buscar" placeholder="Buscar">
        <button>Buscar</button>
    </form>
    
    <table border="1">
        <thead>
            <tr>
                <th>Espaço</th>
                <th>Tipo</th>
                <th>Capacidade</th>
                <th>Data Reserva</th>
                <th>Usuário</th>
                <th>Cancelar</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT 
            Espacos.nome AS espaco_nome, 
            Espacos.tipo AS espaco_tipo, 
            Espacos.capacidade AS espaco_capacidade,
            Usuario.nome AS usuario_nome,
            reserva.dataReserva AS data_reserva
            FROM reserva 
            JOIN Espacos ON Espacos.idEspacos = reserva.idEspacoE 
            JOIN Usuario ON Usuario.idUsuario = reserva.idUsuarioE";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['espaco_nome']); ?></td>
                    <td><?php echo htmlspecialchars($row['espaco_tipo']); ?></td>
                    <td><?php echo htmlspecialchars($row['espaco_capacidade']); ?></td>
                    <td><?php echo htmlspecialchars($row['data_reserva']); ?></td>
                    <td><?php echo htmlspecialchars($row['usuario_nome']); ?></td> 
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="espaco_nome" value="<?php echo htmlspecialchars($row['espaco_nome']); ?>">
                            <button type="submit" name="cancelar">Cancelar</button>
                        </form>
                    </td>                  
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>


<?php
if (isset($_GET['buscar'])) {
    $buscar = trim($_GET['buscar']); 

    if (!empty($buscar)) {
        try {
           
            $sql = "SELECT * FROM Espacos WHERE nome LIKE :buscar";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(":buscar", '%' . $buscar . '%'); 
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($resultados) > 0) {
                echo "<h2>Resultados da Busca:</h2>";
                echo "<table border='1'>";
                echo "<thead>
                        <tr>
                            <th>Espaço</th>
                            <th>Tipo</th>
                            <th>Capacidade</th>
                            <th>Descrição</th>
                        </tr>
                      </thead>";
                echo "<tbody>";

                foreach ($resultados as $resultado) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($resultado['nome']) . "</td>";
                    echo "<td>" . htmlspecialchars($resultado['tipo']) . "</td>";
                    echo "<td>" . htmlspecialchars($resultado['capacidade']) . "</td>";
                    echo "<td>" . htmlspecialchars($resultado['descricao']) . "</td>";
                    echo "</tr>";
                }

                echo "</tbody>";
                echo "</table>";
            } else {
                echo "<p>Nenhum resultado encontrado para: <strong>" . htmlspecialchars($buscar) . "</strong></p>";
            }
        } catch (PDOException $e) {
            echo "<p>Erro ao realizar a busca: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p>Por favor, insira um termo para buscar.</p>";
    }
}
?>




<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['cancelar'])) {
    $idEspaco = $_POST["idEspaco"];
    $nomeUsuario = $_POST['nomeUsuario'];

    try {
        if (empty($idEspaco) || empty($nomeUsuario)) {
            echo "Todos os campos são obrigatórios!";
            exit;
        }
    
        else{
            $sql = "SELECT idUsuario FROM Usuario WHERE nome = :nomeUsuario LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":nomeUsuario", $nomeUsuario);
            $stmt->execute();
            
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$usuario) {
                echo "Usuário não encontrado!";
                exit;
            }
            
            $idUsuario = $usuario['idUsuario']; 

            $sql = "SELECT * FROM reserva WHERE idUsuarioE = :id_usuario";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":id_usuario", $idUsuario);
            $stmt->execute();

            $idDoUsuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$idDoUsuario)
            {
                $sql = "INSERT INTO reserva (idEspacoE, idUsuarioE) VALUES (:idEspaco, :idUsuario)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":idEspaco", $idEspaco);
                $stmt->bindParam(":idUsuario", $idUsuario);
                $stmt->execute();
                echo "Reserva feita com sucesso!";                
            }   
            else{
                echo "Usuário já possui reserva!";
            }        
        }
                
    } catch (PDOException $e) {
        echo "Erro ao realizar a reserva: " . $e->getMessage();
    }


   
}

if(isset($_POST['cancelar']))
{
    $NomeEspaco = $_POST["espaco_nome"];
    $sql = "DELETE FROM reserva WHERE idEspacoE = (SELECT idEspacos FROM Espacos WHERE nome = :espaco_nome)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":espaco_nome", $NomeEspaco);
    $stmt->execute();
    echo "Reserva cancelada com sucesso!";

}
?>
