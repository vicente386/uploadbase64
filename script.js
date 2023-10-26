document.getElementById('file').addEventListener('change', function () {
            var files = this.files;
            var previewContainer = document.getElementById('preview-container');

            for (var i = 0; i < files.length; i++) {
                var file = files[i];

                if (file.type === 'application/pdf') {
                    createFilePreview(file, previewContainer);
                }
            }
        });

        function createFilePreview(file, container) {
            var preview = document.createElement('div');
            preview.className = 'file-preview';

            var name = createInfoElement('Nome: ' + file.name);
            var size = createInfoElement('Tamanho: ' + (file.size / 1024).toFixed(2) + ' KB');
            var type = createInfoElement('Tipo: ' + file.type);

            var thumbnail = createThumbnail();
            var infoIcon = createInfoIcon();
            var fileInfo = createFileInfo(file);
            var deleteIcon = createDeleteIcon();

            preview.appendChild(thumbnail);
            preview.appendChild(infoIcon);
            preview.appendChild(fileInfo);
            preview.appendChild(deleteIcon);

            container.appendChild(preview);

            infoIcon.addEventListener('click', function () {
                fileInfo.style.display = 'block';
            });

            infoIcon.addEventListener('mouseout', function () {
                fileInfo.style.display = 'none';
            });

            thumbnail.addEventListener('click', function () {
                openPdfModal(file);
            });

            deleteIcon.addEventListener('click', function () {
                // Remove o arquivo da lista
                container.removeChild(preview);
            });

            renderPdfThumbnail(file, thumbnail);
        }

        function createInfoElement(text) {
            var element = document.createElement('p');
            element.innerHTML = text;
            return element;
        }

        function createThumbnail() {
            var thumbnail = document.createElement('canvas');
            thumbnail.className = 'thumbnail';
            return thumbnail;
        }

        function createInfoIcon() {
            var infoIcon = document.createElement('div');
            infoIcon.className = 'info-icon';
            infoIcon.innerHTML = '?';
            return infoIcon;
        }

        function createFileInfo(file) {
            var fileInfo = document.createElement('div');
            fileInfo.className = 'file-info';
            fileInfo.innerHTML = 'Nome: ' + file.name + '<br>Tamanho: ' + (file.size / 1024).toFixed(2) + ' KB<br>Tipo: ' + file.type;
            return fileInfo;
        }

        function createDeleteIcon() {
            var deleteIcon = document.createElement('div');
            deleteIcon.className = 'delete-icon';
            deleteIcon.innerHTML = 'X';
            return deleteIcon;
        }

        function renderPdfThumbnail(file, canvas) {
            var fileReader = new FileReader();

            fileReader.onload = function (e) {
                var arrayBuffer = e.target.result;

                pdfjsLib.getDocument(arrayBuffer).promise.then(function (pdfDoc) {
                    pdfDoc.getPage(1).then(function (page) {
                        var scale = 0.5;
                        var viewport = page.getViewport({ scale: scale });

                        var context = canvas.getContext('2d');
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;

                        page.render({
                            canvasContext: context,
                            viewport: viewport
                        });
                    });
                });
            };

            fileReader.readAsArrayBuffer(file);
        }

        function openPdfModal(file) {
            var pdfViewer = document.getElementById('pdfViewer');
            pdfViewer.src = URL.createObjectURL(file);
            document.getElementById('pdfModal').style.display = 'block';
        }

        document.getElementById('closeModal').addEventListener('click', function () {
            document.getElementById('pdfModal').style.display = 'none';
        });

        window.onclick = function (event) {
            var modal = document.getElementById('pdfModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        };