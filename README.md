# 🔥 XTREAM SERVER OPENSOURCE 🇧🇷

📅 **Data**: 23/03/2025  
👤 **Dev**: [@FURIA401](https://t.me/FLAVIO401)    
📸 **Screenshots** 
![dashboard do Sistema](https://fxtream.xyz/img/image_2025-03-17_22-12-00.png)
![uploud de m3u](https://fxtream.xyz/img/image_2025-03-16_20-39-23.png)
![categorias](https://fxtream.xyz/img/image_2025-03-17_22-12-32.png)
![Editar canais](https://fxtream.xyz/img/image_2025-03-17_22-13-34.png)
![Editar series](https://fxtream.xyz/img/image_2025-03-17_22-18-19.png)
![listar temporadas](https://fxtream.xyz/img/image_2025-03-17_22-18-33.png)
![Listar episodios e editar](https://fxtream.xyz/img/image_2025-03-17_22-19-52.png)
![deletar episodio](https://fxtream.xyz/img/image_2025-03-17_22-21-39.png)


---

### 🌐 TECNOLOGIAS UTILIZADAS  
🚀 **Desenvolvido com**:  
-  PHP 8  
-  Bootstrap 5  
-  JavaScript  

🔒 **Compatível com**:  
- HTTP & HTTPS  

---

### ✔️ **RECURSOS PRINCIPAIS**

- **Gerenciamento de Clientes e Testes** ✅
- **Gerenciamento de Revendedores**  
  - Níveis: Master, Revenda, Sub-revenda  
- **Sistema de Gerenciamento de Conteúdos**  
  - Categorias: Criar ✅ | Editar ✅ | Excluir ✅  
  - Canais: Criar ✅ | Editar ✅ | Excluir ✅  
  - Filmes: Criar ✅ | Editar ✅ | Excluir ✅  
  - Séries: Criar ✅ | Editar ✅ | Excluir ✅  
  - Temporadas: Criar ✅ | Editar ❌ | Excluir ✅  
  - Episódios: Criar ✅ | Editar ✅ | Excluir ✅  

- **Sistema de Upload Inteligente**  
  - Upload via Arquivo ou Link  
  - Super rápido (depende da hospedagem)  
  - Upload inteligente: Criação automática de categorias para canais, filmes e séries  
  - Sem limite de tamanho de arquivo

- **Sistema de EPG**  
  - No momento do Upload, é verificado se a lista possui EPG e é implementado aos canais  
  - Não compatível com listas de canais por IP, apenas listas Xtream Codes ou Xtream UI, pois elas possuem EPG embutido.

- **Ferramentas Avançadas**  
  - Excluir todo o conteúdo  
  - Excluir apenas Canais (inclui categorias relacionadas)  
  - Excluir apenas Filmes (inclui categorias relacionadas)  
  - Excluir apenas Séries (inclui temporadas, episódios e categorias)  

- **Sistema de Categorias Inteligente**  
  - Conteúdo adulto: Alterar categoria converte automaticamente todos os canais, filmes e séries para adulto  
  - Exclusão de categoria: Todo o conteúdo vinculado (canais, filmes e séries) será removido  

- **Sistema de Ocultação de Fonte**  
  - Proteção extra para esconder a origem do conteúdo  

---

### 📢 **PRÓXIMA ATUALIZAÇÃO**  
- 🔒 Sistema de Bloqueio de Conexão (em desenvolvimento)  
- 🔒 Upload pelo padrão Xtream Codes (não iniciado no momento)  
- 🔒 TMDB (Em desenvolvimento)  
- 🔒 Sistema de clientes e testes online (em desenvolvimento)  

---

### 📱 **APLICATIVOS TESTADOS**  

#### 📲 **Dispositivos Android**  
- IBO  
- XCIPTV  
- SMARTERS PLAYER  
- CAP PLAYER  
- GSE  

#### 📺 **Smart TVs (Samsung & LG)**  
- SmartOne  
- DreamTV  
- QuickPlayer  
- LazerPlay  
- Clouddy (Necessário baixar o M3U e importar manualmente)  

#### 🍏 **Dispositivos iPhone**  
- Smarters Lite  
- GSE  

#### 📺 **Smarters Pro (Smart TVs)**  
- Funciona, mas exige um procedimento para burlar o app  

---

### 💬 **COMUNIDADE & SUPORTE**  
Participe do grupo do canal para discutir melhorias, sugerir novos recursos e ajudar a corrigir possíveis erros encontrados!

🔗 **Link do Grupo**: [@xtreamserveropengrupo](https://t.me/xtreamserveropengrupo)  
🔗 **Link do Canal**: [@xtreamserveropen](https://t.me/xtreamserveropen)  

---

### 💰 **QUER APOIAR O PROJETO?**  
Se deseja contribuir financeiramente para o desenvolvimento, faça uma doação via PIX!  
💸 **Pix chave aleatória**: [877eac58-cedc-400b-b91f-db8681ac8923]
💸 **Com cartao de credito**: [Mercado PAGO](https://link.mercadopago.com.br/furiaplayer)

---

📌 **Um sistema completo para gerenciamento de IPTV!**  

---

# 🚀 **Tutorial de Instalação do XTREAM SERVER OPENSOURCE** 🇧🇷

Este tutorial irá guiá-lo pelo processo de instalação e configuração do **XTREAM SERVER OPENSOURCE** no seu servidor.

---
### Passo 1: **Preparar o Ambiente**

Certifique-se de ter um servidor com as seguintes tecnologias instaladas:

- **Apache**
- **PHP 8.x**
- **MySQL ou MariaDB**


### Passo 2: Importar o Banco de Dados
Localize o arquivo SQL do banco de dados dentro da pasta /Banco de dados.

### Passo 3: Configurar os Detalhes do Banco de Dados
Acesse o arquivo de configuração do banco de dados:

- Navegue até a pasta /api/controles/ no diretório onde você extraiu os arquivos.
Abra o arquivo db.php com um editor de texto.

- Configure os dados do banco de dados: No arquivo db.php, você verá uma função para definir os dados de conexão com o banco de dados. Edite os seguintes campos para refletir as configurações do seu banco de dados:

- $endereco = "localhost";
- $banco = "xtserveropensource"; // nome do seu banco de dados
- $dbusuario = "root"; // usuario do seu banco de dados
- $dbsenha = ""; // senha do seu banco de dados

### dados de acesso

- usuario: admin
- senha: admin