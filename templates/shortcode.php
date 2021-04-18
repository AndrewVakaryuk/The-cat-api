<?php
/** @var array $args */

if ( isset( $args['breeds'] ) && is_array( $args['breeds'] ) ) {
	$script = AV_TCA_PLUGIN_URL . "/assets/shortcode.js";
	?>
    <style>

        .av-the-cat-api-breed-loader
        {
            width: 28px;
            height: 4px;
            background: url('<?=AV_TCA_PLUGIN_URL."/assets";?>/loading.gif') no-repeat center center;
            background-size: contain;
        }
    </style>
    <script>
        var thecat_ajaxurl = '<?= admin_url( 'admin-ajax.php' );?>';
        var thecat_nonce = '<?= wp_create_nonce( 'thecatapi' );?>';
    </script>
    <script src="<?= $script; ?>" type="application/javascript"></script>
    <select name="the_cat_breeds" id="the_cat_breeds">
		<?php
		foreach ( $args['breeds'] as $breed ) {
			?>
            <option value="<?= $breed['id']; ?>"><?= $breed['name']; ?></option>
			<?php
		}
		?>
    </select><br><br>
    <div id="av-the-cat-api-breed"></div>
	<?php
}
