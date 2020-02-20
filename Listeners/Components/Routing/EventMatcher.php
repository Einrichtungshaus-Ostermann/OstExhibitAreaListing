<?php declare(strict_types=1);

/**
 * Einrichtungshaus Ostermann GmbH & Co. KG - Exhibit Area Listing
 *
 * @package   OstExhibitAreaListing
 *
 * @author    Tim Windelschmidt <tim.windelschmidt@fionera.de>
 * @copyright 2020 Einrichtungshaus Ostermann GmbH & Co. KG
 * @license   proprietary
 */

namespace OstExhibitAreaListing\Listeners\Components\Routing;

use Enlight_Event_EventArgs as EventArgs;

class EventMatcher
{
    /**
     * ...
     *
     * @var array
     */
    protected $configuration;

    /**
     * ...
     *
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        // set params
        $this->configuration = $configuration;
    }

    /**
     * ...
     *
     * @param EventArgs $arguments
     *
     * @return mixed
     */
    public function changeRoute(EventArgs $arguments)
    {
        // get the current path
        $path = $arguments->get('request')->getPathInfo();

        $parts = explode('/', ltrim($path, '/'));

        if (empty($this->configuration['seoUrl'])) {
            return null;
        }

        if (count($parts) !== 2 || $parts[0] !== $this->configuration['seoUrl']) {
            return null;
        }

        return [
            'controller' => 'OstExhibitAreaListing',
            'action'     => 'index',
            'koje' => $parts[1],
        ];
    }
}
