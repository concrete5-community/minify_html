<?php 

namespace A3020\MinifyHtml\Listener;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\User;
use A3020\MinifyHtml\HtmlMin;

final class PageOutput implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @param \Symfony\Component\EventDispatcher\GenericEvent $event
     *
     * @return void
     */
    public function handle($event)
    {
        $contents = $event->getArgument('contents');

        if ($this->shouldMinify($contents)) {
            $contents = $this->minify($contents);
            $event->setArgument('contents', $contents);
        }
    }

    /**
     * Should we minimize the HTML for this page?
     *
     * @param string $contents
     *
     * @return bool
     */
    protected function shouldMinify($contents)
    {
        /** @var Page $page */
        $page = Page::getCurrentPage();

        if (!$page or $page->isError()) {
            return false;
        }

        /**
         * Minify HTML is disabled for dashboard pages.
         * The minification could break things, e.g. it conflicts with JavaScript templates.
         */
        if ($page->isAdminArea()) {
            return false;
        }

        /** @var Repository $config */
        $config = $this->app->make(Repository::class);

        // Check if Minify HTML is disabled globally.
        if ($config->get('minify_html.settings.status') === false) {
            return false;
        }

        $u = new User();

        if ($u->isRegistered()) {
            // Should we minify for logged in users?
            if ($config->get('minify_html.settings.enable_for_registered_users') === false) {
                return false;
            }

            $p = new Checker($page);
            if ($p->canEditPageContents()) {
                return false;
            }
        }

        // Is minification disabled for the current page?
        if ($page->getAttribute('disable_html_minification')) {
            return false;
        }

        return true;
    }

    /**
     * Do the actual minification.
     *
     * @link credits for https://github.com/searchturbine/phpwee-php-minifier
     * @param string $contents
     *
     * @return string
     */
    private function minify($contents)
    {
        // Turn JS and CSS minification off
        return HtmlMin::minify($contents, false, false);
    }
}
