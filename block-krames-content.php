<?php
/**
 * this template goes here wp-content/themes/genesis-sample/blocks/block-krames-content.php
 *
 * create Genesis custom content block named Krames content
 *
 * create a text editor field named "search"
 *
 */

<div class="krames-content">
       <?php 
	$search = block_field( 'search', false );
	fc_krames_content($search);	?>
</div>
