<!DOCTYPE html>
<html>
<head>
    <title>Upload e Visualização de PDFs</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h2>Upload e Visualização de PDFs</h2>
    <form action="index.php" method="post" enctype="multipart/form-data">
        <label for="file" class="file-label">Escolha os arquivos PDF para enviar</label>
        <input type="file" name="files[]" id="file" accept=".pdf" multiple class="file-input">
        <input type="submit" value="Enviar Arquivos" name="submit">
    </form>

    <div id="preview-container"></div>

    <!-- Modal para exibir o PDF -->
    <div id="pdfModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <iframe id="pdfViewer" width="100%" height="500"></iframe>
        </div>
    </div>

	<div class="pdf-gallery">
		<?php
			require_once 'view.php';
		?>
	</div>
	
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
                $fileType = strtolower(pathinfo($pdfFile, PATHINFO_EXTENSION)); // Correção: fechar pathinfo com parênteses

                // Verifica se o arquivo é um PDF
                if($fileType !== "pdf") {
                    echo "<script>
							document.addEventListener('DOMContentLoaded', function() {
								Swal.fire({
									icon: 'error',
									title: 'Erro!!',
									text: 'Desculpe, apenas arquivos PDF são permitidos.',
								});
							});
						  </script>";
					$uploadOk = 0;
                    continue; // Pule este arquivo e vá para o próximo
                }

                // Verifica se o arquivo já existe
                if (file_exists($pdfFile)) {
					echo "<script>
							document.addEventListener('DOMContentLoaded', function() {
								Swal.fire({
									icon: 'error',
									title: 'Erro!!',
									text: 'Desculpe, o arquivo $fileName já existe.',
								});
							});
						  </script>";
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

							echo "<script>
									document.addEventListener('DOMContentLoaded', function() {
										Swal.fire({
											icon: 'success',
											title: 'Sucesso!',
											text: 'Arquivo PDF $fileName enviado e armazenado no banco de dados.',
										});
									});
								  </script>";
                        } catch(PDOException $e) {
							$mensagen = "Erro ao enviar os dados para o banco de dados: " . $e->getMessage();
							echo "<script>
								document.addEventListener('DOMContentLoaded', function() {
									Swal.fire({
										icon: 'error',
										title: 'Erro!!',
										text: '$mensagen',
									});
								});
							  </script>";
                        }
                        $conn = null;
                    } else {
						echo "<script>
								document.addEventListener('DOMContentLoaded', function() {
									Swal.fire({
										icon: 'error',
										title: 'Erro!!',
										text: 'Desculpe, houve um erro no envio do arquivo $fileName.',
									});
								});
							  </script>";
                    }
                }

            } else {
				$mensagen = "Erro no upload do arquivo: " . $_FILES["files"]["error"][$key];
				echo "<script>
					document.addEventListener('DOMContentLoaded', function() {
						Swal.fire({
							icon: 'error',
							title: 'Erro!!',
							text: '$mensagen',
						});
					});
				  </script>";
            }

        }
    }
    ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
