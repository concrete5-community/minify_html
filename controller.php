<?php

namespace Concrete\Package\MinifyHtml;

use A3020\MinifyHtml\Listener\PageOutput;
use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Package\Package;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class Controller extends Package
{
    protected $pkgHandle = 'minify_html';
    protected $appVersionRequired = '8.4.0';
    protected $pkgVersion = '2.0.0';
    protected $pkgAutoloaderRegistries = [
        'src/MinifyHtml' => '\A3020\MinifyHtml',
    ];

    public function getPackageName()
    {
        return t('Minify HTML');
    }

    public function getPackageDescription()
    {
        return t('Minify HTML output to decrease page load times');
    }

    public function on_start()
    {
        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = $this->app->make(EventDispatcherInterface::class);
        $dispatcher->addListener('on_page_output', function($event) {
            /** @var PageOutput $listener */
            $listener = $this->app->make(PageOutput::class);
            $listener->handle($event);
        });
    }

    public function install()
    {
        $pkg = parent::install();

        $this->installPages($pkg);
        $this->installAttributes($pkg);
        $this->enable();
    }


    /**
     * @param Package $pkg
     *
     * @return void
     */
    protected function installPages($pkg)
    {
        foreach ([
            '/dashboard/system/optimization/minify_html' => 'Minify HTML',
        ] as $path => $name) {
            $page = Page::getByPath($path);
            if (!$page || $page->isError()) {
                $singlePage = Single::add($path, $pkg);
                $singlePage->update([
                    'cName' => $name,
                ]);
            }
        }
    }

    /**
     * @param Package $pkg
     *
     * @return void
     */
    private function installAttributes($pkg)
    {
        $handle = 'disable_html_minification';
        if (is_object(CollectionKey::getByHandle($handle))) {
            return;
        }

        /** @var PageCategory $category */
        $category = $this->app->make(PageCategory::class);

        $category
            ->add('boolean',
            [
                'akHandle' => $handle,
                'akName' => t('Disable HTML minification'),
                'akIsSearchable' => false,
                'akCheckedByDefault' => true,
            ], $pkg
        );
    }

     /**
      * Enable the add-on.
      *
      * Minify HTML can be disabled via Dashboard / Systems & Settings / Optimization / Minify HTML.
      */
    private function enable()
    {
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        $config->save('minify_html.settings.status', true);
    }
}
