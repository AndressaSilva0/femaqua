# 🚀 FEMAQUA API – Laravel 12 + JWT + Swagger

**FEMAQUA** – Ferramentas Maravilhosas Que Adoro – é uma API REST desenvolvida para o desafio técnico da **Biztrip**.  
Essa aplicação permite o cadastro, listagem, filtro e remoção de ferramentas, organizadas por tags, com autenticação via **JWT** e documentação **Swagger**.

🔗 Repositório: [github.com/AndressaSilva0/femaqua](https://github.com/AndressaSilva0/femaqua)

---

## ✅ Requisitos Atendidos

- [x] API REST com Laravel 12
- [x] Porta 3000
- [x] Cadastro de ferramentas com tags
- [x] Listagem com filtro por `?tag=`
- [x] Remoção por ID (restrita a admin)
- [x] Autenticação JWT
- [x] Documentação Swagger completa
- [x] Migrations para banco MySQL

---

## ⚙️ Tecnologias

- **PHP 8.2+**
- **Laravel 12**
- **MySQL**
- **JWT (tymon/jwt-auth)**
- **Swagger (l5-swagger)**
- **Doctrine DBAL**

---

## 💻 Como Rodar o Projeto

### 📦 1. Clone o projeto

```bash
git clone https://github.com/AndressaSilva0/femaqua.git
cd femaqua
```

---
## 📁 2. Instale as dependências

---
### Windows (PowerShell ou CMD)

```bash
 composer install
```
---
### macOS / Linux
```bash
composer install
```
---
## ⚙️ 3. Configure o `.env`

```bash
cp .env.example .env
```
### Edite o arquivo `.env` com suas credenciais do banco de dados:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=femaqua_app
DB_USERNAME=
DB_PASSWORD=
```
---
## 🔑 4. Gere a chave da aplicação
```bash
php artisan key:generate
```
---
## 🔐 5. Gere o token JWT
```bash
php artisan jwt:secret
```
---

## 🧪 Exemplo .env
```bash
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:xxxxx
APP_DEBUG=true
APP_URL=http://localhost:3000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=femaqua_app
DB_USERNAME=
DB_PASSWORD=

JWT_SECRET=chave_gerada
JWT_TTL=60
REFRESH_TTL=20160

CACHE_STORE=database
QUEUE_CONNECTION=database
SESSION_DRIVER=database
```
---
## 🛠️ 6. Rode as migrations
```bash
php artisan migrate
```
---
## 📄 7. Gere a documentação Swagger
```bash
php artisan l5-swagger:generate
```
### Acesse a documentação em:
```bash
http://localhost:3000/api/documentation
```
---
## ▶️ 8. Inicie o servidor
```bash
php artisan serve --port=3000
```
#### ou
```bash
composer serve
```
---
## 🔐 Autenticação

- As rotas exigem autenticação JWT.

- Usuários devem estar logados para cadastrar ferramentas.

- Apenas usuários com tipo `admin` podem deletar ferramentas.

---

## 📡 Rotas da API
### 🔓 Rotas Públicas
| Método | Rota          | Ação                         |
| ------ | ------------- | ---------------------------- |
| POST   | /api/login    | Login e geração de token JWT |
| POST   | /api/register | Registro de novo usuário     |

### 🔐 Rotas Protegidas (JWT)
> É necessário enviar o token no header `Authorization: Bearer {token}`

#### 🔧 Tools (Ferramentas)
| Método | Rota             | Ação                             |
| ------ | ---------------- | -------------------------------- |
| GET    | /api/tools       | Lista todas as ferramentas       |
| GET    | /api/tools?tag=x | Lista ferramentas por tag        |
| POST   | /api/tools       | Cadastra uma nova ferramenta     |
| DELETE | /api/tools/{id}  | Deleta ferramenta (admin apenas) |

#### 👤 Users
| Método | Rota                   | Ação                         |
| ------ | ---------------------- | ---------------------------- |
| GET    | /api/users/list        | Lista todos os usuários      |
| GET    | /api/users/show/{id}   | Mostra dados de um usuário   |
| PUT    | /api/users/update/{id} | Atualiza dados de um usuário |
| DELETE | /api/users/delete/{id} | Deleta um usuário            |

#### 🔐 Autenticação
| Método | Rota        | Ação                                    |
| ------ | ----------- | --------------------------------------- |
| POST   | /api/logout | Logout do usuário autenticado           |
| GET    | /api/me     | Retorna os dados do usuário autenticado |

---
## 💬 Exemplo de Cadastro
### Requisição
```bash
POST /api/tools
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Notion",
  "link": "https://notion.so",
  "description": "All in one workspace",
  "tags": ["organization", "calendar"]
}
```

### Resposta esperada
```bash
{
  "title": "Notion",
  "link": "https://notion.so",
  "description": "All in one workspace",
  "tags": ["organization", "calendar"],
  "id": 1
}
```

## 💡 Bônus Implementados
- 🔒 Autenticação via JWT com proteção de rotas

- 🧩 Relacionamento many-to-many entre Tools ↔ Tags

- 📑 Swagger com anotações OpenAPI

- 🔁 Migrations + Seeders

- 📦 Estrutura limpa com Controllers, Models e Providers
- 🖼 Bônus: Modelagem Banco de Dados + Desenho funcionamento da Api
---
## 📥 Modelagem Banco de Dados
<img width="1303" height="607" alt="Image" src="https://github.com/user-attachments/assets/92328e1b-a8fd-41d6-a4b4-e8b59efae86c" />

> [Database Model Link](https://dbdiagram.io/d/femaqua-68645470f413ba3508cb6c38)
---

## 💻 Desenho do Funcionamento da API
<img width="1536" height="1024" alt="Image" src="https://github.com/user-attachments/assets/ac3050f0-187b-45a4-830f-8d907531194c" />

---
## 🧠 Autor

#### Desenvolvido com 💜 por Andressa Silva 
