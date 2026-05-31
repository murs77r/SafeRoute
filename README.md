# saferoute

API PHP + MySQL para autenticação e gestão de eventos acadêmicos.

## Endpoints

### POST /auth

Body JSON:

```json
{
	"email": "String",
	"senha": "String",
	"acao": "login | cadastro"
}
```

Regras:

- `cadastro`: cria conta se o e-mail nao existir; se existir, retorna erro.
- `login`: autentica se existir e senha estiver correta; se o e-mail nao existir, cria automaticamente.

Resposta de sucesso (200):

```json
{
	"status": "sucesso",
	"mensagem": "Autenticacao realizada com sucesso",
	"usuario": {
		"id": 12,
		"email": "pessoal@email.com"
	}
}
```

### POST /salvar_evento

Body JSON:

```json
{
	"usuario_id": 12,
	"nome_disciplina": "Estrutura de Dados",
	"descricao_atividade": "Prova com valor de 2 pontos",
	"data_entrega": "2026-06-01"
}
```

Resposta de sucesso (201):

```json
{
	"status": "sucesso",
	"mensagem": "Evento cadastrado com sucesso.",
	"evento_id": 45
}
```

### GET /listar_eventos

Query params:

- `usuario_id` (obrigatorio)
- `limit` (opcional)

Exemplo:

- `/listar_eventos?usuario_id=12&limit=3`
- `/listar_eventos?usuario_id=12`

Resposta de sucesso (200):

```json
{
	"status": "sucesso",
	"eventos": [
		{
			"id": 45,
			"nome_disciplina": "Estrutura de Dados",
			"descricao_atividade": "Prova com valor de 2 pontos",
			"data_entrega": "2026-06-01"
		}
	]
}
```

## Banco de dados

Execute o script `banco.sql` para criar as tabelas:

- `usuarios`
- `eventos` com relacao 1:N (`eventos.usuario_id -> usuarios.id`)

Configuracao opcional por variaveis de ambiente (somente banco):

- `DB_HOST` (padrao: `localhost`)
- `DB_PORT` (padrao: `3306`)
- `DB_USER` (padrao: `root`)
- `DB_PASSWORD` (padrao: vazio)
- `DB_NAME` (padrao: `saferoute`)

## Docker

Build:

```bash
docker build -t saferoute-php .
```

Run local:

```bash
docker run --rm -p 8080:80 saferoute-php
```