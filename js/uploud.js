let isProcessing = false;
let add_urls = 0; 
let add_epg = false; 
let urls_processadas = 0; 
let urlCount = 0; 
let startTime = null; 
function updateTimeEstimates(averageRequestTime) {
    const batchSize = 500; 
    const batchTime = averageRequestTime; 

    const totalBatches = Math.ceil(urlCount / batchSize);
    const totalEstimatedTime = totalBatches * batchTime; 

    if (!startTime) startTime = new Date(); 
    const currentTime = new Date();
    const elapsedTime = (currentTime - startTime) / 1000; 

    const completedBatches = Math.floor(urls_processadas / batchSize);
    const remainingBatches = totalBatches - completedBatches;
    const remainingTime = remainingBatches * batchTime;

    const formatTime = (seconds) => {
        const hrs = Math.floor(seconds / 3600);
        const mins = Math.floor((seconds % 3600) / 60);
        const secs = Math.floor(seconds % 60);
        return `${hrs.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    };

    const tempo_Total_Estimado = document.getElementById("tempo_Total_Estimado");
    const tempo_Decorrido = document.getElementById("tempo_Decorrido");
    const tempo_Restante = document.getElementById("tempo_Restante");
    tempo_Total_Estimado.innerHTML = `${formatTime(totalEstimatedTime)}`;
    tempo_Decorrido.innerHTML = `${formatTime(elapsedTime)}`;
    tempo_Restante.innerHTML = `${formatTime(remainingTime)}`;
}

let totalRequests = 0; 
    let adicionando = 0; 
    let canais_adicionando = 0; 
    let filmes_adicionando = 0; 
    let series_adicionando = 0; 
    let epg_adicionando = 0; 

    let temporadas_adicionando = 0; 
    let episodios_adicionando = 0; 
    let exitente = 0; 
    let Erro = 0; 
    let paused = false; 

    const pauseBtn = document.getElementById('pauseBtn');
    const resumeBtn = document.getElementById('resumeBtn');

    pauseBtn.addEventListener('click', () => {
        paused = true;
        pauseBtn.disabled = true;
        resumeBtn.disabled = false;
    });

    resumeBtn.addEventListener('click', () => {
        paused = false;
        pauseBtn.disabled = false;
        resumeBtn.disabled = true;
        continueProcess(); 
    });

    function divideM3UContent(content) {
        const lines = content.split('\n');
        const parts = [];
        let currentPart = "#EXTM3U\n"; 
        let currentSize = 0;

        const maxPartSize = 200 * 1024 * 1024;

        lines.forEach(line => {
            currentPart += line + "\n"; 
            currentSize += line.length + 1; 

            if (/^(http|rtmp)/i.test(line.trim())) {

                if (currentSize >= maxPartSize) {
                    parts.push(currentPart); 
                    currentPart = "#EXTM3U\n"; 
                    currentSize = 0;
                }
            }
        });

        if (currentSize > 0) {
            parts.push(currentPart);
        }

        return parts;
    }

   function processFileParts(parts, callback) {

        let progress = 0;
        const totalParts = parts.length;

        const interval = setInterval(() => {
            progress += 1;
            const progressPercent = (progress / totalParts) * 100;
            document.getElementById("progressBar").style.width = `${progressPercent}%`;

            if (progress >= totalParts) {
                clearInterval(interval);
                callback(); 
            }
        }, 100); 
    }
    let currentBlockIndex = 0; 
    let currentPartIndex = 0; 
    let filePartsGlobal = []; 
    let fileBlocksGlobal = []; 
    function updatePartProgress(currentPart, totalParts) {

        const progressPercent = (currentPart / totalParts) * 100;
        document.getElementById("partProgressBar").style.width = `${progressPercent}%`;

        document.getElementById("partProgressText").textContent = `Parte atual: ${currentPart} de ${totalParts}`;
    }

    function continueProcess() {
        if (paused) return; 
        sendContentBlocks(filePartsGlobal); 
    }
    let totalRequestTime = 0; 
    async function processBlock(block) {
        const startRequestTime = new Date().getTime(); 
        const params = new URLSearchParams();
        block.forEach((item, index) => {
            params.append(`block[${index}][tvgName]`, item.tvgName);
            params.append(`block[${index}][tvgId]`, item.tvgId);
            params.append(`block[${index}][tvgLogo]`, item.tvgLogo);
            params.append(`block[${index}][groupTitle]`, item.groupTitle);
            params.append(`block[${index}][url]`, item.url);
            params.append(`block[${index}][channelName]`, item.channelName);
        });

        try {
            const response = await fetch('./api/controles/importar-arquivo-m3u.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: params.toString()
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            totalRequests++;
            const data = await response.json();

            if (data.results && typeof data.results === 'object') {
                const {
                    success,
                    exists,
                    error,
                    urls,
                    movie,
                    series,
                    live,
                    episodios,
                    temporadas
                } = data.results;

                success.forEach(result => {

                    adicionando++;
                });
                if (urls) {

                    add_urls += urls; 
                }
                if (movie) {

                    if (live == 0 && add_epg == false) {
                        await carregarEPG();
                        add_epg = true;
                    }
                    filmes_adicionando += movie; 
                    urls_processadas += movie; 
                }
                if (series) {
                    if (live == 0 && add_epg == false) {
                        await carregarEPG();
                        add_epg = true;
                    }

                    series_adicionando += series; 
                }
                if (live) {

                    canais_adicionando += live; 
                    urls_processadas += live; 
                }
                if (episodios) {
                    if (live == 0 && add_epg == false) {
                        await carregarEPG();
                        add_epg = true;
                    }

                    episodios_adicionando += episodios; 
                    urls_processadas += episodios; 
                }
                if (temporadas) {

                    temporadas_adicionando += temporadas; 
                }

                if (exists) {

                    exitente += exists; 
                    urls_processadas += exists; 
                    add_urls += exists; 
                }

                error.forEach(result => {

                    Erro++;
                    urls_processadas++; 
                });
            } else {

            }
        } catch (error) {
            console.error('Error:', error);
        }
        const endRequestTime = new Date().getTime(); 
        const requestDuration = (endRequestTime - startRequestTime) / 1000; 

        totalRequestTime += requestDuration;

        const averageRequestTime = totalRequestTime / totalRequests;

        updateTimeEstimates(averageRequestTime); 
        updateProgressBar();
        updateStatus();
    }
function updateStatus() {
        document.getElementById('totalRequests').textContent = totalRequests;
        document.getElementById('canais').textContent = canais_adicionando;
        document.getElementById('filmes').textContent = filmes_adicionando;
        document.getElementById('series_adicionando').textContent = series_adicionando;
        document.getElementById('epg_adicionando').textContent = epg_adicionando;
        document.getElementById('add_urls').textContent = add_urls;
        document.getElementById('episodios_adicionando').textContent = episodios_adicionando;
        document.getElementById('temporadas_adicionando').textContent = temporadas_adicionando;
        document.getElementById('exitente').textContent = exitente;
        document.getElementById('Erro').textContent = Erro;
    }

    function chunkArray(arr, size) {
        const chunks = [];
        for (let i = 0; i < arr.length; i += size) {
            chunks.push(arr.slice(i, i + size));
        }
        return chunks;
    }

function extractAttribute(line, attribute) {
        const regex = new RegExp(`${attribute}="([^"]+)"`);
        const match = line.match(regex);
        return match ? match[1] : "";

    }

function isValidUrl(line) {
        const regex = /(https?|rtsp|ftp):\/\/[^\s]+/g;
        return regex.test(line);
    }

    function processLocalFile(file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const fileContent = event.target.result;

            const fileParts = divideM3UContent(fileContent);

            processFileParts(fileParts, function() {
                sendContentBlocks(fileParts); 
            });
            if (firstModal) {
                let btn_openFirstModal = document.getElementById('openFirstModal');
                btn_openFirstModal.disabled = true;
                btn_openFirstModal.style.display = 'none'; 
                firstModal.hide();
            }else{
                console.error('firstModal: erro');
            }
        };
        reader.readAsText(file);
    }

function processFileFromUrl(url) {

        fetch(url)
            .then(response => response.text())
            .then(fileContent => {
                const fileParts = divideM3UContent(fileContent);

                processFileParts(fileParts, function() {
                    sendContentBlocks(fileParts); 
                });
                if (firstModal) {
                    let btn_openFirstModal = document.getElementById('openFirstModal');
                    btn_openFirstModal.disabled = true;
                    btn_openFirstModal.style.display = 'none'; 
                    firstModal.hide();
                }else{
                    console.error('firstModal: erro');
                }
            })
            .catch(error => {
                resultElement.textContent = `Erro ao carregar o arquivo da URL: ${error.message}`;
            });
    }

async function limparcahe() {
    try {

        const response = await fetch('./api/limpar-cache.php');

        if (!response.ok) {
            throw new Error('Erro ao tentar gerar as séries.');
        }

        const data = await response.json();

        if (data.status === "continua") {

        } else if (data.status === "finalizado") {

        } else {

        }
    } catch (error) {
        console.error('Erro:', error);
    }
}

function updateProgressBar() {
    if (urlCount > 0) { 
        const progressPercent = (urls_processadas / urlCount) * 100;
        const partProgressBar = document.getElementById("partProgressBar");
        partProgressBar.style.width = `${progressPercent}%`;
        partProgressBar.textContent = `${progressPercent.toFixed(2)}%`; 
    }
}

function mostrarModalFinalizacao() {
    const modalHtml = `
        <div class="modal fade" id="finalizacaoModal" tabindex="-1" aria-labelledby="finalizacaoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="finalizacaoModalLabel">Processo Concluído</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        O processo de geração de séries foi concluído. Não há mais séries a serem geradas.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);

    const modal = new bootstrap.Modal(document.getElementById('finalizacaoModal'));
    modal.show();
}