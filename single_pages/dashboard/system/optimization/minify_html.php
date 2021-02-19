<?php

defined('C5_EXECUTE') or die('Access Denied.');

/** @var \Concrete\Core\Validation\CSRF\Token $token */
/** @var bool $status */
/** @var bool $enableForRegisteredUsers */
/** @var \Concrete\Core\Page\Page[] $pagesWithMinificationDisabled */
?>

<form method="post" action="<?php echo $this->action('save') ?>">
	<?php $token->output('minify_html.settings'); ?>

	<div class="form-group">
        <?php echo $form->checkbox('status', 1, $status); ?>
        <?php echo $form->label('status', t('Enable HTML minification')); ?>
	</div>

    <div class="form-group">
        <?php echo $form->checkbox('enableForRegisteredUsers', 1, $enableForRegisteredUsers); ?>
        <?php echo $form->label('enableForRegisteredUsers', t('Enable for Registered Users')); ?>
    </div>
    
    <?php 
    if (count($pagesWithMinificationDisabled) > 0) {
        ?>
        <hr />
        <strong><?php  echo t('Pages with minification disabled') ?></strong>
        <ul>
            <?php 
            foreach ($pagesWithMinificationDisabled as $page) {
                echo '<li><a href="' . $page->getCollectionLink() . '">';
                echo h($page->getCollectionName());
                echo '</a></li>';
            }
            ?>
        </ul>
        <?php 
    }
    ?>
	
	<div class="ccm-dashboard-form-actions-wrapper">
		<div class="ccm-dashboard-form-actions">
			<button class="pull-right btn btn-primary" type="submit">
                <?php echo t('Save') ?>
            </button>
		</div>
	</div>
</form>
