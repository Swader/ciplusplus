<div id="container">
	<div id="body" class="hero-unit">
		<p>The value fetched from the DB was: <span style="color:red"><?php echo $databaseData; ?></span>! Now try going back and clicking the other
            link, or simply altering the number in the URL to another value.</p>
        <p>Please don't be alarmed - this part is ugly on purpose!</p>
		<a href="/" title="Back home">Go back home.</a>
        <br />
        <p>Here is another fragment demo. Read the blog post for more info on fragments.</p>
        <?php $this->renderLayoutFragment('fragmentsample'); ?>
    </div>
</div>

