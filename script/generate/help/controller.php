Como usar o comando <strong>controller</strong>:

Exemplo b�sico: script/generate.php/controller/pessoas/index/show/add/edit

O exemplo acima ir� gerar o controller pessoas.php em system/application/controllers/ com 4 fun��es:

index()
show()
add()
edit()

para cada fun��o, ser� gerada uma view especifica, em:

index() => system/application/views/pessoas/index.php
show() => system/application/views/pessoas/show.php
add() => system/application/views/pessoas/add.php
edit() => system/application/views/pessoas/edit.php

Detalhe: dentro de cada fun��o, vai ter o seguinte c�digo: $this -> load -> view(nome_da_view);
Para efetuar o load da view.


<strong>Gerando controllers em sub-pastas</strong>

Exemplo: script/generate.php/controller/admin:pessoas/index/show/add/edit

O exemplo acima ir� gerar o controller pessoas.php em system/application/controllers/admin/ e suas 4 fun��es com suas respectivas views.
Detalhe para as views que tamb�m ficam nos subdiret�rios:

index() => system/application/views/admin/pessoas/index.php
show() => system/application/views/admin/pessoas/show.php
add() => system/application/views/admin/pessoas/add.php
edit() => system/application/views/admin/pessoas/edit.php