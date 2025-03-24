const btnSelectFile = document.getElementById('btnSelectFile');
const fileInput = document.getElementById('m3uFile');
const processFileBtn = document.getElementById('processFileBtn');

  btnSelectFile.addEventListener('click', () => {
    fileInput.click(); 
  });

  fileInput.addEventListener('change', (event) => {
    processFileBtn.click();
    firstModal.hide();

  });

    const dropArea = document.getElementById('dropArea');
    const dropArea2 = document.getElementById('dropArea2');

        document.body.addEventListener('dragenter', () => {
            dropArea.style.display = 'block'; 
            dropArea2.style.display = 'none'; 
        });

        document.addEventListener('dragleave', (e) => {
            if (e.relatedTarget === null) { 
                dropArea.style.display = 'none'; 
                dropArea2.style.display = 'block'; 

            }
        });

        document.body.addEventListener('dragover', (e) => {
            e.preventDefault(); 
        });

        document.body.addEventListener('drop', (e) => {
            e.preventDefault(); 
            dropArea.style.display = 'none'; 
            dropArea2.style.display = 'block'; 

            processFileBtn.click();
            firstModal.hide();
        });

        dropArea2.addEventListener('dragover', (e) => {
            e.preventDefault(); 
            dropArea.style.display = 'block'; 
            dropArea2.style.display = 'none'; 
        });

        dropArea.addEventListener('dragover', (e) => {
            e.preventDefault(); 
        });

        dropArea.addEventListener('dragleave', () => {

        });

        dropArea.addEventListener('drop', (e) => {
            e.preventDefault(); 
            dropArea.style.display = 'none'; 
            dropArea2.style.display = 'block'; 

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0]; 
                if (file.type === "application/x-mpegURL" || file.name.endsWith('.m3u')) {
                    processLocalFile(file); 
                    processFileBtn.click();
                  firstModal.hide();
                } else {

                }
            }
        });
    const firstModal = new bootstrap.Modal(document.getElementById('modal_arquivo'));
    const secondModal = new bootstrap.Modal(document.getElementById('modal_url'));
    firstModal.show();

    document.getElementById('openFirstModal').addEventListener('click', () => {
      firstModal.show();
    });

    document.getElementById('openSecondModal').addEventListener('click', () => {
      firstModal.hide();
      secondModal.show();
    });

    document.getElementById('backToFirstModal').addEventListener('click', () => {
      secondModal.hide();
      firstModal.show();
    });

document.getElementById("processFileBtn").addEventListener("click", function() {

    if (isProcessing) {

        isProcessing = false; 
        console.log('aqui seria reiniciado');

    }
    limparcahe();

    const processFileBtn = document.getElementById("processFileBtn");

    const fileInput = document.getElementById("m3uFile");
    const urlInput = document.getElementById("m3uUrl");
    let resultElement = document.getElementById("result");
    let partCountElement = document.getElementById("partCount");
    const progressBar = document.getElementById("progressBar");
    let urlsListElement = document.getElementById("urlsList");

    const valor_mb = 200; 

    if (isNaN(valor_mb) || valor_mb <= 0) {
        resultElement.textContent = "Por favor, insira um valor válido em MB para dividir.";
        return;
    }

    partCountElement.textContent = '';

    if (!fileInput.files.length && !urlInput.value) {
        resultElement.textContent = "Por favor, selecione um arquivo .m3u ou forneça uma URL.";
        return;
    }

    if (fileInput.files.length) {

        const file = fileInput.files[0];
        processLocalFile(file);
        isProcessing = true;
    } else {
        const fileUrl = urlInput.value;
        processFileFromUrl(fileUrl);
        processFileBtn.disabled = true;
        resultElement.textContent = 'Baixando arquivo arguarde pode demorar um pouco...';
        isProcessing = true;
    }

}); 
function extractUrlsFromM3U(content) {
        const lines = content.split('\n');
        let urls = [];
        let isAfterExtinf = false;

        for (let i = 0; i < lines.length; i++) {
            const line = lines[i].trim();

            if (line.startsWith('#EXTINF')) {
                isAfterExtinf = true; 

                const groupTitle = extractAttribute(line, 'group-title');
                const tvgLogo = extractAttribute(line, 'tvg-logo');
                const tvgName = extractAttribute(line, 'tvg-name');
                const tvgId = extractAttribute(line, 'tvg-id');

                let nameAfterComma = "Nome não disponível";

                const groupTitleIndex = line.indexOf('group-title');
                if (groupTitleIndex !== -1) {
                    const contentAfterGroupTitle = line.slice(groupTitleIndex); 
                    const commaAfterGroupTitle = contentAfterGroupTitle.indexOf(','); 
                    if (commaAfterGroupTitle !== -1) {
                        nameAfterComma = contentAfterGroupTitle.slice(commaAfterGroupTitle + 1).trim(); 
                    }
                }

                const urlLine = lines[i + 1]?.trim();
                if (urlLine && isValidUrl(urlLine)) {
                    urls.push({
                        url: urlLine,
                        groupTitle: groupTitle,
                        tvgLogo: tvgLogo,
                        tvgName: tvgName,
                        tvgId: tvgId,
                        channelName: nameAfterComma 
                    });
                    urlCount++; 
                }

                isAfterExtinf = false;
            }
        }
        let resultElement = document.getElementById("result");
        resultElement.textContent = 'Arquivo baixado com sucesso!';

        return urls;
    }

    async function sendContentBlocks(fileParts) {

        secondModal.hide();

        for (let partIndex = 0; partIndex < fileParts.length; partIndex++) {

            const part = fileParts[partIndex];
            const urls = extractUrlsFromM3U(part);
            const blocks = chunkArray(urls, 500); 

            fileBlocksGlobal = blocks; 

            for (let blockIndex = 0; blockIndex < blocks.length; blockIndex++) {
                while (paused) {
                    await new Promise(resolve => setTimeout(resolve, 500)); 
                }
                await processBlock(blocks[blockIndex]);
            }
            currentPartIndex = partIndex + 1; 
            currentBlockIndex = 0; 

            if (add_epg == false) {
                await carregarEPG();
                add_epg = true;
            }
            const completionModal = new bootstrap.Modal(document.getElementById('completionModal'));
            completionModal.show();
            let controles = document.getElementById('controles');
            controles.style.display = 'none'; 

            limparcahe();
            processFileBtn.disabled = false;
        }

    }
async function carregarEPG() {
    try {

        const loadingAlert = Swal.fire({
            title: 'Verificando EPG',
            html: 'Aguarde enquanto verificamos o arquivo EPG...',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            timerProgressBar: false,
            timer: 50000
        });

        const respostaEpg = await fetch('./xmltv.php?epg');

        if (!respostaEpg.ok) {
            throw new Error(`Erro no servidor: ${respostaEpg.status} - ${respostaEpg.statusText}`);
        }

        const xmlText = await respostaEpg.text();
        if (!xmlText.startsWith('<?xml')) {
            throw new Error('O arquivo retornado não é um XML válido');
        }

        const parser = new DOMParser();
        const xmlDoc = parser.parseFromString(xmlText, "text/xml");

        const parserError = xmlDoc.getElementsByTagName('parsererror');
        if (parserError.length > 0) {
            throw new Error('XML malformado ou inválido');
        }

        const canais = xmlDoc.getElementsByTagName("channel");
        if (canais.length === 0) {
            throw new Error('Nenhum canal encontrado no arquivo EPG');
        }

        let listaCanais = [];
        for (let canal of canais) {
            let id = canal.getAttribute("id");
            let nome = canal.getElementsByTagName("display-name")[0]?.textContent || "Sem Nome";
            if (id) listaCanais.push({ id, nome });
        }

        await Swal.update({
            title: 'Processando...',
            html: `Encontrados ${listaCanais.length} canais<br>Atualizando base de dados...`
        });

        await enviarEmBlocos(listaCanais, 500);

        await Swal.fire({
            title: 'Sucesso!',
            text: `EPG atualizado com ${listaCanais.length} canais`,
            icon: 'success',
            timer: 5000
        });

    } catch (erro) {

        await Swal.close();
        await Swal.fire({
            title: 'Erro!',
            html: `<small>Não foi possivel adicionar o epg</small>`,
            icon: 'error',
            confirmButtonText: 'OK',
            timer: 8000
        });
        console.error("Erro detalhado:", erro);
    }
}

async function enviarEmBlocos(listaCanais, tamanhoBloco) {
    let total = listaCanais.length;
    let totalBlocos = Math.ceil(total / tamanhoBloco);

    for (let i = 0; i < totalBlocos; i++) {
        let inicio = i * tamanhoBloco;
        let fim = inicio + tamanhoBloco;
        let bloco = listaCanais.slice(inicio, fim);

        await enviarDados(bloco, i + 1, totalBlocos);
    }
    SweetAlert3("atribudos todos epg aos canais", 'info', "5000");
}

async function enviarDados(bloco, numBloco, totalBlocos) {
    const formData = new FormData();

    bloco.forEach((canal, index) => {
        formData.append(`epg[${index}][id]`, canal.id);
        formData.append(`epg[${index}][nome]`, canal.nome);
    });

    try {
        const response = await fetch('./api/controles/importar-arquivo-m3u.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status}`);
        }

        const data = await response.json(); 

        if (data.results && typeof data.results === 'object') {
            const results = data.results;

            const success = Array.isArray(results.success) ? results.success : [];
            const exists = Array.isArray(results.exists) ? results.exists : [];
            const error = Array.isArray(results.error) ? results.error : [];

            success.forEach(result => {

            });

            exists.forEach(result => {

            });

            error.forEach(result => {

            });

            if (typeof results.epg === 'number') {
                epg_adicionando += results.epg;

                await Swal.update({
                    title: 'Processando...',
                    html: `Adcionados ${results.epg} canais<br>Aguade atualizando base de dados...`
                });
            }
        }

        console.log(`Bloco ${numBloco}/${totalBlocos} enviado!`, data);

        updateStatus();
    } catch (error) {
        console.error('Erro:', error);

    }
}