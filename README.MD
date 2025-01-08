
## Ferramentas necessárias para rodar o projeto

- **PHP 8.x**
- **Connect Com Banco**
- **Apach2**

#### Endpoint:
- **URL**: 'http://public-api.sulnet.net.br:9191/api/XXX'
- **Método HTTP**: GET ou POST (checar)
- **Requer Autenticação**: SIM - 'Headers HTTP: Authorization'
- **Descrição**: Token deve ser liberado conforme API



## API -> api_sorteador_listar_clientes.php

- Tem como objetivo listar todos os clientes Ativos em um determinado periodo e seu respectivo HASH para ser utilizado no Código do Sorteador (Roleta da SULNET)
- Essa API tem como obrigatório o preenchimento via GET dos Parametros PI (Periodo Inicial e Periodo Final).
Exemplo:    PI Value: 2024-10-01
            PF Value: 2024-10-31

#### `GET /api_sorteador_listar_clientes.php`##

##### Exemplo de Resposta:
```json
{
	"cliente": [
		{
			"documento": "025.512.650-61",
			"cliente": "ELIAS K",
			"hash": "http:\/\/sulnet.net.br\/sorteador\/index.html?hash=37889fa0c7dc5aa05bxxcvxcvxcve28152248",
			"contrato": 111152248,
			"descricao_plano": "SULNET RESID FIBRA - 400MB - 1-23",
			"ativacao": "2024-10-12",
			"status": "Ativo",
			"documento_ganhador": null,
			"contrato_ganhador": null,
			"nome_ganhador": null,
			"id_premio": null,
			"premio": null
		}
}
```


## API -> api_sorteador.php

- Tem como objetivo validar se o HASH informado pertence a um cliente com contrato ativo/suspenso. Caso não pertencer resposta será não encontrado. Caso localizado a resposta será com alguns dados do cliente.
- Essa API tem como obrigatório o preenchimento via GET dos Parametro HASH.
Exemplo:    hash: hdfousgdoufguogduif12312

#### `GET /api_sorteador.php`##

##### Exemplo de Resposta para HASH Válido:
```json
{
	"status": 200,
	"hash_status": 1,
	"cliente": [
		{
			"cpf_cnpj": "845822xxxx",
			"nome": "DAVID SEVERO NETO",
			"hash": "8dfacf39b569efsdfs1cf8faeb164fxxx835",
			"contrato": xxx835
		}
	]
}
```

##### Exemplo de Resposta para HASH não Válido:
```json
{
	"status": 200,
	"hash_status": 0
}
```

## API -> api_mail_queue_generator.php

- Tem como Objetivo receber via parametro .
- Essa API tem como obrigatório o preenchimento via GET dos Parametos a seguir:
	- assunto
	- mensagem
	- destinatario

#### `GET /api_mail_queue_generator.php`##

##### Exemplo de Resposta para Email enfileirado com sucesso
```json
{
	"status": 200,
	"mail_queued": true
}
```

##### Exemplo de Resposta para Email não enfileirado
```json
{
	"status": 200,
	"mail_queued": false
}
```
