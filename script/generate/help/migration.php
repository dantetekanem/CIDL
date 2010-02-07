Como usar o comando <strong>migration</strong>:

Exemplo básico: script/generate.php/migration/create_table:pessoas/nome:string/idade:integer/historico:text

O exemplo acima gera uma migration que vai criar a tabela pessoas, e adicionar 3 campos a ela: nome, idade e histórico.

Os parâmetros do migration são:
- 1. <b>{ação}:{paramêtro}</b>
- *args <strong>nome do campo : tipo de campo</strong> (string/integer/enum/text)

As ações permitidas são:
- create_table => Cria uma nova tabela no campo
	[migration/create_table:pessoas/nome:string/idade:integer]
- add_column => Adiciona uma coluna a tabela
	[migration/add_column:pessoas/historico:text]