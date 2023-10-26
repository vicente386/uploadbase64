<!DOCTYPE html>
<html>
<head>
    <title>Galeria de PDFs</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
	<style>
        .file-input {
            display: none;
        }

        .file-label {
            background-color: #007bff;
            color: #fff;
            padding: 5px 10px;
            cursor: pointer;
        }

        #preview-container {
            display: flex;
            flex-wrap: wrap;
        }

        .file-preview {
            border: 1px solid #ccc;
            margin: 10px;
            padding: 10px;
        }

        .thumbnail {
            max-width: 100px;
            max-height: 100px;
        }
    </style>	
</head>
<body>
    <h2>Galeria de PDFs</h2>
    <div class="pdf-gallery">
        <?php
        // Conectar ao banco de dados MySQL usando PDO
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "pgm";

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Recuperar os arquivos de PDF da tabela 'pdfs'
            $stmt = $conn->query("SELECT id, pdf_content FROM pdfs");

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $pdfId = $row['id'];
                echo "<div class='pdf-item'>";
                echo "<a href='#' class='pdf-link' data-pdf-id='$pdfId'>Visualizar PDF $pdfId</a>";
                echo "</div>";
            }
        } catch(PDOException $e) {
            echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
        }

        $conn = null;
        ?>
    </div>

    <div id="pdf-modal" style="display: none;">
        <iframe id="pdf-frame" width="100%" height="500"></iframe>
    </div>

	<script>
		$(document).ready(function() {
			$(".pdf-link").on("click", function() {
				var pdfId = $(this).data("pdf-id");

				// Defina a origem do iframe para exibir o PDF
				$("#pdf-frame").attr("src", "view_pdf.php?id=" + pdfId);

				// Obtenha a largura e altura da tela7
				var screenWidth = $(window).width();
				var screenHeight = $(window).height();

				// Defina a largura e altura da janela modal com base no tamanho da tela
				var modalWidth = screenWidth > 800 ? 800 : screenWidth - 20; // Limita a largura máxima em 800
				var modalHeight = screenHeight > 600 ? 600 : screenHeight - 20; // Limita a altura máxima em 600

				// Abra a janela modal no topo da página
				$("#pdf-modal").dialog({
					width: modalWidth,
					height: modalHeight,
					modal: true,
					position: { my: "1px", at: "1px", of: window }
				});
			}); 
		});
	</script>
	
</body>
</html>
