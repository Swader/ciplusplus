<div id="container">
	<h1>Welcome to the demo of CI++!</h1>

	<div id="body">
		<p>Hello <?php echo $databaseData; ?>!</p>

		<?php if ((int)$id == 1) : ?>
		<a href="/welcome/index/id/2" title="Testing alternative Hello">Now try this link.</a>
		<?php elseif ((int)$id > 1) : ?>
		<a href="/" title="Back home">Go back home.</a>
		<?php endif; ?>
        <br />
        <a href="/welcome/bootstrapdemo" title="See the bootstrap demo">Bootstrap demo</a>
        <?php $this->renderLayoutFragment('fragmentsample'); ?>
    </div>


	<p class="footer"><a href="http://www.bitfalls.com" title="My blog">Visit my blog for more information</a></p>
</div>
