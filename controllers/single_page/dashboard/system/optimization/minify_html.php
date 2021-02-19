<?php   
namespace Concrete\Package\MinifyHtml\Controller\SinglePage\Dashboard\System\Optimization;

use Core;
use Config;
use PageList;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Attribute\Key\CollectionKey as CollectionAttributeKey;

class MinifyHtml extends DashboardPageController
{
	public function on_start()
	{
		$this->error = Core::make('helper/validation/error');

		$this->set('pages_with_minification_disabled', $this->getPagesWithMinificationDisabled());
	}

	
	public function save() 
	{
		if (Core::make('token')->validate('minify_html.settings') == false) {
            $this->error->add(Core::make('token')->getErrorMessage());
            return;
        }

        Config::save('minify_html.settings.status', (bool) $this->post('status'));
        Config::save('minify_html.settings.enable_for_registered_users', (bool) $this->post('enable_for_registered_users'));
		
		$this->redirect($this->action('save_success'));
	}


    public function save_success()
	{
		$this->set('message', t('Settings saved'));
	}


	/**
	 * @return array
	 */
	public function getPagesWithMinificationDisabled()
	{
		$ak_handle = "disable_html_minification";
		$ak = CollectionAttributeKey::getByHandle($ak_handle);
		if (!is_object($ak)) {
			return array();
		}

		$pl = new PageList();
		$pl->filterByAttribute($ak_handle, 1);
		return $pl->getResults();
	}
}