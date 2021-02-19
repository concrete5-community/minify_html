<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Url;
?>
<p><?php echo t('Congratulations, the add-on has been installed!'); ?></p>
<br>

<a class="btn btn-primary" href="<?php echo Url::to('/dashboard/system/optimization/minify_html') ?>">
    <?php
    echo t('Configure %s', t('Minify HTML'));
    ?>
</a>
