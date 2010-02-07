Como usar o comando <strong>migration</strong>:

Exemplo b�sico: script/generate.php/migration/create_table:pessoas/nome:string/idade:integer/historico:text

O exemplo acima gera uma migration que vai criar a tabela pessoas, e adicionar 3 campos a ela: nome, idade e hist�rico.

Os par�metros do migration s�o:
- 1. <b>{a��o}:{param�tro}</b>
- *args <strong>nome do campo : tipo de campo</strong> (string/integer/enum/text)

As a��es permitidas s�o:
- create_table => Cria uma nova tabela no campo
	[migration/create_table:pessoas/nome:string/idade:integer]
- add_column => Adiciona uma coluna a tabela
	[migration/add_column:pessoas/historico:text]