Como usar o comando <strong>view</strong>:

Exemplo b�sico: script/generate.php/view/pessoas:todas/pessoas:unica/header/footer

O exemplo acima ir� gerar 1 diret�rio e 4 arquivos:

1. system/application/views/pessoas/todas.php
2. system/application/views/pessoas/unica.php
3. system/application/views/header.php
4. system/application/views/footer.php

Observe o caractere ":" (dois pontos), ele significa que a view vai ter subdiret�rios, n�o importa quantos, v�rios ser�o criados (caso n�o existam).
A view gerada ir� conter a data de cria��o e o nome do arquivo todo, voc� pode altera-la a vontade.