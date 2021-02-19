<?php 
namespace Concrete\Package\MinifyHtml\Src\MinifyHtml;

use Config;
use Page;
use Permissions;
use User;

class Controller
{
    /**
     * @param \Symfony\Component\EventDispatcher\GenericEvent $event
     * @return void
     */
    public function boot($event)
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
     * @return bool
     */
    protected function shouldMinify($contents)
    {
        $c = Page::getCurrentPage();
        $u = new User();

        if (!$c or $c->isError()) {
            return false;
        }


        /**
         * Minify HTML is disabled for this website.
         */
        if (!Config::get('minify_html.settings.status')) {
            return false;
        }


        /**
         * Minify HTML is disabled for dashboard pages.
         * The minification could break things, e.g. it conflicts with JavaScript templates.
         */
        if ($c->isAdminArea()) {
            return false;
        }


        /**
         * Minify HTML is disabled for registered users who can edit a page.
         */
        if ($u->isRegistered()) {
            if (!Config::get('minify_html.settings.enable_for_registered_users')) {
                return false;
            }

            $p = new Permissions($c);
            if ($p->canEditPageContents()) {
                return false;
            }
        }



        /**
         * Minify HTML is disabled for this specific page.
         */
        if ($c->getAttribute('disable_html_minification')) {
            return false;
        }


        return true;
    }

    /**
     * Do the actual minification.
     *
     * @link credits for https://github.com/searchturbine/phpwee-php-minifier
     * @param string $contents
     * @return string
     */
    protected function minify($contents)
    {
        // Turn JS and CSS minification off
        return HtmlMin::minify($contents, false, false);
    }
}