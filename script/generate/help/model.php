Como usar o comando <strong>model</strong>:

Exemplo b�sico: script/generate.php/model/pessoas/nome:string/idade:integer

Ir� gerar uma migration para a tabela pessoas, e o modelo Pessoa (em singular, sempre) para a pasta models com os campos passados como validations possiveis.

Os par�metros do model s�o:
- 1. <strong>nome do modelo</strong> (singular ou plural)
- *args. <strong>nome do campo : tipo de campo</strong> (string/integer/enum/text)