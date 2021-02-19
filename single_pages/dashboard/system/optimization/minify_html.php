<?php   
defined('C5_EXECUTE') or die('Access Denied.');

$token = Core::make('token');
?>

<form method="post" action="<?php   echo $controller->action('save') ?>">
	<?php   $token->output('minify_html.settings'); ?>

	<div class="form-group">
        <?php echo $form->checkbox('status', 1, Config::get('minify_html.settings.status')) ?>
        <?php echo $form->label('status', t('Enable HTML minification'))?>
	</div>

    <div class="form-group">
        <?php echo $form->checkbox('enable_for_registered_users', 1, Config::get('minify_html.settings.enable_for_registered_users')) ?>
        <?php echo $form->label('enable_for_registered_users', t('Enable for Registered Users'))?>
    </div>
    
    <?php 
    if (count($pages_with_minification_disabled) > 0) {
        ?>
        <hr />
        <strong><?php  echo t("Pages with minification disabled") ?></strong>
        <ul>
            <?php 
            foreach ($pages_with_minification_disabled as $page) {
                echo '<li><a href="' . $page->getCollectionLink() . '">' . $page->getCollectionName() . '</a>';
            }
            ?>
        </ul>
        <?php 
    }
    ?>
	
	<div class="ccm-dashboard-form-actions-wrapper">
		<div class="ccm-dashboard-form-actions">
			<button class="pull-right btn btn-primary" type="submit"><?php   echo t('Save') ?></button>
		</div>
	</div>
</form>
