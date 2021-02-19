<?php

namespace Concrete\Package\MinifyHtml\Controller\SinglePage\Dashboard\System\Optimization;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\PageList;
use Concrete\Core\Routing\Redirect;
use Exception;

final class MinifyHtml extends DashboardPageController
{
    public function view()
	{
	    /** @var Repository $config */
	    $config = $this->app->make(Repository::class);

	    $this->set('pagesWithMinificationDisabled', $this->getPagesWithMinificationDisabled());
	    $this->set('status', $config->get('minify_html.settings.status'));
	    $this->set('enableForRegisteredUsers', $config->get('minify_html.settings.enable_for_registered_users'));
	}
	
	public function save() 
	{
		if (!$this->app->make('token')->validate('minify_html.settings')) {
            $this->error->add($this->app->make('token')->getErrorMessage());

            return;
        }

        /** @var Repository $config */
	    $config = $this->app->make(Repository::class);

        $config->save('minify_html.settings.status', (bool) $this->post('status'));
        $config->save('minify_html.settings.enable_for_registered_users', (bool) $this->post('enableForRegisteredUsers'));

        $this->flash('success', t('Settings saved'));

        return Redirect::to($this->action('view'));
	}

	/**
	 * @return \Concrete\Core\Page\Page[]
	 */
	private function getPagesWithMinificationDisabled()
	{
	    try {
            $pl = new PageList();
            $pl->filterByAttribute('disable_html_minification', 1);
            return $pl->getResults();
        } catch (Exception $e) { }

        return [];
	}
}