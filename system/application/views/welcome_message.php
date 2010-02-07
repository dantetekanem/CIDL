<?= base_tag() ?>
<?= stylesheet_link_tag('base') ?>
<h2>Posts cadastrados: </h2>

<? foreach($posts as $post): ?>
	<p>
		<?= $post->title ?> - criado em: <?= date("d/m/Y - H:i:s", $post->created_at) ?> por <?= $post->user->name ?>
	</p>
<? endforeach ?>

<br /><br /><br />

<? foreach($users as $user): ?>
	<p>
		<b><?= $user->name ?></b>
		<br />
		Posts: <b><?= $user->post->count() ?></b><br />
		<img src="uploads/avatars/normal/<?= $user->avatar ?>" />
	</p>
<? endforeach ?>