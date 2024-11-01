
## Ferramentas necessárias para rodar o projeto

- **PHP 8.x**
- **Connect Com Banco**
- **Apach2**

#### Endpoint:
- **URL**: '/api/xxx'
- **Método HTTP**: GET
- **Requer Autenticação**: SIM - 'Headers HTTT Authorization'
- **Descrição**: Recebe dados de medição de energia e os insere no banco de dados após realizar validações.



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
