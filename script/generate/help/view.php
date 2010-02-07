Como usar o comando <strong>view</strong>:

Exemplo básico: script/generate.php/view/pessoas:todas/pessoas:unica/header/footer

O exemplo acima irá gerar 1 diretório e 4 arquivos:

1. system/application/views/pessoas/todas.php
2. system/application/views/pessoas/unica.php
3. system/application/views/header.php
4. system/application/views/footer.php

Observe o caractere ":" (dois pontos), ele significa que a view vai ter subdiretórios, não importa quantos, vários serão criados (caso não existam).
A view gerada irá conter a data de criação e o nome do arquivo todo, você pode altera-la a vontade.