<?php
if(isset($_POST["submit"])) {
    $targetDir = "uploads/";
    $uploadOk = 1;
    
    // Verifique se o diretório de destino existe, caso contrário, crie-o
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    // Loop pelos arquivos enviados
    foreach ($_FILES["files"]["name"] as $key => $fileName) {
			if ($_FILES["files"]["error"][$key] == UPLOAD_ERR_OK) {
				
				$pdfFile = $targetDir . basename($fileName);
        $fileType = strtolower(pathinfo($pdfFile, PATHINFO_EXTENSION));

        // Verifica se o arquivo é um PDF
        if($fileType !== "pdf") {
            echo "Desculpe, apenas arquivos PDF são permitidos.";
            $uploadOk = 0;
            continue; // Pule este arquivo e vá para o próximo
        }

        // Verifica se o arquivo já existe
        if (file_exists($pdfFile)) {
            echo "Desculpe, o arquivo '$fileName' já existe.";
            $uploadOk = 0;
            continue; // Pule este arquivo e vá para o próximo
        }

        if ($uploadOk === 1) {
            if (move_uploaded_file($_FILES["files"]["tmp_name"][$key], $pdfFile)) {
				
                // Ler o arquivo PDF e armazená-lo no banco de dados
                $pdfContent = file_get_contents($pdfFile);

                // Conectar ao banco de dados MySQL usando PDO
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "pgm";

                try {
				
                    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Inserir conteúdo do PDF no banco de dados
                    $stmt = $conn->prepare("INSERT INTO pdfs (pdf_content) VALUES (:pdfContent)");
                    $stmt->bindParam(':pdfContent', $pdfContent, PDO::PARAM_LOB);
                    $stmt->execute();

                    echo "Arquivo PDF '$fileName' enviado e armazenado no banco de dados.";
                } catch(PDOException $e) {
                    echo "Erro ao enviar os dados para o banco de dados: " . $e->getMessage();
                }

                $conn = null;
            } else {
                echo "Desculpe, houve um erro no envio do arquivo '$fileName'.";
            }
        }
			
			}else{
				echo "Erro no upload do arquivo: " . $_FILES["files"]["error"][$key];
			}
		
    }
}

?>