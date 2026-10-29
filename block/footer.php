	<!-- body end -->
	</div>

	<!-- main js -->
	<? foreach ($sjs as $i): ?> <script src="/assets/js/<?=$i?>.js?v=<?=$ver?>"></script> <? endforeach ?>
	<? foreach ($js as $i): ?> <script src="/assets/js/<?=$i?>.js?v=<?=$ver?>"></script> <? endforeach ?>
	<script src="/assets/js/berik.js?v=<?=$ver?>-<?=@filemtime($_SERVER['DOCUMENT_ROOT'].'/assets/js/berik.js')?>"></script>
		
</body>
</html>

	<?php include "modal.php"; ?>