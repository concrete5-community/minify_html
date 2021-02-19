<?php 
namespace Concrete\Package\MinifyHtml;

use Config;
use BlockType;
use Page;
use Package;
use SinglePage;
use Concrete\Core\Support\Facade\Events;
use Concrete\Core\Attribute\Key\CollectionKey as CollectionAttributeKey;
use Concrete\Core\Attribute\Type as AttributeType;
use Concrete\Package\MinifyHtml\Src\MinifyHtml\Controller as MinifyController;

class Controller extends Package
{
    protected $pkgHandle = 'minify_html';
    protected $appVersionRequired = '5.7.4';
    protected $pkgVersion = '0.9.2';

    protected $single_pages = array(
        '/dashboard/system/optimization/minify_html' => array(
            'cName' => 'Minify HTML'
        )
    );

    public function getPackageName()
    {
        return t("Minify HTML");
    }

    public function getPackageDescription()
    {
        return t("Minify HTML output to decrease page load times");
    }

    public function on_start()
    {
        $controller = new MinifyController();
        Events::addListener('on_page_output', array($controller, "boot"));
    }

    public function install()
    {
        $pkg = parent::install();

        $this->installPages($pkg);

        // Install CheckBox Attribute "disable_html_minification"
        $bool = AttributeType::getByHandle('boolean');
        $handle = "disable_html_minification";
        $ak = CollectionAttributeKey::getByHandle($handle);
        if (!is_object($ak)) {
            CollectionAttributeKey::add($bool,
                array('akHandle' => $handle,
                    'akName' => t('Disable HTML minification'),
                    'akIsSearchable' => false,
                    'akCheckedByDefault' => true,
                ), $pkg);
        }

        /**
         * Enable the add-on.
         * Can be disabled via Dashboard / Systems & Settings / Optimization / Minify HTML.
         */
        Config::save('minify_html.settings.status', true);
    }


    /**
     * @param Package $pkg
     * @return void
     */
    protected function installPages($pkg)
    {
        foreach ($this->single_pages as $path => $value) {
            if (!is_array($value)) {
                $path = $value;
                $value = array();
            }
            $page = Page::getByPath($path);
            if (!$page || $page->isError()) {
                $single_page = SinglePage::add($path, $pkg);

                if ($value) {
                    $single_page->update($value);
                }
            }
        }
    }
}