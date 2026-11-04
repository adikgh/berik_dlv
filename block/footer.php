	<!-- body end -->
	</div>

	<!-- main js -->
	<? foreach (array_merge($sjs, $js) as $i): ?> <script src="<?=fun::asset('js/'.$i.'.js')?>"></script> <? endforeach ?>
	<? if (!$dist || !empty($asset_shared)): ?><script src="<?=fun::asset('js/berik.js')?>"></script><? endif ?>
		
</body>
</html>

	<?php include "modal.php"; ?>