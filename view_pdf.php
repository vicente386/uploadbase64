<!DOCTYPE html>
<html>
<head>
    <title>Visualizar PDF</title>
</head>
<body>
    <h2>Visualizar PDF</h2>
    <?php
    if(isset($_GET['id'])) {
        // Conectar ao banco de dados MySQL usando PDO
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "pgm";

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Recuperar o conteúdo do PDF específico
            $pdfId = $_GET['id'];
            $stmt = $conn->prepare("SELECT pdf_content FROM pdfs WHERE id = :pdfId");
            $stmt->bindParam(':pdfId', $pdfId);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                // Exibir o conteúdo do PDF em um iframe (pode ser melhorado para exibição mais avançada)
                echo "<iframe src='data:application/pdf;base64," . base64_encode($row['pdf_content']) . "' width='100%' height='600px'></iframe>";
            } else {
                echo "PDF não encontrado.";
            }
        } catch(PDOException $e) {
            echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
        }

        $conn = null;
    } else {
        echo "ID do PDF não fornecido.";
    }
    ?>
</body>
</html>
