# saferoute

API PHP + MySQL para autenticação e gestão de eventos acadêmicos.

## Endpoints

### POST /auth

Body JSON:

```json
{
	"email": "String",
	"senha": "String",
	"acao": "login"
}
```

Regras:

- `login`: autentica se existir e senha estiver correta; se o e-mail nao existir, cria automaticamente.
- nao existe acao separada de cadastro; o primeiro login cria a conta.

Resposta de sucesso (200):

```json
{
	"status": "sucesso",
	"mensagem": "Login realizado com sucesso.",
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
	"mensagem": "Evento salvo com sucesso.",
	"evento_id": 45
}
```

### POST /editar_evento

Body JSON:

```json
{
	"evento_id": 45,
	"usuario_id": 12,
	"nome_disciplina": "Estrutura de Dados",
	"descricao_atividade": "Prova remarcada para sexta",
	"data_entrega": "2026-06-03"
}
```

Compatibilidade:

- aceita `evento_id` ou `id_evento`
- aceita `usuario_id` ou `id_usuario`

Resposta de sucesso (200):

```json
{
	"status": "sucesso",
	"mensagem": "Evento atualizado com sucesso.",
	"evento_id": 45
}
```

### POST /excluir_evento

Body JSON:

```json
{
	"evento_id": 45,
	"usuario_id": 12
}
```

Compatibilidade:

- aceita `evento_id` ou `id_evento`
- aceita `usuario_id` ou `id_usuario`

Resposta de sucesso (200):

```json
{
	"status": "sucesso",
	"mensagem": "Evento excluido com sucesso.",
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
- `requisicoes_sucesso` para auditoria de chamadas bem-sucedidas aos endpoints PHP

Cada linha da tabela `requisicoes_sucesso` guarda um JSON na coluna `registro_json` com este formato:

```json
{
	"metodo": "POST",
	"endpoint": "/salvar_evento",
	"status_code": 201,
	"ip": "127.0.0.1",
	"query_params": [],
	"body": {
		"usuario_id": 12,
		"nome_disciplina": "Estrutura de Dados",
		"descricao_atividade": "Prova com valor de 2 pontos",
		"data_entrega": "2026-06-01"
	},
	"resposta": {
		"status": "sucesso",
		"mensagem": "Evento salvo com sucesso.",
		"evento_id": 45
	},
	"registrado_em": "2026-05-31T12:00:00+00:00"
}
```

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