(() => {
    let qnturl = 150;
    let e = !1,
        t = 0,
        o = !1,
        n = 0,
        a = 0,
        r = null,
        i = 0,
        s = 0,
        l = 0,
        d = 0,
        c = 0,
        m = 0,
        u = 0,
        p = 0,
        g = 0,
        f = !1;
    const h = document.getElementById("pauseBtn"),
        y = document.getElementById("resumeBtn");

    function E(e) {
        const t = e.split("\n"),
            o = [];
        let n = "#EXTM3U\n",
            a = 0;
        return t.forEach((e => {
            n += e + "\n", a += e.length + 1, /^(http|rtmp)/i.test(e.trim()) && a >= 209715200 && (o.push(n), n = "#EXTM3U\n", a = 0)
        })), a > 0 && o.push(n), o
    }

    function v(e, t) {
        let o = 0;
        const n = e.length,
            a = setInterval((() => {
                o += 1;
                const e = o / n * 100;
                document.getElementById("progressBar").style.width = `${e}%`, o >= n && (clearInterval(a), t())
            }), 100)
    }
    h.addEventListener("click", (() => {
        f = !0, h.disabled = !0, y.disabled = !1
    })), y.addEventListener("click", (() => {
        f = !1, h.disabled = !1, y.disabled = !0, f || O(I)
    }));
    let w = 0,
        B = 0,
        I = [],
        b = [],
        x = 0;
    async function L(e) {
        const c = (new Date).getTime(),
            f = new URLSearchParams;
        e.forEach(((e, t) => {
            f.append(`block[${t}][tvgName]`, e.tvgName), f.append(`block[${t}][tvgId]`, e.tvgId), f.append(`block[${t}][tvgLogo]`, e.tvgLogo), f.append(`block[${t}][groupTitle]`, e.groupTitle), f.append(`block[${t}][url]`, e.url), f.append(`block[${t}][channelName]`, e.channelName)
        }));
        try {
            const e = await fetch("./api/controles/importar-arquivo-m3u.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: f.toString()
            });
            if (!e.ok) throw new Error("Network response was not ok");
            i++;
            const a = await e.json();
            if (a.results && "object" == typeof a.results) {
                const {
                    success: e,
                    exists: r,
                    error: i,
                    urls: c,
                    movie: f,
                    series: h,
                    live: y,
                    episodios: E,
                    temporadas: v
                } = a.results;
                e.forEach((e => {})), c && (t += c), f && (0 == y && 0 == o && (await H(), o = !0), l += f, n += f), h && (0 == y && 0 == o && (await H(), o = !0), d += h), y && (s += y, n += y), E && (0 == y && 0 == o && (await H(), o = !0), u += E, n += E), v && (m += v), r && (p += r, n += r, t += r), i.forEach((e => {
                    g++, n++
                }))
            }
        } catch (e) {
            console.error("Error:", e)
        }
        const h = (new Date).getTime();
        x += (h - c) / 1e3,
            function(e) {
                const t = e,
                    o = Math.ceil(a / qnturl),
                    i = o * t;
                r || (r = new Date);
                const s = (new Date - r) / 1e3,
                    l = (o - Math.floor(n / qnturl)) * t,
                    d = e => {
                        const t = Math.floor(e / 3600),
                            o = Math.floor(e % 3600 / 60),
                            n = Math.floor(e % 60);
                        return `${t.toString().padStart(2,"0")}:${o.toString().padStart(2,"0")}:${n.toString().padStart(2,"0")}`
                    },
                    c = document.getElementById("tempo_Total_Estimado"),
                    m = document.getElementById("tempo_Decorrido"),
                    u = document.getElementById("tempo_Restante");
                c.innerHTML = `${d(i)}`, m.innerHTML = `${d(s)}`, u.innerHTML = `${d(l)}`
            }(x / i),
            function() {
                if (a > 0) {
                    const e = n / a * 100,
                        t = document.getElementById("partProgressBar");
                    t.style.width = `${e}%`, t.textContent = `${e.toFixed(2)}%`
                }
            }(), k()
    }

    function k() {
        document.getElementById("totalRequests").textContent = i, document.getElementById("canais").textContent = s, document.getElementById("filmes").textContent = l, document.getElementById("series_adicionando").textContent = d, document.getElementById("epg_adicionando").textContent = c, document.getElementById("add_urls").textContent = t, document.getElementById("episodios_adicionando").textContent = u, document.getElementById("temporadas_adicionando").textContent = m, document.getElementById("exitente").textContent = p, document.getElementById("Erro").textContent = g
    }

    function T(e, t) {
        const o = [];
        for (let n = 0; n < e.length; n += t) o.push(e.slice(n, n + t));
        return o
    }

    function M(e, t) {
        const o = new RegExp(`${t}="([^"]+)"`),
            n = e.match(o);
        return n ? n[1] : ""
    }

    function $(e) {
        return /(https?|rtsp|ftp):\/\/[^\s]+/g.test(e)
    }

    function F(e) {
        const t = new FileReader;
        t.onload = function(e) {
            const t = E(e.target.result);
            if (v(t, (function() {
                    O(t)
                })), _) {
                let e = document.getElementById("openFirstModal");
                e.disabled = !0, e.style.display = "none", _.hide()
            } else console.error("firstModal: erro")
        }, t.readAsText(e)
    }

    function S(e) {
        const t = function(e) {
            return /^https?:\/\//i.test(e) ? "https:" === window.location.protocol && e.startsWith("http:") ? e.replace("http:", "https:") : e : "https://" + e
        }(e);
        fetch(t).then((e => e.text())).then((e => {
            const t = E(e);
            if (v(t, (function() {
                    O(t)
                })), _) {
                let e = document.getElementById("openFirstModal");
                e.disabled = !0, e.style.display = "none", _.hide()
            } else console.error("firstModal: erro")
        })).catch((e => {
            let t = document.getElementById("result");
            q.disabled = !1, t.textContent = "Não foi possível baixar o arquivo da URL. Verifique a URL e tente novamente ou baixe o arquivo e faça o uploud por arquivo.", Swal.fire({
                title: "Erro ao baixar",
                text: "Não foi possível baixar o arquivo da URL. Verifique a URL e tente novamente.",
                icon: "error",
                confirmButtonText: "OK"
            })
        }))
    }
    async function C() {
        try {
            const e = await fetch("./api/limpar-cache.php");
            if (!e.ok) throw new Error("Erro ao tentar gerar as séries.");
            const t = await e.json();
            "continua" === t.status || t.status
        } catch (e) {
            console.error("Erro:", e)
        }
    }
    const A = document.getElementById("btnSelectFile"),
        N = document.getElementById("m3uFile"),
        q = document.getElementById("processFileBtn");
    A.addEventListener("click", (() => {
        N.click()
    })), N.addEventListener("change", (e => {
        q.click(), _.hide()
    }));
    const P = document.getElementById("dropArea"),
        R = document.getElementById("dropArea2");
    document.body.addEventListener("dragenter", (() => {
        P.style.display = "block", R.style.display = "none"
    })), document.addEventListener("dragleave", (e => {
        null === e.relatedTarget && (P.style.display = "none", R.style.display = "block")
    })), document.body.addEventListener("dragover", (e => {
        e.preventDefault()
    })), document.body.addEventListener("drop", (e => {
        e.preventDefault(), P.style.display = "none", R.style.display = "block", q.click(), _.hide()
    })), R.addEventListener("dragover", (e => {
        e.preventDefault(), P.style.display = "block", R.style.display = "none"
    })), P.addEventListener("dragover", (e => {
        e.preventDefault()
    })), P.addEventListener("dragleave", (() => {})), P.addEventListener("drop", (e => {
        e.preventDefault(), P.style.display = "none", R.style.display = "block";
        const t = e.dataTransfer.files;
        if (t.length > 0) {
            const e = t[0];
            ("application/x-mpegURL" === e.type || e.name.endsWith(".m3u")) && (F(e), q.click(), _.hide())
        }
    }));
    const _ = new bootstrap.Modal(document.getElementById("modal_arquivo")),
        D = new bootstrap.Modal(document.getElementById("modal_url"));

    function U(e) {
        const t = e.split("\n");
        let o = [],
            n = !1;
        for (let e = 0; e < t.length; e++) {
            const r = t[e].trim();
            if (r.startsWith("#EXTINF")) {
                n = !0;
                const i = M(r, "group-title"),
                    s = M(r, "tvg-logo"),
                    l = M(r, "tvg-name"),
                    d = M(r, "tvg-id");
                let c = "Nome não disponível";
                const m = r.indexOf("group-title");
                if (-1 !== m) {
                    const e = r.slice(m),
                        t = e.indexOf(","); - 1 !== t && (c = e.slice(t + 1).trim())
                }
                const u = t[e + 1]?.trim();
                u && $(u) && (o.push({
                    url: u,
                    groupTitle: i,
                    tvgLogo: s,
                    tvgName: l,
                    tvgId: d,
                    channelName: c
                }), a++), n = !1
            }
        }
        return document.getElementById("result").textContent = "Arquivo baixado com sucesso!", o
    }
    async function O(e) {
        D.hide();
        for (let t = 0; t < e.length; t++) {
            const n = T(U(e[t]), qnturl);
            b = n;
            for (let e = 0; e < n.length; e++) {
                for (; f;) await new Promise((e => setTimeout(e, 500)));
                await L(n[e])
            }
            B = t + 1, w = 0, 0 == o && (await H(), o = !0), new bootstrap.Modal(document.getElementById("completionModal")).show(), document.getElementById("controles").style.display = "none", C(), q.disabled = !1
        }
    }
    async function H() {
        try {
            Swal.fire({
                title: "Verificando EPG",
                html: "Aguarde enquanto verificamos o arquivo EPG...",
                icon: "info",
                showConfirmButton: !1,
                allowOutsideClick: !1,
                timerProgressBar: !1,
                timer: 5e4
            });
            const e = await fetch("./xmltv.php?epg");
            if (!e.ok) throw new Error(`Erro no servidor: ${e.status} - ${e.statusText}`);
            const t = await e.text();
            if (!t.startsWith("<?xml")) throw new Error("O arquivo retornado não é um XML válido");
            const o = (new DOMParser).parseFromString(t, "text/xml");
            if (o.getElementsByTagName("parsererror").length > 0) throw new Error("XML malformado ou inválido");
            const n = o.getElementsByTagName("channel");
            if (0 === n.length) throw new Error("Nenhum canal encontrado no arquivo EPG");
            let a = [];
            for (let e of n) {
                let t = e.getAttribute("id"),
                    o = e.getElementsByTagName("display-name")[0]?.textContent || "Sem Nome";
                t && a.push({
                    id: t,
                    nome: o
                })
            }
            await Swal.update({
                title: "Processando...",
                html: `Encontrados ${a.length} canais<br>Atualizando base de dados...`
            }), await async function(e) {
                let t = e.length,
                    o = Math.ceil(t / 500);
                for (let t = 0; t < o; t++) {
                    let n = 500 * t,
                        a = n + 500,
                        r = e.slice(n, a);
                    await j(r, t + 1, o)
                }
                SweetAlert3("atribudos todos epg aos canais", "info", "5000")
            }(a), await Swal.fire({
                title: "Sucesso!",
                text: `EPG atualizado com ${a.length} canais`,
                icon: "success",
                timer: 5e3
            })
        } catch (e) {
            await Swal.close(), await Swal.fire({
                title: "Erro!",
                html: "<small>Não foi possivel adicionar o epg</small>",
                icon: "error",
                confirmButtonText: "OK",
                timer: 8e3
            }), console.error("Erro detalhado:", e)
        }
    }
    async function j(e, t, o) {
        const n = new FormData;
        e.forEach(((e, t) => {
            n.append(`epg[${t}][id]`, e.id), n.append(`epg[${t}][nome]`, e.nome)
        }));
        try {
            const e = await fetch("./api/controles/importar-arquivo-m3u.php", {
                method: "POST",
                body: n
            });
            if (!e.ok) throw new Error(`Erro HTTP: ${e.status}`);
            const a = await e.json();
            if (a.results && "object" == typeof a.results) {
                const e = a.results,
                    t = Array.isArray(e.success) ? e.success : [],
                    o = Array.isArray(e.exists) ? e.exists : [],
                    n = Array.isArray(e.error) ? e.error : [];
                t.forEach((e => {})), o.forEach((e => {})), n.forEach((e => {})), "number" == typeof e.epg && (c += e.epg, await Swal.update({
                    title: "Processando...",
                    html: `Adcionados ${e.epg} canais<br>Aguade atualizando base de dados...`
                }))
            }
            console.log(`Bloco ${t}/${o} enviado!`, a), k()
        } catch (e) {
            console.error("Erro:", e)
        }
    }! function() {
        const e = document.getElementById("autor1"),
            t = document.getElementById("autor2");
        e && (e.innerHTML = '<a href="https://t.me/xtreamserveropengrupo" target="_blank">Feito por: 💡 FURIA/FLAVIO</a>'), t && (t.innerHTML = '<a href="https://t.me/xtreamserveropengrupo" target="_blank">Feito por: 💡 FURIA/FLAVIO</a>')
    }(), _.show(), document.getElementById("openFirstModal").addEventListener("click", (() => {
        _.show()
    })), document.getElementById("openSecondModal").addEventListener("click", (() => {
        _.hide(), D.show()
    })), document.getElementById("backToFirstModal").addEventListener("click", (() => {
        D.hide(), _.show()
    })), document.getElementById("processFileBtn").addEventListener("click", (function() {
        e && (e = !1), C();
        const t = document.getElementById("processFileBtn"),
            o = document.getElementById("m3uFile"),
            n = document.getElementById("m3uUrl");
        let a = document.getElementById("result"),
            r = document.getElementById("partCount");
        document.getElementById("progressBar"), document.getElementById("urlsList"), isNaN(200) ? a.textContent = "Por favor, insira um valor válido em MB para dividir." : (r.textContent = "", o.files.length || n.value ? o.files.length ? (F(o.files[0]), e = !0) : (S(n.value), t.disabled = !0, a.textContent = "Baixando arquivo arguarde pode demorar um pouco...", e = !0) : a.textContent = "Por favor, selecione um arquivo .m3u ou forneça uma URL.")
    }))
})();
